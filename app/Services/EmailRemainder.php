<?php

/**
 * EmailRemainder.php
 *
 * @author Gokul
 * @category Service
 * @purpose This is the mailRemainder service class. This class helps to send the remainder mail.
 */

namespace App\Services;

use App\Models\Meeting\MeetingModel;
use App\Models\RemainderJobModel;
use App\Services\EmailService;
use DateInterval;
use DateTime;
use Exception;

class EmailRemainder
{
    protected $custom;
    protected $emailService;
    protected $remainderModel;
    protected $meetingModel;

    /**
     * This function is used to send the remainderMail from current time to after 15 Mins meeting only.
     */
    public function remainderMail()
    {
        try {
            $currentDateTime = new DateTime();
            $currentDate = $currentDateTime->format('Y-m-d');
            $currentTime = $currentDateTime->format('H:i:s');
            $next15Minutes = $currentDateTime->add(new DateInterval('PT15M'))->format('H:i:s');

            $this->meetingModel = new MeetingModel();
            $data = $this->meetingModel->getMeetingDetails($currentDate, $currentTime, $next15Minutes);

            if (!empty($data)) {
                foreach ($data as $value) {
                    $body = [
                        'email_id' => $value['email_id'],
                        'fileName' => 'mailTemplate',
                        'contents' => [
                            'subject' => 'Meeting Starts at ' . $value['meeting_start_time'],
                            'Meeting_heading' => 'Meeting Remainder',
                            'meeting_title' => $value['meeting_title'],
                            'meeting_type' => $value['meeting_type_name'],
                            'host_name' => session()->get('first_name'),
                            'product' => $value['product_name'],
                            'meeting_description' => $value['meeting_description'],
                            'meeting_start_date' => $value['meeting_start_date'],
                            'meeting_start_time' => $value['meeting_start_time'],
                            'meeting_end_time' => $value['meeting_end_time'],
                            'meeting_link' => $value['meeting_link'],
                        ],
                    ];

                    $this->emailService = new EmailService();
                    $this->remainderModel = new RemainderJobModel();

                    $this->emailService->asynMail($body);
                    $this->remainderModel->insertRemainderJob($body);
                    $this->remainderModel->deleteJob();

                }
                return "Mail Sended Successfully";
            } else {
                return "No Meeting Available";
            }
        } catch (Exception $e) {
            $e->getMessage();
        }
    }
}
