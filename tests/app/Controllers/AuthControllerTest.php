<?php

namespace App\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\URI;
use CodeIgniter\HTTP\UserAgent;
use Config\App;

class AuthControllerTest extends CIUnitTestCase
{
    use ControllerTestTrait;
    protected $backlogModelMock;
    protected $userModelMock;
    protected function setUp(): void
    {
        parent::setUp();
        $this->userModelMock = $this->createMock(\App\Models\User\UserModel::class);
        $this->backlogModelMock = $this->createMock(\App\Models\Backlog\BacklogModel::class);

        $this->userModelMock->method("getUser")
            ->willReturn([
                "first_name"=> "yuvansri",
                "external_employee_id"=> "36",
                "r_role_id"=> 7,
                "external_api_key"=> "e736069c1402952414054e6903170c61dbbcc5af",
            ]);

        $this->mockSession();
        $_SESSION['employee_id'] = 31;

        // Create an instance of the controller
        $this->controller = new \App\Controllers\AuthController();

        // Inject the mock models into the controller
        $this->setPrivateProperty($this->controller, 'userModel', $this->userModelMock);
        $this->setPrivateProperty($this->controller, 'backlogModel', $this->backlogModelMock);
    }

    public function testLoginValidate()
    {
        // Duplicate the POST data for the test
        $testData = [
            'username' => "yuvansri",
            'password' => "Infi@123",
        ];
        // creating the POST form Data to send to controller
        $uri = new URI('http://localhost:8080/login');
        $config = new App();
        $request = new IncomingRequest($config, $uri, null, new UserAgent());
        $request->withMethod('post');
        $request->setGlobal('post', $testData);
        // preparing the POST to send to the function
        $result = $this->withRequest($request)
            ->controller(\App\Controllers\AuthController::class)
            ->execute('loginValidate');
        // Assert that the result of the query execution is OK
        $this->assertTrue($result->isOK());        
    }

    public function testLogout()
    {
        // Set the URI for the request
        $result = $this->withURI('http://localhost:8080/logout')
            ->controller(\App\Controllers\AuthController::class)
            ->execute('logout');
        $this->assertTrue($result->isOK());        
    }

    public function testNoAccess()
    {
        // Set the URI for the request
        $result = $this->withURI('http://localhost:8080/no_access')
            ->controller(\App\Controllers\AuthController::class)
            ->execute('noAccess');
        $this->assertTrue($result->isOK());  
    }
}