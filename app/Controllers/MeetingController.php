<?php
/**
 * MeetingController.php
 *
 * @category   Controller
 * @author     Hari Sankar, Gokul ,Rama Selvan, Ruban Edward
 * @created    09 July 2024
 * @purpose    manages meetings , displaying the calendar,
 *             retrieving and updating meeting details and handling meeting cancellations
 */
namespace App\Controllers;

use App\Helpers\CustomHelpers;
use App\Services\EmailService;
use CodeIgniter\HTTP\Response;
use Config\SprintModelConfig;
use DateTime;

class MeetingController extends BaseController
{
    protected $meetingModelObj;
    protected $MeetingDetailModelObj;
    protected $MeetingMemberModelObj;
    protected $meetingObj;
    protected $brainstormMeetingObj;
    protected $MeetingTeamModelObj;
    protected $MeetingTeamMemberModelObj;
    protected $emailService;
    protected $backlogModel;
    protected $epicModel;
    protected $holidayModelObj;

    /**
     * Creating a object to access MeetingModel functions
     */
    public function __construct()
    {
        $this->meetingModelObj = model(\App\Models\Meeting\MeetingModel::class);
        $this->MeetingDetailModelObj = model(\App\Models\Meeting\MeetingDetailModel::class);
        $this->MeetingTeamModelObj = model(\App\Models\Meeting\MeetingTeamModel::class);
        $this->emailService = new EmailService();
    }

    /**
     * @author Ruban Edward
     * @return string
     * Retrieves and prepares data for displaying the meeting calendar view and sprint view
     */
    public function calendar(): string
    {
        $this->holidayModelObj = model(\App\Models\Meeting\HolidaysModel::class);

        $breadcrumbs = [
            'Home' => ASSERT_PATH . 'dashboard/dashboardView',
            'Calendar' => ASSERT_PATH . 'meeting/calendar',
        ];
        $id = session()->get('employee_id');
        $calendarData = [
            'showMeeting' => $this->MeetingDetailModelObj->showMeetings($id),
            'holidays' => $this->holidayModelObj->getHolidayDetails(),
            'meetingType' => $this->meetingModelObj->getMeetingType(),
            'meetingLocation' => $this->meetingModelObj->getMeetingLocation(),
            'product' => $this->meetingModelObj->getProduct($id),
            'sprintDetails' => $this->meetingModelObj->ShowSprint($id),
            'sprintDuration' => $this->meetingModelObj->getSprintDuration(),
            'sprintStatus' => $this->meetingModelObj->getSprintStatus(),
            'groupName' => $this->MeetingTeamModelObj->getTeamMembers($id),
        ];
        return $this->template_view('meeting/calendar', $calendarData, 'Calendar', $breadcrumbs);
    }

    /**
     * @author   Rama Selvan
     * @updated  Hari Sankar, Rama Selvan
     * @param int $id
     * @return Response A JSON-encoded response containing the sprint details.
     * Retrieves meeting details by ID and returns them as a JSON response.
     */
    public function getMeetingDetails($id, $type): response
    {
        $this->MeetingMemberModelObj = model(\App\Models\Meeting\MeetingMemberModel::class);
        // Fetch the meeting details from the model using the provided ID
        $meeting = $this->meetingModelObj->getMeetingById($id, $type);

        if ($meeting) {
            $meetId = $meeting[0]['meeting_details_id'];
            // Fetch the members associated with the meeting
            $meeting_members = $this->MeetingMemberModelObj->getMeetingMembersEdit(['meetId' => $meetId]);
            $memberId = array_column($meeting_members, 'r_user_id');
            $meeting_members_id = implode(",", $memberId);
            $names = array_column($meeting_members, 'first_name');
            $meeting_members_names = implode(",", $names);
            // Append the concatenated member names to the meeting details array
            array_push($meeting, ['meeting_members_names' => $meeting_members_names, 'meeting_members_id' => $meeting_members_id]);
            // Format the start and end dates using the helper function
            if (!empty($meeting)) {
                foreach ($meeting as &$sprintDetails) {
                    if (isset($sprintDetails['meeting_start_date'])) {
                        $sprintDetails['meeting_start_date'] = CustomHelpers::formatDate($sprintDetails['meeting_start_date']);
                    }
                }
            }
            return $this->response->setJSON($meeting);
        } else {
            return $this->response->setJSON(["message" => "Wrong Data"]);
        }
    }

    /**
     * @author  Gokul
     * @param int $id
     * @return Response A JSON-encoded response containing the sprint details.
     * Retrieves details of a specific sprint based on the given ID.
     */
    public function getSprintDetails($id): Response
    {
        $sprint = $this->meetingModelObj->getSprintById($id);
        if ($sprint) {
            return $this->response->setJSON($sprint);
        } else {
            return $this->response->setJSON(["message" => false]);
        }
    }

