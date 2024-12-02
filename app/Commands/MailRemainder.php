<?php

namespace App\Commands;

use App\Services\EmailRemainder;
use CodeIgniter\CLI\BaseCommand;

class MailRemainder extends BaseCommand
{

    protected $group = 'Tasks';
    protected $name = 'tasks:sync';
    protected $description = 'Remainder Mail Sending...';

    public function run(array $params)
    {
        $mailRemainder = new EmailRemainder();
        $sync = service('syncService');

        $productUserResult = $sync->syncProductUsers();
        $productResult = $sync->syncProducts();
        $taskResult = $sync->syncTasks();
        $customerResult = $sync->syncCustomers();
        $memberResult = $sync->syncMembers();

        $mailResult = $mailRemainder->remainderMail();

        if ($productUserResult && $productResult && $taskResult && $customerResult && $memberResult && $mailResult) {
            $result = "Successfully Synced";
        } else {
            $result = "Something error";
        }
        echo $result . " and " . $mailResult;
    }

}
