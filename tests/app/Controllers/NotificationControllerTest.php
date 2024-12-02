<?php

namespace App\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use Config\App;

class NotificationControllerTest extends CIUnitTestCase
{
    use ControllerTestTrait;
    protected $dashboardModelMock;
    protected $backlogModelMock;
    protected function setUp(): void
    {
        parent::setUp();
        $this->dashboardModelMock = $this->createMock(\App\Models\Dashboard\DashboardModel::class);
        $this->backlogModelMock = $this->createMock(\App\Models\Backlog\BacklogModel::class);
        $this->mockSession();
        $_SESSION['employee_id'] = 31;
        // Create an instance of the controller
        $this->controller = new \App\Controllers\DashboardController();
        // Inject the mock models into the controller
        $this->setPrivateProperty($this->controller, 'dashboardModel', $this->dashboardModelMock);
        $this->setPrivateProperty($this->controller, 'backlogModel', $this->backlogModelMock);        
    }

    public function testNotificationDetails(){
        // Set the URI for the request
        $result = $this->withURI('http://localhost:8080/notification/notificationDetails')
            ->controller(\App\Controllers\NotificationController::class)
            ->execute('notificationDetails');

        // Assert that the response is OK (200)
        $this->assertTrue($result->isOK());
    }
}