    /**
     * @author  Gokul
     * @param int $id
     * @return Response A JSON-encoded response containing the sprint details.
     * Retrieves details of a specific sprint based on the product ID.
     */
    public function sprintByProduct($productId): Response
    {
        $sprints = $this->meetingModelObj->getSprintByproduct($productId);
        if ($sprints) {
            return $this->response->setJSON($sprints);
        }
        return $this->response->setJSON(["message" => "Wrong Data"]);
    }
    /**
     * @author Ruban Edward, Hari Sankar
     * @datetime 12 July 2024
     * @param array $data An array containing meeting details and selected members
     * @return Response
     */
    public function meetingUpdation(): Response
    {
        // Loading the necessary models for meeting details and members.
        $this->MeetingMemberModelObj = model(\App\Models\Meeting\MeetingMemberModel::class);

        if ($this->request->getPost('Schedulebutton')) {
            $data = $this->request->getPost();
            $data['r_meeting_type_id'] = $this->request->getPost('r_meeting_type_id');
            $data['meeting_start_time'] = $this->convertMinutesToDateTime($data['meeting_start_time']);
            $data['meeting_end_time'] = $this->convertMinutesToDateTime($data['meeting_end_time']);
            $data['meeting_duration'] = $this->convertHoursAndMinutesToDuration($data['meeting_duration_hours'], $data['meeting_duration_minutes']);
            $data['r_user_id'] = $data['r_user_id_updated'] = session()->get('employee_id');
            $data['updated_date'] = date("Y-m-d H:i:s");
            $data['r_sprint_id'] = $this->request->getPost('r_sprint_id');

            //Recurrance Details
            $updateAsSeries = $this->request->getPost('updateAsSeries');
            $data['recurrance_meeting_id'] = $this->request->getPost('recurranceId');
            $data['update_as_series'] = ($updateAsSeries == "1" && $data['recurrance_meeting_id']) ? true : false;
            //Checking the Validations for the meeting
            $validationErrors = $this->hasInvalidInput($this->MeetingDetailModelObj, $data);
            if ($validationErrors !== true) {
                return $this->response->setJSON(['errors' => $validationErrors]);
            }
            // Handle member selection
            if (array_key_exists('addgroup', $data)) {
                $totalMembers = $this->handleMembersSelections($data['selectedEmails'], $data['addgroup']);
            } else {
                $data['addgroup'] = [];
                $totalMembers = $this->handleMembersSelections($data['selectedEmails'], $data['addgroup']);
            }

            if (empty($totalMembers)) {
                return $this->response->setJSON(['success' => true, 'members' => true]);
            }
            $existData = ['meet_id' => $data['meeting_id']];
            $Existing_members = $this->MeetingMemberModelObj->getExistingMeetingMembers($existData);
            $existing_member_names = [];
            $existing_member_names = array_column($Existing_members, 'external_employee_id');
            $UpdateConflict = array_diff($totalMembers, $existing_member_names);

            $conflictMembers = $this->checkConflicts($UpdateConflict, $data);
            if (!empty($conflictMembers)) {
                if (!session()->get('toast_shown')) {
                    session()->set('toast_shown', true);
                    return $this->response->setJSON(['conflict' => true, 'conflictMembers' => implode(", ", $conflictMembers)]);
                }
            }
            $result = $this->MeetingDetailModelObj->UpdateMeetingDetails($data);
            if ($result) {
                $totalMembers = $this->addHostToMembers($UpdateConflict);
                $meetingMembers = $this->meetingModelObj->getUserId($totalMembers);
                $membersId = array_column($meetingMembers, 'external_employee_id');
                // If update was successful, prepare data for updating meeting members
                $memberData = [
                    'meeting_id' => $data['meeting_id'],
                    'selected_members' => $membersId,
                ];
                $this->meetingMembersUpdate($memberData);
            }
            session()->remove('toast_shown');
            return $this->response->setJSON(['success' => true, 'mail' => true]);
        }
        return $this->response->setJSON(['success' => false]);

    }
    private function handleMembersSelections($members, $group)
    {
        // Handle member selection
        if (isset($group)) {
            $totalMembers = $this->getSelectedMembers($members, $group);
            return $totalMembers;
        } else {
            $group = [];
            $totalMembers = $this->getSelectedMembers($members, $group);
            return $totalMembers;
        }
    }
    public function meetingConfirmation(): Response
    {
        // Load models
        $this->MeetingMemberModelObj = model(\App\Models\Meeting\MeetingMemberModel::class);

        if ($this->request->getPost('Schedulebutton')) {
            // Retrieve and process form data
            $data = $this->request->getPost();
            $data['r_meeting_type_id'] = $this->request->getPost('r_meeting_type_id');
            $data['meeting_start_time'] = $this->convertMinutesToDateTime($data['meeting_start_time']);
            $data['meeting_end_time'] = $this->convertMinutesToDateTime($data['meeting_end_time']);
            $data['meeting_duration'] = $this->convertHoursAndMinutesToDuration($data['meeting_duration_hours'], $data['meeting_duration_minutes']);
            $data['r_user_id'] = $data['r_user_id_created'] = session()->get('employee_id');
            $data['created_date'] = date("Y-m-d H:i:s");
            $data['r_sprint_id'] = $this->request->getPost('r_sprint_id');

            if ($data['r_meeting_type_id'] == 3) {
                $data['meeting_start_date'] = $this->request->getPost('sprint_start_date');
                $data['meeting_end_date'] = $this->request->getPost('sprint_end_date');
            }
            $validationErrors = $this->hasInvalidInput($this->MeetingDetailModelObj, $data);
            if ($validationErrors !== true) {
                return $this->response->setJSON(['errors' => $validationErrors]);
            }
            // Handle member selection
            if (array_key_exists('addgroup', $data)) {
                $totalMembers = $this->handleMembersSelections($data['selectedEmails'], $data['addgroup']);
            } else {
                $data['addgroup'] = [];
                $totalMembers = $this->handleMembersSelections($data['selectedEmails'], $data['addgroup']);
            }

            if (empty($totalMembers)) {
                return $this->response->setJSON(['success' => true, 'members' => true]);
            }
            // Check for conflicts
            $conflictMembers = $this->checkConflicts($totalMembers, $data);
            if (!empty($conflictMembers)) {
                if (!session()->get('toast_shown')) {
                    session()->set('toast_shown', true);
                    return $this->response->setJSON(['conflict' => true, 'conflictMembers' => implode(", ", $conflictMembers)]);
                }
            }
            // Handle recurrence
            $recurrenceResult = $this->handleRecurrence($data);
            if ($recurrenceResult) {
                if ($data['r_meeting_type_id'] == 2) {
                    $this->brainstormMeetingDetails($recurrenceResult, $data);
                }
                $totalMembers = $this->addHostToMembers($totalMembers);
                $meetingMembers = $this->meetingModelObj->getUserId($totalMembers);
                $membersId = array_column($meetingMembers, 'external_employee_id');
                $membersEmail = array_column($meetingMembers, 'email_id');

                //If meeting is recurrence, meeting members can be inserted for the recurrance dates.
                if (is_array($recurrenceResult)) {
                    foreach ($recurrenceResult as $detailsId) {
                        $this->meetingMembersInsert($detailsId, $membersId);
                    }
                } else {
                    $this->meetingMembersInsert($recurrenceResult, $membersId);
                }
                //$emailData = $this->prepareEmailData($membersEmail, $data);
                session()->remove('toast_shown');
                //$this->emailService->sendMail($emailData);
                return $this->response->setJSON(['success' => true, 'mail' => true]);
            }
        }
        return $this->response->setJSON(['success' => false]);
    }
    /**
     * Retrieves the selected members' emails and group members' names..
     * @author Gokul
     * @param string $selectedEmails A comma-separated string of selected email addresses.
     * @param mixed  $group          The group identifier used to fetch group details.
     * @return array The array of member details, including selected emails and group members' names.
     */
    private function getSelectedMembers($selectedEmails, $group)
    {
        $totalMembers = [];
        if (!empty($selectedEmails)) {
            $selectedMembers = explode(',', ltrim($selectedEmails, ','));
            $totalMembers = array_merge($totalMembers, $selectedMembers);
        }
        if (!empty($group)) {
            $groupNames = explode(",", $group);
            foreach ($groupNames as $value) {
                $groupUserId = $this->meetingModelObj->getGroupDetails($value);
                if (!empty($groupUserId)) {
                    $meetingMembers = $this->meetingModelObj->getUserIdEmail($groupUserId);
                    $totalMembers = array_merge($totalMembers, array_column($meetingMembers, 'first_name'));
                }
            }
        }
        return $totalMembers;
    }
    /**
     * Checks for scheduling conflicts among selected members based on meeting data.
     * @author Ruban Edward
     * @param array $totalMembers An array of selected members' names.
     * @param array $data         An associative array containing the meeting details (start date, start time, and end time).
     * @return array An array of conflict descriptions, listing members with overlapping meetings and the corresponding time slots.
     */
    private function checkConflicts($totalMembers, $data)
    {
        $meetData = [
            'meeting_start_date' => $data['meeting_start_date'],
            'meeting_start_time' => $data['meeting_start_time'],
            'meeting_end_time' => $data['meeting_end_time'],
        ];
        $Conflict = $this->meetingModelObj->getMeetingMembersConflict($meetData);
        $conflictNames = [];

        foreach ($Conflict as $value) {
            if (in_array($value['first_name'], $totalMembers)) {
                $conflictNames[] = $value['first_name'] . " from " . CustomHelpers::convertTo12HourFormat($value['meeting_start_time']) . " to " . CustomHelpers::convertTo12HourFormat($value['meeting_end_time']);
            }
        }
        return array_unique($conflictNames);
    }
    /**
     * Handles the recurrence of meetings by generating and inserting meeting details for each recurrence date.
     * @author Rama Selvan
     * @param array $data An associative array containing the meeting details and recurrence information.
     * @return array An array of results from the insertion of each meeting detail, or a single result if not recurring.
     */
    private function handleRecurrence($data)
    {
        if ($data['r_meeting_type_id'] == 3) {
            $dates = $this->calculateRecurrenceDates($data['sprint_start_date'], $data['sprint_end_date'], $this->request->getPost('recurrance_meeting_id'));
            $recurrance_meeting_id = $this->generateUUID();
            $result = [];
            foreach ($dates as $date) {
                $checkdate = $this->checkSundays($date);
                if ($checkdate == 1) {
                    $data['meeting_start_date'] = $date;
                    $data['meeting_end_date'] = $date;
                    $data['recurrance_meeting_id'] = $recurrance_meeting_id;
                    $result[] = $this->MeetingDetailModelObj->insertMeetingDetails($data);
                }
            }
        } else {
            $result = $this->MeetingDetailModelObj->insertMeetingDetails($data);
        }
        return $result;
    }

