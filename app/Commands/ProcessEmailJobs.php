<?php

/**
 * ProcessEmailJobs.php
 * 
 * @author   rubanedward.r <email>
 * @category Commands
 * 
 * this the task that will call the mail service to run in the
 * background process
 */

namespace App\Commands;

use App\Models\EmailJobModel;
use CodeIgniter\CLI\BaseCommand;
use App\Services\EmailService;

class ProcessEmailJobs extends BaseCommand
{
    protected $group = 'Tasks';
    // Command name that will be used to call this task from CLI
    protected $name = 'tasks:process_email_jobs';

    // Description of what this command does
    protected $description = 'Process pending email jobs.';

    /**
     * Execute the command.
     * 
     * @param array $params Command-line parameters (if any)
     */
    public function run(array $params)
    {
        // Initialize the EmailJobModel to interact with the email_jobs table
        $emailJobModel = new EmailJobModel();

        // Fetch all jobs with status 'N' (Not processed yet)
        $jobs = $emailJobModel->where('status', 'N')->findAll();

        // Loop through each job and process it
        foreach ($jobs as $job) {
            // Decode the JSON data for email details
            $emailData = [
                'email_id' => json_decode($job['email_id'], true),
                'fileName' => $job['file_name'],
                'contents' => json_decode($job['contents'], true),
            ];

            // Initialize the EmailService to send emails
            $emailService = new EmailService();

            // Attempt to send the email
            if ($emailService->sendMail($emailData)) {
                // If email is sent successfully, delete the job from the database
                $emailJobModel->delete($job['id']);
            } else {
                // If email is not sent, update the job's status back to 'N'
                $emailJobModel->update($job['id'], ['status' => 'N']);
            }
        }
    }
}
