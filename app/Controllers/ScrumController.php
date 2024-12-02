<?php
namespace App\Controllers;

class ScrumController extends BaseController
{
    public function getProjects()
    {
        $redmine = service('redmine');
        $client = $redmine->getClient(session('redmine_api_key'));
        $users = $client->getApi('issue')->all();
        // $users = $client->getApi('project')->show('5');
        // (['limit' => 50, 'offset' => 50]);
        // print_r($users);
        // $projects = [
        //     'cms', 'cbt', 'b2b'
        // ];
        return json_encode($users);
    }  

    /**
     * Sample mail 
     */
    public function mail()
    {
        $email = service('email');
        // $email->setFrom('');
        $email->setTo('ramaselvan161@gmail.com,rubanedward769@gmail.com');
        // $email->setCC('another@another-example.com');
        // $email->setBCC('them@their-example.com');

        $email->setSubject('Email Test');
        $email->setMessage('dummy mail to check whether it works');

        $dd = $email->send();

        if($dd == TRUE){
            echo "Successfull";
        }
        else{
            echo "Mail not sent";
        }

    }
}