    /**
     * Checks whether the meeting is scheduling sunday or not.
     * @author Hari Sankar R
     * @return integer
     */
    private function checkSundays($date)
    {
        $dayOfWeek = date('l', strtotime($date));
        if ($dayOfWeek === "Sunday") {
            return 0;
        } else {
            return 1;
        }
    }
    /**
     * Adds the host's name to the list of meeting members
     * @author Hari Sankar
     * @param array $membersId An array of member IDs to which the host's name will be added.
     * @return array The updated array of member IDs, including the host.
     */
    private function addHostToMembers($membersId)
    {
        $membersId[] = session()->get('first_name');
        return $membersId;
    }
    /**
     * Prepares the email data for meeting invitations.
     * @author Gokul
     * @param array $membersEmail An array of email addresses of the meeting members.
     * @param array $data         An associative array containing the meeting details.
     * @return array An array containing the email template data, including subject, meeting details, and recipient emails.
     */
    private function prepareEmailData($membersEmail, $data)
    {
        $mailInfo = [
            'meetType' => $data['r_meeting_type_id'],
            'product' => $data['r_product_id'],
        ];
        $result = $this->MeetingDetailModelObj->getProductMeetType($mailInfo);
        $meetType = $result[0]["meeting_type_name"];
        $product = $result[0]["product_name"];
        return [
            'email_id' => $membersEmail,
            'fileName' => 'mailTemplate',
            'contents' => [
                'subject' => $product . " - " . $data['meeting_title'],
                'Meeting_heading' => 'Meeting Invitation',
                'meeting_title' => $data['meeting_title'],
                'meeting_type' => $meetType,
                'host_name' => session()->get('first_name'),
                'product' => $product,
                'meeting_description' => $data['meeting_description'],
                'meeting_start_date' => CustomHelpers::convertDateFormat($data['meeting_start_date']),
                'meeting_start_time' => CustomHelpers::convertTo12HourFormat($data['meeting_start_time']),
                'meeting_end_time' => CustomHelpers::convertTo12HourFormat($data['meeting_end_time']),
                'meeting_link' => $data['meeting_link'],
            ],
        ];
    }

