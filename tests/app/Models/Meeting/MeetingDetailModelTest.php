<?php

namespace Tests\app\Models\Meeting;

use App\Models\Meeting\MeetingDetailModel;
use CodeIgniter\Test\CIUnitTestCase;

class MeetingDetailModelTest extends CIUnitTestCase
{
    protected $meetingDetailModel;

    public function setUp(): void
    {
        parent::setUp();
        $this->meetingDetailModel = new MeetingDetailModel();
    }

    public function testInsertMeetingDetails()
    {
        // Test data for inserting a meeting
        $data = [
            'meeting_title' => 'New Unit Test Case',
            'r_meeting_type_id' => 1,
            'r_user_id' => 36,
            'r_product_id' => 65,
            'r_sprint_id' => 3,
            'r_meeting_location_id' => 1,
            'meeting_description' => 'Unit test case',
            'meeting_start_date' => '2024-09-03',
            'meeting_start_time' => '17:15:00',
            'meeting_end_date' => '2024-09-03',
            'meeting_end_time' => '17:30:00',
            'meeting_duration' => '00:15:00',
            'meeting_link' => 'meeting room no 1',
            'r_user_id_created' => 36,
            'created_date' => '2024-09-03 15:25:00',
        ];

        // Insert the meeting details using the model function
        $insertedId = $this->meetingDetailModel->insertMeetingDetails($data);

        // Assertions to verify the expected results
        $this->assertIsInt($insertedId, 'Insert operation did not return an integer ID');
        $this->assertGreaterThan(0, $insertedId, 'Insert operation failed, returned ID should be greater than 0');
    }

    public function testUpdateMeetingDetails()
    {
        // Test data for inserting a meeting
        $data = [
            'meeting_id' => 27,
            'meeting_title' => 'Unit Test Case Updated',
            'r_meeting_type_id' => 1,
            'r_user_id' => 36,
            'r_product_id' => 65,
            'r_sprint_id' => null,
            'r_meeting_location_id' => 1,
            'meeting_description' => 'Unit test case',
            'meeting_start_date' => '2024-09-03',
            'meeting_start_time' => '17:15:00',
            'meeting_end_date' => '2024-09-03',
            'meeting_end_time' => '17:30:00',
            'meeting_duration' => '00:15:00',
            'meeting_link' => 'meeting room no 1',
            'r_user_id_updated' => 36,
            'updated_date' => '2024-09-03 15:35:00',
            'update_as_series' => 'Sprint',
            'recurrance_meeting_id' => null,
        ];

        // Insert the meeting details using the model function
        $updated = $this->meetingDetailModel->UpdateMeetingDetails($data);

        // Assertions to verify the expected results
        $this->assertTrue($updated);
    }

    public function testUpdateMeetingDetailsExternal()
    {
        $data = ['external_issue_id' => 21341,
            'meeting_details_id' => 27];

        $result = $this->meetingDetailModel->UpdateMeetingDetailsExternal($data);
        $this->assertTrue($result);
    }

    public function testTimeLogisLogged()
    {
        $data = ['is_logged' => 'Y',
            'meeting_details_id' => 5];

        $result = $this->meetingDetailModel->TimeLogisLogged($data);
        $this->assertTrue($result);
    }

    public function testGetMeetingDetails()
    {
        $id = 1;
        $result = $this->meetingDetailModel->GetMeetingDetails($id);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

    public function testUpdateExternalReferenceTaskId()
    {
        $taskId = 21341;
        $meetingId = 25;

        $result = $this->meetingDetailModel->updateExternalReferenceTaskId($meetingId, $taskId);
        $this->assertTrue($result);
    }

    public function testShowMeetings()
    {
        $id = 54;
        $result = $this->meetingDetailModel->showMeetings($id);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

    public function testCancelMeetingsReason()
    {
        $data = ['reason' => "Due to rain",
            'id' => 25,
        ];

        $result = $this->meetingDetailModel->CancelMeetingsReason($data);
        $this->assertTrue($result);
    }

    public function testGetExternalIssueIdForSprint()
    {
        $id = [1];
        $result = $this->meetingDetailModel->getExternalIssueIdForSprint($id);
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function testGetProductMeetType()
    {
        $data = ['meetType' => 1, 'product' => 65];
        $result = $this->meetingDetailModel->getProductMeetType($data);
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }
}
