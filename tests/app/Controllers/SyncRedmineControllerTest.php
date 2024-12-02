<?php
namespace App\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\URI;
use CodeIgniter\HTTP\UserAgent;
use Config\App;
use App\Controllers\SyncRedmineController as SyncRedmineController;

class SyncRedmineControllerTest extends CIUnitTestCase{
    use ControllerTestTrait;

    // Function to test the sync functionality
    protected function testSync($postData, $url){
        $uri = new URI($url);
        $config = new App();
        $request = new IncomingRequest($config, $uri, null, new UserAgent());
        $request->withMethod('post');
        $request->setGlobal('post', $postData);
        $result = $this->withRequest($request)
                       ->controller(SyncRedmineController::class)
                       ->execute('syncAll');
        
        $this->assertTrue($result->isOk());
        $this->assertNotEmpty($result->getBody());
        $this->assertStringContainsString('true', $result->getBody());
    }

    // Testing product sync
    public function testSyncProducts(){
        $postData = [
            "productsync" => "on"
        ];
        $this->testSync($postData, "http://localhost:8080/syncing/redminesync/syncall");
    }

    // Testing product user sync
    public function testProductUserSync(){
        $postData = [
            "usersync" => "on"
        ];
        $this->testSync($postData, "http://localhost:8080/syncing/redminesync/syncall");
    }

    // Testing task sync
    public function testTaskSync(){
        $postData = [
            'tasksync' => "on"
        ];
        $this->testSync($postData, "http://localhost:8080/syncing/redminesync/syncall");
    }

    // Testing members sync
    public function testMembersSync(){
        $postData = [
            "membersync" => "on"
        ];
        $this->testSync($postData, "http://localhost:8080/syncing/redminesync/syncall");
    }

    // Testing customer sync
    public function testCustomerSync(){
        $postData = [
            "customersync" => "on"
        ];
        $this->testSync($postData, "http://localhost:8080/syncing/redminesync/syncall");
    }


    // Positive test case: Valid sync type with multiple syncs
    public function testMultipleSyncs(){
        $postData = [
            "productsync" => "on",
            "usersync" => "on",
            "tasksync" => "on"
        ];
        $this->testSync($postData, "http://localhost:8080/syncing/redminesync/syncall");
    }

    // Positive test case: Sync only users
    public function testSyncOnlyUsers(){
        $postData = [
            "usersync" => "on"
        ];
        $this->testSync($postData, "http://localhost:8080/syncing/redminesync/syncall");
    }

  
}