    /**
     * Insert Brainstorming meeting details and associated members
     * @author     Hari Sankar
     * @datetime   01 July 2024
     * @return mixed
     * @updated    Ruban Edward
     * @reason     Used switch case to schedule different type of meeting
     */
    public function brainstormMeetingDetails($result, $data)
    {
        $brainstorm = explode(',', $data['userstoryHidden']);
        $brainstormDetails = $this->meetingModelObj->getUserStoryId($brainstorm);
        $this->brainstormMeetingObj = model(\App\Models\Meeting\BrainstormMeetingDetailsModel::class);
        $userStoryModel = model(\App\Models\Backlog\UserStoryModel::class);

        foreach ($brainstormDetails as $value) {
            $meetBrainstormData = [
                'r_meeting_details_id' => $result,
                'r_backlog_item_id' => $value['r_backlog_item_id'],
                'r_epic_id' => $value['r_epic_id'],
                'r_user_story_id' => $value['user_story_id'],
            ];

            $validationErrors = $this->hasInvalidInput($this->brainstormMeetingObj, $meetBrainstormData);
            if ($validationErrors !== true) {
                return $this->response->setJSON(['errors' => $validationErrors]);
            }

            $this->brainstormMeetingObj->insertBrainstormDetails($meetBrainstormData);
            $userStoryModel->updateUserStoryStatus($meetBrainstormData['r_user_story_id']);
        }
    }

    /**
     * Update existing meeting details and associated members
     * @author Ruban Edward, Hari Sankar
     * @datetime   12 July 2024
     * @param array $data An array containing meeting details and selected members
     */
    public function meetingMembersInsert($result, $memberId)
    {
        // Iterating through each selected member
        foreach ($memberId as $member) {
            // Skip empty members
            $data = [
                'r_meeting_details_id' => $result,
                'r_user_id' => $member,
            ];
            $checkValidations = $this->hasInvalidInput($this->MeetingMemberModelObj, $data);
            if ($checkValidations !== true) {
                return $this->response->setJSON(['errors' => $checkValidations]);
            }
            $this->MeetingMemberModelObj->insertMeetingMembers($data);
        }
    }

    /**
     * Update meeting members for a specific meeting . If meeting updated means this is for meeting members updating
     * @author Ruban Edward, Hari Sankar
     * @datetime   15 July 2024
     * @param array $memberData An array containing the meeting ID and selected members
     * @return \CodeIgniter\HTTP\ResponseInterface|bool
     */
    public function meetingMembersUpdate($memberData)
    {
        // Split the comma-separated string of selected members into an array
        $selectedMembersArray = $memberData['selected_members'];
        // Iterate through each selected member
        foreach ($selectedMembersArray as $member) {
            $data = [
                'r_meeting_details_id' => $memberData['meeting_id'],
                'r_user_id' => $member,
            ];
            $checkValidations = $this->hasInvalidInput($this->MeetingMemberModelObj, $data);
            if ($checkValidations !== true) {
                return $this->response->setJSON(['errors' => $checkValidations]);
            }
            $this->MeetingMemberModelObj->updateMeetingMembers($data);
        }

        $ExistData = ['meet_id' => $memberData['meeting_id']];
        $Existing_members = $this->MeetingMemberModelObj->getExistingMeetingMembers($ExistData);
        // Extract names of existing members
        $existing_member_names = [];
        $existing_member_names = array_column($Existing_members, 'first_name');
        $deletedMembers = $this->meetingModelObj->getUserId($existing_member_names);
        $deletedId = array_column($deletedMembers, 'external_employee_id');
        // Find members that were not selected in this update
        $Not_Selected_members = $this->notSelectedMembers($deletedId, $selectedMembersArray);
        //Updating the Removed Emails in the column is_deleted as 'Y'
        foreach ($Not_Selected_members as $member) {
            $data = [
                'r_user_id' => $member,
                'r_meeting_details_id' => $memberData['meeting_id'],
            ];
            $this->MeetingMemberModelObj->deletingExistingMembers($data);
        }
        return 1;
    }

