<?php
namespace App\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\URI;
use CodeIgniter\HTTP\UserAgent;
use Config\App;
use App\Controllers\CustomReportController;

class CustomReportControllerTest extends CIUnitTestCase
{
    use ControllerTestTrait;

    // Function to test the Backlog Report
    public function testGetBacklogReportTable()
    {
        $this->testReport("http://localhost:8080/report/backlogreport/backlog", "Backlog");
    }

    // Function to test the Sprint Report
    public function testGetSprintReportTable()
    {
        $this->testReport("http://localhost:8080/report/sprintreport/sprint", "Sprint");
    }

    // Function to test the Meet Report
    public function testGetMeetReportTable()
    {
        $this->testReport("http://localhost:8080/report/meetingreport/meet", "Meet");
    }

    // Function to test the filtered Backlog Report with valid data
    public function testGetFilteredBacklogReport()
    {
        $postData = [
            "product" => ["Voyageraid", "QROBO", "DialogFlow", "Scrumproject-1"]
        ];
        $this->testPostFormData($postData, "http://localhost:8080/report/backlogreport/backlog", "BacklogReport");
    }

    // Function to test the filtered Sprint Report with valid data
    public function testGetFilteredSprintReport()
    {
        $postData = [
            "product" => ["Voyageraid", "QROBO", "DialogFlow", "Scrumproject-1"]
        ];
        $this->testPostFormData($postData, "http://localhost:8080/report/sprintreport/sprint", "SprintReport");
    }

    // Function to test the filtered Meet Report with valid data
    public function testGetFilteredMeetReport()
    {
        $postData = [
            "product" => ["Voyageraid", "QROBO", "DialogFlow", "Scrumproject-1"]
        ];
        $this->testPostFormData($postData, "http://localhost:8080/report/MeetReportfilter", "MeetReport");
    }


    // Function to test the filtered Backlog Report with no filters (positive case)
    public function testGetFilteredBacklogReportWithoutFilters()
    {
        $postData=[];
        $uri = new URI("http://localhost:8080/report/Backlogfilter");
        $config = new App();
        $request = new IncomingRequest($config, $uri, null, new UserAgent());
        $request->withMethod('post');
        $request->setGlobal('post', $postData);

        $result = $this->withRequest($request)
            ->controller(CustomReportController::class)
            ->execute('getFilterReport', "BacklogReport");

        // Assertions
        $this->assertTrue($result->isOK());
        $this->assertNotEmpty($result->getBody());
        
    }


    // Function to test the download of filtered data with no data (negative case)
    public function testDownloadFilteredDataWithNoData()
    {
        $postData = [
            "product" => ["NonExistingProduct"]
        ];
        $uri = new URI("http://localhost:8080/report/backlogreport/backlog/download");
        $config = new App();
        $request = new IncomingRequest($config, $uri, null, new UserAgent());
        $request->withMethod('post');
        $request->setGlobal('post', $postData);
        $result = $this->withRequest($request)
            ->controller(CustomReportController::class)
            ->execute('downloadFilterAllData', 'BacklogReport');

        // Assertions
        $this->assertFalse($result->isOK());
        $this->assertStringContainsString('No data to export', $result->getBody());

    }

    // Common function to test a report type
    protected function testReport($url, $type)
    {
        $result = $this->withURI($url)
            ->controller(CustomReportController::class)
            ->execute("getReportTable", $type);

        // Assertions
        $this->assertTrue($result->isOK());
        $this->assertStringContainsString("harish", $result->getBody());
        $this->assertIsString($result->getBody());
        $this->assertNotEmpty($result->getBody());
    }

    // Common function to test form data submission
    protected function testPostFormData($postData, $url, $type)
    {
        $uri = new URI($url);
        $config = new App();
        $request = new IncomingRequest($config, $uri, null, new UserAgent());
        $request->withMethod('post');
        $request->setGlobal('post', $postData);

        $result = $this->withRequest($request)
            ->controller(CustomReportController::class)
            ->execute('getFilterReport', $type);

        // Assertions
        $this->assertTrue($result->isOK());
        $this->assertNotEmpty($result->getBody());
        $this->assertStringContainsString('data', $result->getBody());
    }
}
