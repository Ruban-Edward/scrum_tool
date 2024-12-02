<?php
/**
 * Author: T. Siva Teja
 * Email: thotasivateja57@gmail.com
 * Date: 8 July 2024
 * Purpose: Controller for syncing Redmine data
 */

namespace App\Controllers;

use Config\SprintModelConfig as Constants;
use App\Controllers\AdminController as MemberSync;

class SyncRedmineController extends BaseController
{
    protected $projects;
    protected $issues;
    protected $sync;
    public $constants;
    protected $memberSync;
    public $customerSync;

    /**
     * Constructor to initialize services and constants
     */
    public function __construct()
    {
        $this->sync = model("SyncRedmineModel");
        $this->projects = service('projects');
        $this->issues = service('issues');
        $this->constants = new Constants();
        $this->memberSync = new MemberSync();
        $this->customerSync = service("customField");
    }

    /**
     * Display the sync settings page with the last updated dates
     *
     * @return mixed
     */
    public function index()
    {
        $dateColumns = $this->getLastUpdatedDates();

        $breadcrumbs = [
            'Home' => ASSERT_PATH . 'dashboard/dashboardView',
            "Redmine Sync" => ''
        ];

        return $this->template_view('RedmineSync/SyncRedmineView', $viewData = $dateColumns, "Sync Settings", $breadcrumbs);
    }

    /**
     * Sync all selected data from Redmine to local database
     *
     * @return mixed
     */
    public function syncAll()
    {
    
        $update = [];
        $employeeId = session()->get('employee_id');

        try {
            // Sync product users
            if ($this->request->getPost('usersync')) {
                $update["members"] = $this->syncProductUsers($employeeId);
            }

            // Sync products
            if ($this->request->getPost('productsync')) {
                $update["product"] = $this->syncProducts($employeeId);
            }

            // Sync tasks
            if ($this->request->getPost('tasksync')) {
                $update["tasks"] = $this->syncTasks($employeeId);
            }

            // Sync members
            if ($this->request->getPost('membersync')) {
                $resultMembers = $this->memberSync->userSync();
                $update["users"] = $this->sync->logSyncActivity('usersync', $resultMembers, $employeeId);
            }

            // Sync customers
            if ($this->request->getPost('customersync')) {
                $type = $this->constants->customers["type"];
                $customer = $this->constants->customers["name"];
                $customerArray = $this->customerSync->getCustomField($type, $customer);
                $update["customers"] = $this->syncCustomers($customerArray, $employeeId);
            }

            return $this->response->setJSON($update);

        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)
                ->setJSON(['error' => $e->getMessage()]);
        }
    }

    /**
     * Sync product users from Redmine to local database
     *
     * @param int $employeeId
     * @return bool
     */
    public function syncProductUsers($employeeId)
    {
        $redmineData = $this->projects->getAllProductUsersFromRedmine();
        $syncResult = $this->sync->updateProductUserSync($redmineData);
        return $this->sync->logSyncActivity('productusersync', $syncResult, $employeeId) && $syncResult;
    }

    /**
     * Sync products from Redmine to local database
     *
     * @param int $employeeId
     * @return bool
     */
    public function syncProducts($employeeId)
    {
        $redmineData = $this->projects->getAllProductsFromRedmine();
        $syncResult = $this->sync->updateProductSync($redmineData);
        
        return $this->sync->logSyncActivity('productsync', $syncResult, $employeeId) && $syncResult;
    }

    /**
     * Sync tasks from Redmine to local database
     *
     * @param int $employeeId
     * @return bool
     */
    public function syncTasks($employeeId)
    {
        $tasksyncDate = $this->getLastUpdatedDates()["tasksync"] ?? "First Sync";
        $resultArray = $this->issues->getAllTasksFromRedmine(
            $this->constants->priorities,
            $this->constants->customFieldId,
            $tasksyncDate
        );
        $syncResult = empty($resultArray) ? true : $this->sync->updateTaskSync($resultArray);
        return $this->sync->logSyncActivity('tasksync', $syncResult, $employeeId) && $syncResult;
    }

    /**
     * Sync customers from Redmine to local database
     *
     * @param array $data
     * @param int $employeeId
     * @return bool
     */
    public function syncCustomers($data, $employeeId)
    {
        $syncResult = $this->sync->updateCustomerSync($data["possible_values"]);
        return $this->sync->logSyncActivity('customersync', $syncResult, $employeeId) && $syncResult;
    }

    /**
     * Retrieve last updated dates for different sync types
     *
     * @return array
     */
    private function getLastUpdatedDates()
    {
        $lastUpdates = $this->sync->getLastUpdates();
        return array_combine(
            array_column($lastUpdates, 'sync_type'),
            array_column($lastUpdates, 'last_updated_datetime')
        );
    }
}