    /**
     * Finding the members that are removed during edit option
     * @author Hari Sankar R
     * @return array
     */
    private function notSelectedMembers($arrayData1, $arrayData2)
    {
        $members = array_diff($arrayData1, $arrayData2);
        return $members;
    }
    /**
     * Converts a given date and total minutes to a date-time string formatted as YYYY-MM-DD HH:MM:SS.
     * @author    Hari Sankar
     * @param string $date The date string in the format YYYY-MM-DD.
     * @param int $totalMinutes The total number of minutes to convert.
     * @return string The formatted date-time string in YYYY-MM-DD HH:MM:SS.
     */
    private function convertMinutesToDateTime($totalMinutes)
    {
        // Calculate hours from total minutes
        $hours = floor($totalMinutes / 60);
        // Calculate remaining minutes
        $minutes = $totalMinutes % 60;
        // Format hours and minutes with leading zeros if necessary
        $hours = ($hours < 10 ? '0' : '') . $hours;
        $minutes = ($minutes < 10 ? '0' : '') . $minutes;
        // Setting  seconds to '00' since this implementation doesn't consider seconds
        $seconds = '00';
        return $hours . ':' . $minutes . ':' . $seconds;
    }

    /**
     * Converts hours and minutes into a duration string formatted as HH:MM:SS.
     * @author Rama Selvan
     * @param int|string $hours The number of hours to convert. Can be an integer or a string representing an integer.
     * @param int|string $minutes The number of minutes to convert. Can be an integer or a string representing an integer.
     * @return string The formatted duration string in the format HH:MM:SS.
     */
    private function convertHoursAndMinutesToDuration($hours, $minutes)
    {
        // Ensure hours and minutes are integers
        $hours = intval($hours);
        $minutes = intval($minutes);
        // Format hours and minutes with leading zeros if necessary
        $hours = ($hours < 10 ? '0' : '') . $hours;
        $minutes = ($minutes < 10 ? '0' : '') . $minutes;
        // Setting seconds to '00' since this implementation doesn't consider seconds
        $seconds = '00';

        return $hours . ':' . $minutes . ':' . $seconds;
    }

    /**
     * Cancels a meeting and logs the reason for cancellation.
     * @author    Hari Sankar
     * @param int $id The ID of the meeting to cancel.
     * @return void
     */
    public function cancelMeetings($id)
    {
        // Check if the reason for cancellation is provided in the POST request
        if ($this->request->getPost('reason')) {
            // Get the cancellation reason from the POST request
            $reason = $this->request->getPost('reason');
            // Call the model method to log the cancellation reason for the specified meeting ID
            $this->MeetingDetailModelObj->CancelMeetingsReason(['id' => $id, 'reason' => $reason]);
            $getMail = $this->meetingModelObj->getCancelEmailId($id);

            $mailId = array_column($getMail, "email_id");
            $emailData = [
                'email_id' => $mailId,
                'fileName' => 'cancelMeet',
                'contents' => [
                    'subject' => $getMail[0]["product_name"] . " Meeting Cancelled",
                    'Meeting_heading' => 'Meeting Cancellation',
                    'host_name' => session()->get('first_name'),
                    'product' => $getMail[0]["product_name"],
                    'meeting_start_date' => $getMail[0]["meeting_start_date"],
                    'meeting_start_time' => CustomHelpers::convertTo12HourFormat($getMail[0]["meeting_start_time"]),
                    'meeting_end_time' => CustomHelpers::convertTo12HourFormat($getMail[0]["meeting_end_time"]),
                    'cancel_reason' => $getMail[0]["cancel_reason"],
                ],
            ];
            //$this->emailService->sendMail($emailData);
        }
    }

