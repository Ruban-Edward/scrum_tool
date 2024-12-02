<?php
namespace Tests\app\Models;

use App\Models\CustomReportModel;
use CodeIgniter\Test\CIUnitTestCase;

class CustomReportModelTest extends CIUnitTestCase
{
    protected $model;

    protected function setUp(): void
    {
        parent::setUp();
        // Initialize the model
        $this->model = new CustomReportModel();
    }

    public function testGetMeetReportView()
    {
        // Call the model method
        $result = $this->model->getMeetReportView();

        // Assertions
        $this->assertIsArray($result, 'Expected result to be an array');
        // $this->assertNotEmpty($result, 'Expected result not to be empty');

        // Additional checks can be done here based on expected structure of $result
    }

    public function testGetFilterMeetReportViewWithParams()
    {
        // Prepare the filter parameters
        $params = [
            
            'product' => ['Training and Development And Meeting']
            
        ];

        // Call the model method
        $result = $this->model->getFilterMeetReportView($params);

        // Assertions
        $this->assertIsArray($result, 'Expected result to be an array');
        $this->assertNotEmpty($result, 'Expected result not to be empty');
        // $this->assertArrayHasKey('Meeting Name', $result[0], 'Expected result to have "Meeting Name" key');
    }

    public function testGetSprintReportView()
    {
        // Call the model method
        $result = $this->model->getSprintReportView();

        // Assertions
        $this->assertIsArray($result, 'Expected result to be an array');
        $this->assertNotEmpty($result, 'Expected result not to be empty');

        // Additional checks can be done here based on expected structure of $result
    }

    public function testGetFilterSprintReportViewWithParams()
    {
        // Prepare the filter parameters
        $params = [
            'product' => ['Wordpress - Projects']
        ];

        // Call the model method
        $result = $this->model->getFilterSprintReportView($params);

        // Assertions
        $this->assertIsArray($result, 'Expected result to be an array');

        $this->assertNotEmpty($result, 'Expected result not to be empty');
        // $this->assertArrayHasKey('Sprint Name', $result[0], 'Expected result to have "Sprint Name" key');
    }

    public function testGetBacklogReportView()
    {
        // Call the model method
        $result = $this->model->getBacklogReportView();

        // Assertions
        $this->assertIsArray($result, 'Expected result to be an array');
        $this->assertNotEmpty($result, 'Expected result not to be empty');

        // Additional checks can be done here based on expected structure of $result
    }

    //testing the backlog view data is correct or not 

    public function testBackLogReportViewDataCorrectOrNOt(){
        $result = $this->model->getBacklogReportView();
        // print_r($result);
        $this->assertArrayHasKey("Backlog Name",$result[0]);
        $this->assertArrayHasKey("Product Name",$result[0]);
        $this->assertContains("Wordpress - Projects",$result[0]);

    }

    //testing the sprint report view is correct or not 
    public function  testSprintReportViewDataCorrectOrNot(){
         // Call the model method
         $result = $this->model->getSprintReportView();
        //  $this->assertArrayHasKey("",$r)
        // print_r($result);
        $this->assertArrayHasKey("Sprint Name",$result[0]);
        $this->assertContains("0.2",$result[0]);

       
    }

    //test to check the data of the meeting data 
    public function testMeetReportViewDataCorrectOrNot(){
        $result=$this->model->getMeetReportView();
        $this->assertArrayHasKey("Meeting Name",$result[0]);
        $this->assertContains("General",$result[16]);
    }

}
