<?php

namespace App\Controllers;

class BackgroundEmail extends BaseController
{
    public function send($emailDataJson)
    {
        $emailData = json_decode($emailDataJson, true);
        $emailService = new \App\Services\EmailService();
        $emailService->asynMail($emailData);
    }
}