    /**
     * @author Rama Selvan
     * Logs meeting times for the provided attendees.
     * @return \CodeIgniter\HTTP\Response JSON response indicating the success or failure of the operation.
     */
    public function logMeetingTimes()
    {
        $timeLog = service('timeEntry');
        $post = $this->request->getPost();
        if ($post) {
            $dateTime = DateTime::createFromFormat('d-m-Y', $post['sdate']);
            $issue = $this->getOrCreateMeetingIssue([
                'meetingId' => $post['meetingId'],
                'productId' => $post['product'],
                'sprintId' => $post['sprintId'],
                'meetType' => $post['mtype'],
            ]);

            $meetTypes = [
                3 => 'Daily Stand up Meeting',
                4 => 'Sprint Review',
                'default' => 'Team Meeting',
            ];
            $activityId = $timeLog->activityId(['name' => $meetTypes[$post['mtype']] ?? $meetTypes['default']]);

            foreach ($post['attendees'] as $attendee) {
                try {
                    $timeLog->timeEntryLog([
                        'project_id' => $post['product'],
                        'author_id' => session()->get('employee_id'),
                        'user_id' => $attendee['id'],
                        'issue_id' => $issue,
                        'hours' => $attendee['hours'],
                        'comments' => $post['comments'],
                        'activity_id' => $activityId,
                        'spent_on' => $dateTime->format('Y-m-d'),
                        'tyear' => $dateTime->format('Y'),
                        'tmonth' => $dateTime->format('m'),
                        'tweek' => $dateTime->format('W'),
                        'created_on' => date('Y-m-d H:i:s'),
                        'updated_on' => date('Y-m-d H:i:s'),
                    ]);
                } catch (\Exception $e) {
                    log_message('error', "Exception while logging time for attendee ID: {$attendee['id']} - " . $e->getMessage());
                }
            }

            $updateResult = $this->MeetingDetailModelObj->timeLogisLogged(['meeting_details_id' => $post['meetingId']]);

            return $this->response->setJSON(['success' => $updateResult]);
        } else {
            return $this->response->setJSON(['success' => false]);
        }

    }

    /**
     * Retrieves or creates a meeting issue.
     *  @author Rama Selvan
     * @param array $data gets the data for the checking whether issue should be newly create or not.
     * @return int|null The ID of the existing or newly created issue, or null if no issue was found or created.
     */
    private function getOrCreateMeetingIssue($data)
    {
        $issueService = service('issues');
        $meetingDetails = $this->MeetingDetailModelObj->getMeetingDetails($data['meetingId']);
        $configObject = new SprintModelConfig();
        $externalIssueId = null;

        if ($data['meetType'] == 2) {
            $BrainStromMeetingDetailsModelobj = model(\App\Models\Meeting\BrainstormMeetingDetailsModel::class);
            $externalIssueId = $BrainStromMeetingDetailsModelobj->getBacklogId(['meeting_details_id' => $data['meetingId']]);
        } elseif ($data['meetType'] == 3 || $data['meetType'] == 4 || $data['meetType'] == 5) {
            $externalIssueId = $this->MeetingDetailModelObj->getExternalIssueIdForSprint([$data['sprintId']]);
        }

        if (!$externalIssueId) {
            $issueData = [
                'project_id' => $data['productId'],
                'task_title' => $meetingDetails['meeting_title'] ?? "N/A",
                'task_subject' => "Time logging issue for " . $this->getMeetingTypeString($data['meetType']),
                'task_description' => "Time logging issue for meeting: {$meetingDetails['meeting_description']}",
                'task_assignee' => session()->get('employee_id'),
                'task_priority' => $configObject->taskDatas['task_priority'],
                'task_statuses' => $configObject->taskDatas['task_statuses'],
                'task_tracker' => $configObject->taskDatas['task_tracker'],
                'author_id' => session()->get('employee_id'),
                'created_on' => date("Y-m-d H:i:s"),
                'updated_on' => date("Y-m-d H:i:s"),
            ];

            $externalIssueId = $issueService->insertTasksId($issueData);
        }

        $this->MeetingDetailModelObj->updateMeetingDetailsExternal([
            'external_issue_id' => $externalIssueId,
            'meeting_details_id' => $data['meetingId'],
        ]);

        return $externalIssueId;
    }

    private function getMeetingTypeString($meetType)
    {
        return match ($meetType) {
            1 => "General meetings",
            2 => "Backlogs Brainstroming meeting",
            3 => "Sprint meetings",
            default => "Unknown meeting type",
        };
    }

    /**
     * Handles the creation of a new meeting group and its members.
     * @author Hari sankar
     * @return mixed
     */
    public function createGroup()
    {
        $this->MeetingTeamMemberModelObj = model(\App\Models\Meeting\MeetingTeamMemberModel::class);
        if ($this->request->getPost()) {
            $data = $this->request->getPost();
            $selectedTeam = $this->request->getPost('selectedMembers');
            $result = $this->checkMembersSelected($selectedTeam);
            if ($result == 0) {
                return $this->response->setJSON(['empty' => true, 'message' => 'No members selected']);
            } else {
                $selectedTeamMembers = explode(",", $selectedTeam);
            }
            $data['r_external_employee_id'] = session()->get('employee_id');
            $checkValidations = $this->hasInvalidInput($this->MeetingTeamModelObj, $data);
            if ($checkValidations !== true) {
                return $this->response->setJSON(['errors' => $checkValidations]);
            }
            $teamId = $this->MeetingTeamModelObj->createGroup($data);
            $data = [
                'selectedTeamMembers' => $selectedTeamMembers,
                'r_meeting_team_id' => $teamId,
            ];
            $this->insertGroupMembers($data);
            return $this->response->setJSON(['success' => true, 'message' => 'Group Created Successfully']);
        } else {
            return $this->response->setJSON(['success' => false]);
        }
    }

