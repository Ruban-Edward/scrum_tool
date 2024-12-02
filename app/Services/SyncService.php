<?php
/**
 * Author: T. Siva Teja
 * Email: thotasivateja57@gmail.com
 * Date: 8 July 2024
 * Purpose: Controller for syncing Redmine data
 */

namespace App\Services;

use App\Controllers\AdminController as MemberSync;
use Config\SprintModelConfig as Constants;

class SyncService
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
     * Sync product users from Redmine to local database
     *
     * @param int $employeeId
     * @return bool
     */
    public function syncProductUsers()
    {
        $redmineData = $this->projects->getAllProductUsersFromRedmine();
        $syncResult = $this->sync->updateProductUserSync($redmineData);
        return $this->sync->logSyncActivity('productusersync', $syncResult, null) && $syncResult;
    }

    /**
     * Sync products from Redmine to local database
     *
     * @param int $employeeId
     * @return bool
     */
    public function syncProducts()
    {
        $redmineData = $this->projects->getAllProductsFromRedmine();
        $syncResult = $this->sync->updateProductSync($redmineData);

        return $this->sync->logSyncActivity('productsync', $syncResult, null) && $syncResult;
    }

    /**
     * Sync tasks from Redmine to local database
     *
     * @param int $employeeId
     * @return bool
     */
    public function syncTasks()
    {
        $tasksyncDate = $this->getLastUpdatedDates()["tasksync"] ?? "First sync";
        $resultArray = $this->issues->getAllTasksFromRedmine(
            $this->constants->priorities,
            $this->constants->customFieldId,
            $tasksyncDate
        );
        $syncResult = empty($resultArray) ? true : $this->sync->updateTaskSync($resultArray);
        return $this->sync->logSyncActivity('tasksync', $syncResult, null) && $syncResult;
    }

    /**
     * Sync customers from Redmine to local database
     *
     * @param array $data
     * @param int $employeeId
     * @return bool
     */
    public function syncCustomers()
    {
        $type = $this->constants->customers["type"];
        $customer = $this->constants->customers["name"];
        $data = $this->customerSync->getCustomField($type, $customer);
        $syncResult = $this->sync->updateCustomerSync($data["possible_values"]);
        return $this->sync->logSyncActivity('customersync', $syncResult, null) && $syncResult;
    }

    public function syncMembers()
    {
        $resultMembers = $this->memberSync->userSync();
        return $this->sync->logSyncActivity('usersync', $resultMembers, null) && $resultMembers;

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
