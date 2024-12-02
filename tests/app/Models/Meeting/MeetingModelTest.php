<?php

namespace Tests\app\Models\Meeting;

use App\Models\Meeting\MeetingModel;
use CodeIgniter\Test\CIUnitTestCase;

class MeetingModelTest extends CIUnitTestCase
{
    protected $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new MeetingModel();
    }

    // Positive Test: Check getMeetingType functionality
    public function testGetMeetingType()
    {
        $result = $this->model->getMeetingType();
        $this->validateResultArray($result);

        $this->assertEquals(1, $result[0]['meeting_type_id']);
        $this->assertEquals('General', $result[0]['meeting_type_name']);
    }

    // Negative Test: Invalid meeting type scenario (simulating an empty result)
    public function testGetMeetingTypeReturnsEmpty()
    {
        // Assuming an invalid type or no data condition leads to an empty result.
        $result = $this->model->getMeetingType();
        $this->assertIsArray($result);
        $this->assertEmpty($result, "The result should be empty for invalid meeting types.");
    }

    // Positive Test: Check getMeetingLocation functionality
    public function testGetMeetingLocation()
    {
        $result = $this->model->getMeetingLocation();
        $this->validateResultArray($result);

        $this->assertEquals(1, $result[0]['meeting_location_id']);
        $this->assertEquals('Online', $result[0]['meeting_location_name']);
    }

    // Negative Test: Invalid meeting location scenario (e.g., non-existent location)
    public function testGetMeetingLocationReturnsEmpty()
    {
        $result = $this->model->getMeetingLocation();
        $this->assertIsArray($result);
        $this->assertEmpty($result, "The result should be empty for an invalid meeting location.");
    }

    // Positive Test: Check getMeetingById with various meeting types
    public function testGetMeetingByType()
    {
        $testCases = [
            ['meetingId' => 1, 'meetType' => 1, 'expectedKeys' => $this->generalMeetingKeys()],
            ['meetingId' => 2, 'meetType' => 2, 'expectedKeys' => $this->backlogMeetingKeys()],
            ['meetingId' => 5, 'meetType' => 3, 'expectedKeys' => $this->sprintMeetingKeys()],
        ];

        foreach ($testCases as $case) {
            $result = $this->model->getMeetingById($case['meetingId'], $case['meetType']);
            $this->validateResultArray($result);

            foreach ($case['expectedKeys'] as $key) {
                $this->assertArrayHasKey($key, $result[0]);
            }
        }
    }

    // Negative Test: Invalid meeting ID and type scenario (e.g., meeting not found)
    public function testGetMeetingByIdReturnsEmpty()
    {
        $result = $this->model->getMeetingById(9999, 9999); // Pass invalid meeting ID and type
        $this->assertIsArray($result);
        $this->assertEmpty($result, "The result should be empty for an invalid meeting ID and type.");
    }

    // Positive Test: Check getProduct functionality
    public function testGetProduct()
    {
        $id = 58;
        $result = $this->model->getProduct($id);
        $this->validateResultArray($result);

        $this->assertEquals(15, $result[0]['product_id']);
        $this->assertEquals(16, $result[0]['external_project_id']);
        $this->assertEquals('Agency - B2B', $result[0]['product_name']);
    }

    // Negative Test: Invalid product ID scenario (e.g., product not found)
    public function testGetProductReturnsEmpty()
    {
        $invalidId = 9999; // Assume this is an invalid ID
        $result = $this->model->getProduct($invalidId);
        $this->assertIsArray($result);
        $this->assertEmpty($result, "The result should be empty for an invalid product ID.");
    }

    // Positive Test: Check getSprintDuration functionality
    public function testGetSprintDuration()
    {
        $result = $this->model->getSprintDuration();
        $this->validateResultArray($result);

        $this->assertEquals(1, $result[0]['sprint_duration_id']);
        $this->assertEquals("1 week", $result[0]['sprint_duration_value']);
    }

    // Negative Test: Invalid sprint duration scenario (e.g., no sprint durations available)
    public function testGetSprintDurationReturnsEmpty()
    {
        $result = $this->model->getSprintDuration();
        $this->assertIsArray($result);
        $this->assertEmpty($result, "The result should be empty for invalid sprint durations.");
    }

    // Positive Test: Check getSprintStatus functionality
    public function testGetSprintStatus()
    {
        $result = $this->model->getSprintStatus();
        $this->validateResultArray($result);

        $this->assertEquals(19, $result[0]['module_status_id']);
        $this->assertEquals("Sprint Planned", $result[0]['status_name']);
    }

    // Negative Test: Invalid sprint status scenario (e.g., status not found)
    public function testGetSprintStatusReturnsEmpty()
    {
        $result = $this->model->getSprintStatus();
        $this->assertIsArray($result);
        $this->assertEmpty($result, "The result should be empty for invalid sprint statuses.");
    }

    // Positive Test: Check ShowSprint functionality
    public function testShowSprint()
    {
        $id = 36;
        $result = $this->model->ShowSprint($id);
        $this->validateResultArray($result);

        $this->assertEquals(1, $result[0]['sprint_id']);
        $this->assertEquals("Pnr creation", $result[0]['sprint_name']);
        $this->assertEquals("Wordpress - Projects", $result[0]['product_name']);
        $this->assertEquals("2024-08-29", $result[0]['start_date']);
        $this->assertEquals("2024-09-04", $result[0]['end_date']);
        $this->assertEquals("Sprint Running", $result[0]['status_name']);
        $this->assertEquals("1 week", $result[0]['sprint_duration_value']);
        $this->assertEquals("Advait infra\r", $result[0]['customer_name']);
        $this->assertEquals("Yuvansri Thangavel", $result[0]['first_name']);
    }

    // Negative Test: Invalid sprint ID scenario (e.g., sprint not found)
    public function testShowSprintReturnsEmpty()
    {
        $invalidId = 9999; // Assume this is an invalid sprint ID
        $result = $this->model->ShowSprint($invalidId);
        $this->assertIsArray($result);
        $this->assertEmpty($result, "The result should be empty for an invalid sprint ID.");
    }

    // --- HELPER METHODS ---

    private function validateResultArray($result)
    {
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

    private function generalMeetingKeys()
    {
        return [
            'meeting_details_id', 'meeting_title', 'product_name', 'first_name',
            'meeting_start_date', 'meeting_start_time', 'r_user_id', 'meeting_end_date',
            'meeting_end_time', 'meeting_duration', 'meeting_description', 'meeting_link',
            'r_user_id_created', 'is_logged', 'r_meeting_type_id', 'r_product_id',
            'r_meeting_location_id', 'is_deleted', 'cancel_reason', 'recurrance_meeting_id',
        ];
    }

    private function backlogMeetingKeys()
    {
        return array_merge($this->generalMeetingKeys(), ['r_backlog_item_id']);
    }

    private function sprintMeetingKeys()
    {
        return array_merge($this->generalMeetingKeys(), ['sprint_name', 'r_sprint_id']);
    }
}