    /**
     * Inserts group members into a meeting team.
     * @author Hari  sankar
     * @param array $data An associative array
     * @return \CodeIgniter\HTTP\Response|void JSON response containing 'errors' (if validation fails).
     */
    public function insertGroupMembers($data)
    {
        foreach ($data['selectedTeamMembers'] as $value) {
            $data = [
                'r_meeting_team_id' => $data['r_meeting_team_id'],
                'r_external_employee_id' => $value,
            ];
            $checkValidations = $this->hasInvalidInput($this->MeetingTeamMemberModelObj, $data);
            if ($checkValidations !== true) {
                return $this->response->setJSON(['errors' => $checkValidations]);
            }
            $this->MeetingTeamMemberModelObj->insertGroupMembers($data);
        }
    }

    /**
     * Retrieves the team details by ID.
     * @author Hari  Sankar
     * @param int $id The ID of the meeting team.
     * @return \CodeIgniter\HTTP\Response JSON response
     */
    public function getTeamDetailsById($id)
    {
        $data = ['r_meeting_team_id' => $id];
        $members = $this->meetingModelObj->getTeamDetailsById($data);
        if ($members) {
            return $this->response->setJSON(['members' => $members]);
        } else {
            return $this->response->setJSON(['success' => false]);
        }

    }
    /**
     * Checks whether the members are selected for the group
     * @author Hari  Sankar
     * @param string
     * @return mixed
     */
    public function checkMembersSelected($selectedTeam)
    {
        if (is_string($selectedTeam)) {
            if (empty(trim($selectedTeam))) {
                return 0;
            } else {
                return 1;
            }
        }

    }
    /**
     * Handles the editing of an existing meeting group and its members.
     * @author Hari Sankar
     * @return mixed
     */
    public function editGroup()
    {
        $this->MeetingTeamMemberModelObj = model(\App\Models\Meeting\MeetingTeamMemberModel::class);
        if ($this->request->getPost()) {
            $data = $this->request->getPost();
            $selectedTeam = $this->request->getPost('selectedMembers');
            $result = $this->checkMembersSelected($selectedTeam);
            if ($result == 0) {
                return $this->response->setJSON(['empty' => true, 'message' => 'No members selected']);
            } else {
                $selectedTeamMembers = explode(",", $selectedTeam);
            }
            $data['r_external_employee_id'] = session()->get('employee_id');
            $checkValidations = $this->hasInvalidInput($this->MeetingTeamModelObj, $data);
            if ($checkValidations !== true) {
                return $this->response->setJSON(['errors' => $checkValidations]);
            }
            $this->MeetingTeamModelObj->editTeamDetails($data);
            $data = [
                'selectedTeamMembers' => $selectedTeamMembers,
                'r_meeting_team_id' => $data['meeting_team_id'],
            ];
            $this->editGroupMembers($data);
            return $this->response->setJSON(['update' => true, 'message' => 'Group Updated Successfully']);
        }
    }

    /**
     * Edits the members of an existing meeting group.
     * @author Hari Sankar
     * @param array $data An associative array containing:
     *                    - 'selectedTeamMembers' (array): An array of member IDs to be added or retained in the group.
     *                    - 'r_meeting_team_id' (int): The ID of the meeting team to which the members belong.
     * @return \CodeIgniter\HTTP\Response|void JSON response containing 'errors' (if validation fails).
     */
    public function editGroupMembers($data)
    {
        $result = "";
        $membersTeam = [];
        foreach ($data['selectedTeamMembers'] as $value) {
            $membersTeam[] = $value;
            $data = [
                'r_meeting_team_id' => $data['r_meeting_team_id'],
                'r_external_employee_id' => $value,
            ];
            $checkValidations = $this->hasInvalidInput($this->MeetingTeamMemberModelObj, $data);
            if ($checkValidations !== true) {
                return $this->response->setJSON(['errors' => $checkValidations]);
            }
            $this->MeetingTeamMemberModelObj->editGroupMembers($data);
        }

        $ExistData = ['r_meeting_team_id' => $data['r_meeting_team_id']];
        $Existing_members = $this->MeetingTeamMemberModelObj->getExistingGroupMembers($ExistData);
        // Extract names of existing members
        $existing_member_names = [];
        $existing_member_names = array_column($Existing_members, 'r_external_employee_id');
        // Find members that were not selected in this update
        $Not_Selected_members = $this->notSelectedMembers($existing_member_names, $membersTeam);
        foreach ($Not_Selected_members as $member) {
            $data = [
                'r_external_employee_id' => $member,
                'r_meeting_team_id' => $data['r_meeting_team_id'],
            ];
            $result = $this->MeetingTeamMemberModelObj->deletingGroupMembers($data);
        }
    }

