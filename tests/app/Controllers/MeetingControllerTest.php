<?php

namespace Tests\Controllers;

use App\Controllers\MeetingController as MeetingController;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\URI;
use CodeIgniter\HTTP\UserAgent;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use Config\App;

class MeetingControllerTest extends CIUnitTestCase
{
    use ControllerTestTrait;
    protected $controller;
    protected $session;
    protected $meetingDetailModel;
    protected $holidaysModel;
    protected $meetingModel;
    protected $meetingTeamModel;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock the session data
        $this->session = \Config\Services::session();
        $this->session->set('employee_id', 36);

        // Create mock objects for models
        $this->meetingDetailModel = $this->createMock(\App\Models\Meeting\MeetingDetailModel::class);
        $this->holidaysModel = $this->createMock(\App\Models\Meeting\HolidaysModel::class);
        $this->meetingModel = $this->createMock(\App\Models\Meeting\MeetingModel::class);
        $this->meetingTeamModel = $this->createMock(\App\Models\Meeting\MeetingTeamModel::class);

        // Set up mock return values
        $this->setupMockReturnValues();

        // Create and set up the controller
        $this->controller = new MeetingController();
        $this->injectMockedModels();
    }

    private function injectMockedModels(): void
    {
        CIUnitTestCase::setPrivateProperty($this->controller, 'meetingModelObj', $this->meetingModel);
        CIUnitTestCase::setPrivateProperty($this->controller, 'MeetingDetailModelObj', $this->meetingDetailModel);
        CIUnitTestCase::setPrivateProperty($this->controller, 'holidayModelObj', $this->holidaysModel);
        CIUnitTestCase::setPrivateProperty($this->controller, 'MeetingTeamModelObj', $this->meetingTeamModel);
    }

    private function setupMockReturnValues(): void
    {
        $this->meetingDetailModel->method('showMeetings')->willReturn([
            [
                'is_deleted' => 'N',
                'is_logged' => 'Y',
                'meeting_details_id' => '1',
                'meeting_end_date' => '2024-08-28',
                'meeting_end_time' => '15:30:00',
                'meeting_start_date' => '2024-08-28',
                'meeting_start_time' => '15:00:00',
                'meeting_type_name' => 'General',
                'r_meeting_type_id' => '1',
            ],
        ]);

        $this->holidaysModel->method('getHolidayDetails')->willReturn([
            [
                'holiday_id' => '1',
                'holiday_start_date' => '2024-10-02',
                'holiday_title' => 'Gandhi Jayanthi',
            ],
        ]);

        $this->meetingModel->method('getMeetingType')->willReturn([
            [
                'is_deleted' => 'N',
                'meeting_type_id' => '1',
                'meeting_type_name' => 'General',
            ],
        ]);

        $this->meetingModel->method('getMeetingLocation')->willReturn([
            [
                'meeting_location_id' => 1,
                'meeting_location_name' => 'Online',
                'is_deleted' => 'N',
            ],
        ]);

        $this->meetingModel->method('getProduct')->willReturn([
            [
                'external_project_id' => '13',
                'product_id' => '13',
                'product_name' => 'CBT',
            ],
        ]);

        $this->meetingModel->method('ShowSprint')->willReturn([
            [
                'customer_name' => 'AYP',
                'end_date' => '2024-09-06',
                'first_name' => 'Yuvansri Thangavel',
                'product_name' => 'Agency - B2B',
                'sprint_duration_value' => '1 week',
                'sprint_id' => '1',
                'sprint_name' => 'Sprint 0.1',
                'start_date' => '2024-09-02',
                'status_name' => 'Sprint Planned',
            ],
        ]);

        $this->meetingModel->method('getSprintDuration')->willReturn([
            [
                'duration_id' => '1',
                'duration_value' => '1 week',
            ],
        ]);

        $this->meetingModel->method('getSprintStatus')->willReturn([
            [
                'is_deleted' => 'N',
                'meeting_type_id' => '1',
                'meeting_type_name' => 'General',
            ],
        ]);

        $this->meetingTeamModel->method('getTeamMembers')->willReturn([
            [
                'meeting_team_id' => 1,
                'meeting_team_name' => 'B2B',
            ],
        ]);
    }

    public function testCalendar()
    {
        $result = $this->controller(MeetingController::class)
            ->execute('calendar');

        $this->assertTrue($result->isOK());
        $this->assertStringContainsString('General', $result->getBody());
    }

    public function testGetMeetingDetails()
    {
        $result = $this->controller(MeetingController::class)
            ->execute('getMeetingDetails', 1, 1);

        $this->assertTrue($result->isOK());
        $this->assertStringContainsString('Yuvansri Thangavel', $result->getBody());
    }

    public function testInvalidGetMeetingDetails()
    {
        // Attempt to get meeting details for an invalid meeting ID or user ID
        $result = $this->controller(MeetingController::class)
            ->execute('getMeetingDetails', 9999, 1);

        $this->assertTrue($result->isOK());
        $this->assertJsonStringEqualsJsonString(json_encode(["message" => "Wrong Data"]), $result->getJSON());
    }

    public function testGetSprintDetails()
    {
        $id = 1;
        $result = $this->controller(MeetingController::class)
            ->execute('getSprintDetails', $id);

        $this->assertTrue($result->isOK());
        $this->assertStringContainsString('Sprint Running', $result->getBody());
    }

    public function testInvalidGetSprintDetails()
    {
        $id = 1000;
        $result = $this->controller(MeetingController::class)
            ->execute('getSprintDetails', $id);

        $this->assertTrue($result->isOK());
        $this->assertJsonStringEqualsJsonString(json_encode(["message" => false]), $result->getJSON());
    }

    public function testSprintByProduct()
    {
        $result = $this->controller(MeetingController::class)
            ->execute('sprintByProduct', 65);

        $this->assertTrue($result->isOK());
        $this->assertStringContainsString('Pnr creation', $result->getBody());
    }

    public function testInvalidSprintByProduct()
    {
        $result = $this->controller(MeetingController::class)
            ->execute('sprintByProduct', 6500);

        $this->assertTrue($result->isOK());
        $this->assertJsonStringEqualsJsonString(json_encode(["message" => "Wrong Data"]), $result->getJSON());

    }

    // public function testMeetingUpdation()
    // {
    //     $data = [
    //         'Schedulebutton' => 1,
    //         'meeting_id' => 19,
    //         'meeting_title' => 'Update Test Case',
    //         'r_meeting_type_id' => 4,
    //         'r_user_id' => 36,
    //         'r_product_id' => 65,
    //         'r_meeting_location_id' => 2,
    //         'meeting_description' => 'Update Test Case',
    //         'meeting_start_date' => '2024-09-03',
    //         'meeting_start_time' => 915,
    //         'meeting_end_date' => '2024-09-03',
    //         'meeting_end_time' => 945,
    //         'meeting_duration_hours' => 0,
    //         'meeting_duration_minutes' => 30,
    //         'meeting_link' => 'Meeting Room- 342',
    //         'r_user_id_updated' => 36,
    //         'selectedEmails' => ',ashwinbalaji@infinitisoftware.net',
    //     ];

    //     $uri = new URI('http://localhost:8080/meeting/updateMeeting');
    //     $config = new App();
    //     $request = new IncomingRequest($config, $uri, null, new UserAgent());
    //     $request->withMethod('post');
    //     $request->setGlobal('post', $data);

    //     // Preparing the POST to send to the function
    //     $result = $this->withRequest($request)
    //         ->controller(MeetingController::class)
    //         ->execute('meetingUpdation');

    //     $this->meetingDetailModel->method('UpdateMeetingDetails')
    //         ->with($this->equalTo($data))
    //         ->willReturn(true);

    //     // Assert that the result of the query execution is OK
    //     $this->assertTrue($result->isOK());
    //     $this->assertJsonStringEqualsJsonString(json_encode(['mail' => true, 'success' => true]), $result->getJSON());
    // }

    public function testMeetingConfirmation()
    {
        $data = [
            'Schedulebutton' => 1,
            'meeting_id' => 6,
            'meeting_title' => 'Insert Unit test case',
            'r_meeting_type_id' => 4,
            'r_user_id' => 36,
            'r_product_id' => 65,
            'r_sprint_id' => 1,
            'r_meeting_location_id' => 2,
            'meeting_description' => 'Check Unit Test Case',
            'meeting_start_date' => '2024-09-02',
            'meeting_start_time' => 950,
            'meeting_end_date' => '2024-09-02',
            'meeting_end_time' => 955,
            'meeting_duration_hours' => 0,
            'meeting_duration_minutes' => 5,
            'meeting_link' => 'Meeting Room- 342',
            'r_user_id_created' => 36,
            'created_date' => '2024-09-02 13:45:02',
            'selectedEmails' => ',thameem@infinitisoftware.net',
        ];

        $uri = new URI('http://localhost:8080/meeting/scheduleMeeting');
        $config = new App();
        $request = new IncomingRequest($config, $uri, null, new UserAgent());
        $request->withMethod('post');
        $request->setGlobal('post', $data);

        // Preparing the POST to send to the function
        $result = $this->withRequest($request)
            ->controller(MeetingController::class)
            ->execute('meetingConfirmation');

        // Assert that the result of the query execution is OK
        $this->assertTrue($result->isOK());
        $this->assertJsonStringEqualsJsonString(json_encode(["mail" => true, 'success' => true]), $result->getJSON());
    }

    public function testInvalidMeetingConfirmation()
    {
        $data = [
            'Schedulebutton' => 1,
            'meeting_id' => 6,
            'meeting_title' => 'Insert Unit test case',
            'r_meeting_type_id' => 4,
            'r_user_id' => 36,
            'r_product_id' => 65,
            'r_sprint_id' => 1,
            'meeting_start_date' => '2024-09-02',
            'meeting_start_time' => 950,
            'meeting_end_date' => '2024-09-02',
            'meeting_end_time' => 955,
            'meeting_duration_hours' => 0,
            'meeting_duration_minutes' => 5,
            'meeting_link' => 'Meeting Room- 342',
            'r_user_id_created' => 36,
            'created_date' => '2024-09-02 13:45:02',
            'selectedEmails' => ',thameem@infinitisoftware.net',
        ];

        $uri = new URI('http://localhost:8080/meeting/scheduleMeeting');
        $config = new App();
        $request = new IncomingRequest($config, $uri, null, new UserAgent());
        $request->withMethod('post');
        $request->setGlobal('post', $data);

        // Preparing the POST to send to the function
        $result = $this->withRequest($request)
            ->controller(MeetingController::class)
            ->execute('meetingConfirmation');

        // Assert that the result of the query execution is OK
        $this->assertTrue($result->isOK());
        $this->assertJsonStringEqualsJsonString(json_encode(["errors" => [
            "r_meeting_location_id" => "The meeting location ID is required.",
        ]]), $result->getJSON());
    }

    // public function testCancelMeetings()
    // {
    //     $data = ['reason' => 'Test Case Cancel Meeting'];
    //     $id = 18;

    //     $uri = new URI('http://localhost:8080/meeting/cancelMeeting');
    //     $config = new App();
    //     $request = new IncomingRequest($config, $uri, null, new UserAgent());
    //     $request->withMethod('post');
    //     $request->setGlobal('post', $data);

    //     $result = $this->withRequest($request)
    //         ->controller(MeetingController::class)
    //         ->execute('cancelMeetings', $id);

    //     $this->assertTrue($result->isOK());
    //     // $this->assertJsonStringEqualsJsonString(json_encode(['success' => true]), $result->getJSON());
    // }

    public function testLogMeetingTimes()
    {
        $data = array(
            "attendees" => array(
                array(
                    "id" => 36,
                    "name" => "Yuvansri Thangavel",
                    "hours" => 0.5,
                ),
                array(
                    "id" => 86,
                    "name" => "Ranjani P",
                    "hours" => 0.5,
                ),
                array(
                    "id" => 95,
                    "name" => "Divya Bharathi S",
                    "hours" => 0.5,
                ),
                array(
                    "id" => 129,
                    "name" => "baranitharan B",
                    "hours" => 0.5,
                ),
                array(
                    "id" => 140,
                    "name" => "Bhavani R",
                    "hours" => 0.5,
                ),
                array(
                    "id" => 263,
                    "name" => "Gokul Mani",
                    "hours" => 0.5,
                ),
            ),
            "meetingId" => 9,
            "comments" => " Agency - B2B",
            "sdate" => "02-09-2024",
            "product" => 16,
            "mtype" => 1,
            "sprintId" => null,
        );

        $uri = new URI('http://localhost:8080/meeting/logMeetingTimes');
        $config = new App();
        $request = new IncomingRequest($config, $uri, null, new UserAgent());
        $request->withMethod('post');
        $request->setGlobal('post', $data);

        $result = $this->withRequest($request)
            ->controller(MeetingController::class)
            ->execute('logMeetingTimes');

        $this->assertTrue($result->isOK());
        $this->assertJsonStringEqualsJsonString(json_encode(['success' => true]), $result->getJSON());
    }

    public function testInvalidLogMeetingTimes()
    {
        $data = [];

        $uri = new URI('http://localhost:8080/meeting/logMeetingTimes');
        $config = new App();
        $request = new IncomingRequest($config, $uri, null, new UserAgent());
        $request->withMethod('post');
        $request->setGlobal('post', $data);

        $result = $this->withRequest($request)
            ->controller(MeetingController::class)
            ->execute('logMeetingTimes');

        $this->assertTrue($result->isOK());
        $this->assertJsonStringEqualsJsonString(json_encode(['success' => false]), $result->getJSON());
    }

    public function testCreateGroup()
    {
        $data = [
            'r_product_id' => 16,
            'meeting_team_name' => 'Team Test',
            'meeting_team_id' => 16,
            'selectedMembers' => '51,319,113,112',
        ];

        $uri = new URI('http://localhost:8080/meeting/createGroupDetails');
        $config = new App();
        $request = new IncomingRequest($config, $uri, null, new UserAgent());
        $request->withMethod('post');
        $request->setGlobal('post', $data);

        $result = $this->withRequest($request)
            ->controller(MeetingController::class)
            ->execute('createGroup');

        $this->assertTrue($result->isOK());
        $this->assertJsonStringEqualsJsonString(json_encode(["message" => "Group Created Successfully", 'success' => true]), $result->getJSON());
    }

    public function testInvalidCreateGroup()
    {
        $data = [
            'meeting_team_name' => 'Team Test',
            'meeting_team_id' => 16,
            'selectedMembers' => '51,319,113,112',
        ];

        $uri = new URI('http://localhost:8080/meeting/createGroupDetails');
        $config = new App();
        $request = new IncomingRequest($config, $uri, null, new UserAgent());
        $request->withMethod('post');
        $request->setGlobal('post', $data);

        $result = $this->withRequest($request)
            ->controller(MeetingController::class)
            ->execute('createGroup');

        $this->assertTrue($result->isOK());
        $this->assertJsonStringEqualsJsonString(json_encode(["errors" => [
            "r_product_id" => "The product ID is required.",
        ]]), $result->getJSON());
    }

    public function testGetTeamDetailsById()
    {
        $data = ['r_meeting_team_id' => 2];

        $result = $this->controller(MeetingController::class)
            ->execute('getTeamDetailsById', $data);

        $this->assertTrue($result->isOK());
    }

    public function testInvalidGetTeamDetailsById()
    {
        $data = ['r_meeting_team_id' => 1000];

        $result = $this->controller(MeetingController::class)
            ->execute('getTeamDetailsById', $data);

        $this->assertJsonStringEqualsJsonString(json_encode(['success' => false]), $result->getJSON());
    }

    public function testEditGroup()
    {
        $data = [
            'r_product_id' => 16,
            'meeting_team_name' => 'Team Test',
            'meeting_team_id' => 4,
            'selectedMembers' => '54, 319, 113, 133',
        ];

        $uri = new URI('http://localhost:8080/meeting/editGroupDetails');
        $config = new App();
        $request = new IncomingRequest($config, $uri, null, new UserAgent());
        $request->withMethod('post');
        $request->setGlobal('post', $data);

        $result = $this->withRequest($request)
            ->controller(MeetingController::class)
            ->execute('editGroup');

        $this->assertTrue($result->isOK());
        $this->assertJsonStringEqualsJsonString(json_encode(["message" => "Group Updated Successfully", 'update' => true]), $result->getJSON());
    }

    public function testInvalidDataTypeEditGroup()
    {
        $data = [
            'r_product_id' => "abcd",
            'meeting_team_name' => 'Team Test',
            'meeting_team_id' => 4,
            'selectedMembers' => '54, 319, 113, 133',
        ];

        $uri = new URI('http://localhost:8080/meeting/editGroupDetails');
        $config = new App();
        $request = new IncomingRequest($config, $uri, null, new UserAgent());
        $request->withMethod('post');
        $request->setGlobal('post', $data);

        $result = $this->withRequest($request)
            ->controller(MeetingController::class)
            ->execute('editGroup');

        $this->assertTrue($result->isOK());
        $this->assertJsonStringEqualsJsonString(json_encode(["errors" => [
            "r_product_id" => "The Product ID must be an integer.",
        ]]), $result->getJSON());
    }

    public function testInvalidEditGroup()
    {
        $data = [
            'meeting_team_name' => 'Team Test',
            'meeting_team_id' => 4,
            'selectedMembers' => '54, 319, 113, 133',
        ];

        $uri = new URI('http://localhost:8080/meeting/editGroupDetails');
        $config = new App();
        $request = new IncomingRequest($config, $uri, null, new UserAgent());
        $request->withMethod('post');
        $request->setGlobal('post', $data);

        $result = $this->withRequest($request)
            ->controller(MeetingController::class)
            ->execute('editGroup');

        $this->assertTrue($result->isOK());
        $this->assertJsonStringEqualsJsonString(json_encode(["errors" => [
            "r_product_id" => "The product ID is required.",
        ]]), $result->getJSON());
    }

    public function testDeleteGroup()
    {
        $data = ['groupSelect' => 3];

        $uri = new URI('http://localhost:8080/meeting/deleteGroupDetails');
        $config = new App();
        $request = new IncomingRequest($config, $uri, null, new UserAgent());
        $request->withMethod('post');
        $request->setGlobal('post', $data);

        $result = $this->withRequest($request)
            ->controller(MeetingController::class)
            ->execute('deleteGroup');

        $this->assertTrue($result->isOK());
        $this->assertJsonStringEqualsJsonString(json_encode(['deleted' => true, 'message' => 'Success']), $result->getJSON());
    }

    public function testInvalidDeleteGroup()
    {
        $data = ['groupSelect' => ''];

        $uri = new URI('http://localhost:8080/meeting/deleteGroupDetails');
        $config = new App();
        $request = new IncomingRequest($config, $uri, null, new UserAgent());
        $request->withMethod('post');
        $request->setGlobal('post', $data);

        $result = $this->withRequest($request)
            ->controller(MeetingController::class)
            ->execute('deleteGroup');

        $this->assertTrue($result->isOK());
        $this->assertJsonStringEqualsJsonString(json_encode(['deleted' => false, 'message' => 'Failed']), $result->getJSON());
    }

    public function testGetSprintMembersDetails()
    {
        $id = 1;
        $result = $this->controller(MeetingController::class)
            ->execute('getSprintDetails', $id);

        $this->assertTrue($result->isOK());
        foreach ($result as $data) {
            $this->assertArrayHasKey('sprint_id', $data);
        }
    }

    public function testInvalidGetSprintMembersDetails()
    {
        $id = '';
        $result = $this->controller(MeetingController::class)
            ->execute('getSprintDetails', $id);

        $this->assertTrue($result->isOK());
        $this->assertJsonStringEqualsJsonString(json_encode(['message' => false]), $result->getJSON());
    }

    public function testGetMembersByProduct()
    {
        $id = ['external_project_id' => 16];
        $result = $this->controller(MeetingController::class)
            ->execute('getMembersByProduct', $id);

        $this->assertTrue($result->isOK());
        foreach ($result as $member) {
            $this->assertArrayHasKey('external_employee_id', $member);
        }
    }

    public function testInvalidGetMembersByProduct()
    {
        $id = ['external_project_id' => 1600];
        $result = $this->controller(MeetingController::class)
            ->execute('getMembersByProduct', $id);

        $this->assertTrue($result->isOK());
        $this->assertJsonStringEqualsJsonString(json_encode(['success' => false]), $result->getJSON());
    }

    public function testBacklogByProduct()
    {
        $id = 65;
        $result = $this->controller(MeetingController::class)
            ->execute('backlogByProduct', $id);

        $this->assertTrue($result->isOK());

        $backlog = json_decode($result->getJSON(), true);
        $this->assertIsArray($backlog);
        foreach ($backlog as $value) {
            $this->assertArrayHasKey('backlog_item_id', $value);
        }
    }

    public function testInvalidBacklogByProduct()
    {
        $id = 605;
        $result = $this->controller(MeetingController::class)
            ->execute('backlogByProduct', $id);

        $this->assertTrue($result->isOK());
        $this->assertJsonStringEqualsJsonString(json_encode(['success' => false]), $result->getJSON());
    }

    public function testGetEpic()
    {
        $id = 1;
        $result = $this->controller(MeetingController::class)
            ->execute("getEpic", $id);

        $this->assertTrue($result->isOK());
    }

    public function testInavlidGetEpic()
    {
        $id = 1000;
        $result = $this->controller(MeetingController::class)
            ->execute("getEpic", $id);

        $this->assertTrue($result->isOK());
        $this->assertJsonStringEqualsJsonString(json_encode(['success' => false]), $result->getJSON());
    }

    public function testGetSprintMembersById()
    {
        $id = 1;
        $result = $this->controller(MeetingController::class)
            ->execute("getSprintMembersById", $id);

        $this->assertTrue($result->isOK());
    }

    public function testInvalidGetSprintMembersById()
    {
        $id = 100;
        $result = $this->controller(MeetingController::class)
            ->execute("getSprintMembersById", $id);

        $this->assertTrue($result->isOK());
        $this->assertJsonStringEqualsJsonString(json_encode(["members" => false]), $result->getJSON());
    }

}
