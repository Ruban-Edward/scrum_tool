<?php
namespace Tests\app\Models;
use CodeIgniter\Test\CIUnitTestCase;

class IssueModelTest extends CIUnitTestCase
{
    protected $model;

    protected function setUp(): void
    {
        parent::setUp();
        // Initialize the model
        $this->model = service('issues');
    }

    //test to get all tasks from redmine 
    public function testGetAllTasksFromRedmine()
    {
        $priority = [1, 2, 3];
        $customfieldId = 52;
        $lastupdate = "2024-08-04 12:00:00";
        $result = $this->model->getAllTasksFromRedmine($priority, $customfieldId, $lastupdate);
        $this->assertIsArray($result);
          

    }

    //testing whether the data comming is correct or not

    public function testTaskDataIsCorrectOrNot(){
        $priority=[1,2,3];
        $customfieldId=52;
        $lastupdate = "2024-08-04 12:00:00";
        $result=$this->model->getAllTasksFromRedmine($priority,$customfieldId,$lastupdate);
        $this->assertArrayHasKey("tracker_id",$result[0]);
        $this->assertContains("task1",$result[8]);
        
    }

    


}