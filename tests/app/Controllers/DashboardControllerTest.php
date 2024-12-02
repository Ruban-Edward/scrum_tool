<?php

namespace App\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\URI;
use CodeIgniter\HTTP\UserAgent;
use Config\App;

class DashboardControllerTest extends CIUnitTestCase
{
    use ControllerTestTrait;
    protected $dashboardModelMock;
    protected $backlogModelMock;
    protected $sprintModelMock;
    protected $redmineModelMock;
    protected $reportModelMock;
    protected $configMock;
    protected $productIdMock;
    protected $userIdMock;
    protected $productsMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dashboardModelMock = $this->createMock(\App\Models\Dashboard\DashboardModel::class);
        $this->backlogModelMock = $this->createMock(\App\Models\Backlog\BacklogModel::class);
        $this->sprintModelMock = $this->createMock(\App\Models\SprintModel::class);
        $this->redmineModelMock = $this->createMock(\Redmine\Services\IssuesService::class);
        $this->reportModelMock = $this->createMock(\App\Models\CustomReportModel::class);
        $this->configMock = $this->createMock(\Config\SprintModelConfig::class);
        $this->mockSession();
        $_SESSION['employee_id'] = 31;
        $this->productsMock = [
                                ["product_id" => 5, "product_name"=> "AgenyAuto"], 
                                ["product_id"=> 6,"product_name"=> "AgencyDirct"]
                            ];
        $this->productIdMock = array_column($this->productsMock,'product_id');
        // Create an instance of the controller
        $this->controller = new \App\Controllers\DashboardController();

        // Inject the mock models into the controller
        $this->setPrivateProperty($this->controller, 'dashboardModel', $this->dashboardModelMock);
        $this->setPrivateProperty($this->controller, 'backlogModel', $this->backlogModelMock);
        $this->setPrivateProperty($this->controller, 'sprintModel', $this->sprintModelMock);
        $this->setPrivateProperty($this->controller, 'reportModel', $this->reportModelMock);
        $this->setPrivateProperty($this->controller, 'redmineModel', $this->redmineModelMock);
        $this->setPrivateProperty($this->controller, 'config', $this->configMock);
        $this->setPrivateProperty($this->controller, 'products', $this->productsMock);
        $this->setPrivateProperty($this->controller, 'productId', $this->productIdMock);
    }

    public function testDashboardView()
    {
        // Set the URI for the request
        $result = $this->withURI('http://localhost:8080/dashboard/dashboardView')
            ->controller(\App\Controllers\DashboardController::class)
            ->execute('dashboardView');
        $this->assertTrue($result->isOK());  
    }

    public function testShowProductDashboard(){
        // Set the URI for the request
        $result = $this->withURI('http://localhost:8080/dashboard/showProductDashboard')
            ->controller(\App\Controllers\DashboardController::class)
            ->execute('showProductDashboard');
        $this->assertTrue($result->isOK()); 
    }

    public function testShowProductDashboardArgument(){
        // Set the URI for the request
        $result = $this->withURI('http://localhost:8080/dashboard/showProductDashboard/5')
            ->controller(\App\Controllers\DashboardController::class)
            ->execute('showProductDashboard');
        $this->assertTrue($result->isOK()); 
        // Checking whether the expected output is present
        $this->assertStringContainsString(5, $result->getBody());
    }

    public function testFetchPendingTaskBySprintId()  {
        // Duplicate the POST data for the test
        $testData = [
            'sprint_id' => 1,
            'product_id' => 5,
        ];
        // creating the POST form Data to send to controller
        $uri = new URI('http://localhost:8080/dashboard/pendingTasks');
        $config = new App();
        $request = new IncomingRequest($config, $uri, null, new UserAgent());
        $request->withMethod('post');
        $request->setGlobal('post', $testData);
        // preparing the POST to send to the function
        $result = $this->withRequest($request)
            ->controller(\App\Controllers\DashboardController::class)
            ->execute('fetchPendingTaskBySprintId');
        $this->assertTrue($result->isOK());  
    }
}