    /**
     * Deletes the group details.
     * @author Hari Sankar
     * @return mixed
     */
    public function deleteGroup()
    {
        $this->MeetingTeamMemberModelObj = model(\App\Models\Meeting\MeetingTeamMemberModel::class);
        if ($this->request->getPost()) {
            $id = $this->request->getPost('groupSelect');
            if ($id == null) {
                return $this->response->setJSON(['deleted' => false, 'message' => 'Failed']);
            }
            $data = ['meeting_team_id' => $id];
            $result = $this->MeetingTeamModelObj->deleteGroupDetails($data);
            log_message('debug', 'result ' . print_r($result, true));
            if ($result == true) {
                $this->MeetingTeamMemberModelObj->deleteGroupMembersDetails($data);
                return $this->response->setJSON(['deleted' => true, 'message' => 'Success']);
            } else {
                return $this->response->setJSON(['deleted' => false]);
            }
        }
    }
    /**
     * Retrieve sprint team members and meeting types details.
     * @author  Ruban Edward
     * @param int $sprintId The ID of the sprint for which to retrieve team members and meeting types.
     * @return \CodeIgniter\HTTP\Response JSON response                                      - 'team_members_id' (array): An array of team members' external employee IDs.
     */
    public function getSprintMembersDetails($sprintId)
    {
        // Fetch team members
        $teamMembers = $this->meetingModelObj->getSprintMembers($sprintId);
        $meetingTypearray = $this->meetingModelObj->getMeetingType();
        $meetingType = [];
        $meetingTypeId = [];

        foreach ($meetingTypearray as $data) {
            if ($data['meeting_type_name'] != 'General' && $data['meeting_type_name'] != 'Brainstorming') {
                $meetingType[] = $data['meeting_type_name'];
                $meetingTypeId[] = $data['meeting_type_id'];
            }
        }
        $memname = array_column($teamMembers, 'first_name');
        $mememail = array_column($teamMembers, 'email_id');
        $memid = array_column($teamMembers, 'external_employee_id');
        $response = [
            'team_members' => $memname,
            'team_members_email' => $mememail,
            'meettype' => $meetingType,
            'meettypeId' => $meetingTypeId,
            'team_members_id' => $memid,
        ];

        return $this->response->setJSON($response);
    }

    /**
     * Calculate the recurrence dates based on the given start date, end date, and recurrence pattern.
     * @author Rama Selvan
     * @param string $start_date The start date in 'Y-m-d' format.
     * @param string $end_date The end date in 'Y-m-d' format.
     * @param int $recurrence The recurrence pattern as an integer.
     * @return array An array of dates in 'Y-m-d' format based on the recurrence pattern.
     */
    private function calculateRecurrenceDates($start_date, $end_date, $recurrence)
    {
        $dates = [];
        $currentDate = strtotime($start_date);
        $endDate = strtotime($end_date);

        if ($recurrence === 0) {
            return [$start_date];
        }

        $intervals = [
            1 => "+1 day",
            2 => "+2 days",
            3 => "+3 days",
            4 => "+4 days",
            5 => "+5 days",
            6 => "+6 days",
            7 => "+1 week",
        ];

        while ($currentDate <= $endDate) {
            $dates[] = date("Y-m-d", $currentDate);
            if (isset($intervals[$recurrence])) {
                $currentDate = strtotime($intervals[$recurrence], $currentDate);
            } else {
                break;
            }
        }
        return $dates;
    }

    /**
     * Gets the members based on the product selected
     * @author    Hari Sankar
     * @param int $id
     * @return Response
     */
    public function getMembersByProduct($id): Response
    {
        $data = ['external_project_id' => $id];
        $members = $this->meetingModelObj->getMembersByProduct($data);
        if ($members) {
            return $this->response->setJSON(['members' => $members]);
        } else {
            return $this->response->setJSON(['success' => false]);
        }
    }

    /**
     * Retrieve the backlog items based on the product
     * @author    Hari Sankar
     * @param int $productId
     * @return    \CodeIgniter\HTTP\Response
     */
    public function backlogByProduct($productId): Response
    {
        $backlogs = $this->meetingModelObj->getbacklogByProduct($productId);
        if ($backlogs) {
            return $this->response->setJSON($backlogs);
        }
        return $this->response->setJSON(["success" => false]);
    }

    /**
     * retrives the epic based on the backlog
     * @param int $backlogId
     * @return \CodeIgniter\HTTP\Response
     */
    public function getEpic($backlogId): Response
    {
        $this->epicModel = model(\App\Models\Backlog\EpicModel::class);
        $epics = $this->epicModel->epicByBrainstrom($backlogId);
        if ($epics) {
            return $this->response->setJSON($epics);
        } else {
            return $this->response->setJSON(["success" => false]);
        }
    }

    /**
     * Generates a UUID (Universally Unique Identifier).
     * The UUID is generated using a combination of random values and specific
     * formatting to ensure uniqueness. The format follows the UUID version 4
     * specification, which is randomly generated.
     * @return string The generated UUID.
     */
    private function generateUUID()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    /**
     * @author  Ruban Edward
     * Check whether the pressed date is a holiday in the calendar
     */
    public function checkHoliday()
    {
        $this->holidayModelObj = model(\App\Models\Meeting\HolidaysModel::class);
        $date = $this->request->getGet('date');

        // Assuming you have a model method to check if a date is a holiday
        $isHoliday = $this->holidayModelObj->isHoliday($date);

        return $this->response->setJSON(['isHoliday' => $isHoliday]);
    }

    /**
     * Gets the sprint members based on the sprint ID
     * @author Hari Sankar R
     * @param int $sprintId
     * @return  \CodeIgniter\HTTP\Response
     */
    public function getSprintMembersById($sprintId)
    {
        $sprintMembers = $this->meetingModelObj->getSprintMembersById($sprintId);
        if ($sprintMembers) {
            return $this->response->setJSON(['members' => $sprintMembers]);
        }
        return $this->response->setJSON(['members' => false]);
    }
}