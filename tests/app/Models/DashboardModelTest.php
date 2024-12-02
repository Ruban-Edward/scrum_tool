<?php

namespace Tests\app\Models;

use App\Models\Dashboard\DashboardModel;
use CodeIgniter\Test\CIUnitTestCase;

class DashboardModelTest extends CIUnitTestCase
{
    protected $model;

    protected function setUp(): void
    {
        parent::setUp();
        // Initialize the model
        $this->model = new DashboardModel();
    }

    public function testGetSprintDetails()
    {
        $testData = [
            "productId"=> 1,
            "sprintStatus"=> [
                'ongoing' => [20, 21, 22],
                'upcoming' => 19,
                'completed' => 23,
            ],
        ];

        $result = $this->model->getSprintDetails($testData);
        $this->assertIsArray($result);
    }

    public function testGetRunningSprintId()
    {
        $testData = [
            'productId' => 5,
            'status' => [20, 21, 22],
        ] ;
        $result = $this->model->getRunningSprintId($testData);
        $this->assertIsArray($result);
    }

    public function testGetBacklogStatusCounts()
    {
        $testData = [
            'productId' => 5,
            'backlogStatus' => [
                'completed_backlogs' => 12,
                'in_progress_backlogs' => [9, 10],
                'on_hold_backlogs' => 11,
                'not_started_backlogs' => [1, 2, 3, 4, 5, 6, 7, 8]
            ],
        ] ;
        $result = $this->model->getBacklogStatusCounts($testData);
        $this->assertIsArray($result);
    }

    public function testGetUserStorystatusCounts()
    {
        $testData = [
            'productId' => 5,
            'userStoryStatus' => 18
        ] ;
        $result = $this->model->getUserStorystatusCounts($testData);
        $this->assertIsArray($result);
    }

    public function testGetSprintTasksId()
    {
        $testData = [
            'productId' => 5,
            'sprintId' => 1
        ] ;
        $result = $this->model->getSprintTasksId($testData);
        $this->assertIsArray($result);
    }

    public function testGetAlluserStoryIds()
    {
        $testData = [
            'productId'=> 5,
            'status' => 23,
        ] ;
        $result = $this->model->getAlluserStoryIds($testData);
        $this->assertIsArray($result);
    }

    public function testGetPendingTaskStatusCounts()
    {
        $testData = [
            'productId'=> 5,
            'sprintId'=> 1,
            'status' => [1, 2, 4, 8, 16]
        ] ;
        $result = $this->model->getPendingTaskStatusCounts($testData);
        $this->assertIsArray($result);
    }

    public function testGetPendingTasks()
    {
        $testData = [
            'sprintId' => 1,
            'productId' => 5,
            'status' => [1, 2, 4, 8, 16]
        ] ;
        $result = $this->model->getPendingTasks($testData);
        $this->assertIsArray($result);
    }

    public function testGetEstimatedSprintHours()
    {
        $testData = [
            'productId' => 5,
            'sprintId' => 1
        ] ;
        $result = $this->model->getEstimatedSprintHours($testData);
        $this->assertIsArray($result);
    }

    public function testUserMeetings()
    {
        $testData = 31 ;
        $result = $this->model->userMeetings($testData);
        $this->assertIsArray($result);
    }

    public function testGetBacklogPriority()
    {
        $testData = [
            'product_id' => [5, 6, 19, 25],
            'module_status_id' => [12]
        ] ;
        $result = $this->model->getBacklogPriority($testData);
        $this->assertIsArray($result);
    }

    public function testGetSprintPerformance()
    {
        $testData = [
            'product_id' => [5, 6, 25],
            'module_status_id' => [20, 21, 22],
        ] ;
        $result = $this->model->getSprintPerformance($testData);
        $this->assertIsArray($result);
    }

    public function testProductOnTrack()
    {
        $testData = [
            'product_id' => [5, 6, 25],
            'module_status_id' => [20, 21, 22],
        ] ;
        $result = $this->model->productOnTrack($testData);
        $this->assertIsArray($result);
    }

    public function testGetAllUpcomingSprints()
    {
        $testData = [
            'sprintPlannedStatus' => [29],
            'sprintStatuses' => 19,
            'product_id' => [5, 6, 25]
        ] ;
        $result = $this->model->getAllUpcomingSprints($testData);
        $this->assertIsArray($result);
    }
}