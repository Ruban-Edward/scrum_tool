<?php

namespace Tests\app\Models;

use App\Models\User\UserModel;
use CodeIgniter\Test\CIUnitTestCase;

class UserModelTest extends CIUnitTestCase
{
    protected $model;

    protected function setUp(): void
    {
        parent::setUp();
        // Initialize the model
        $this->model = new UserModel();
    }

    public function testGetUser()
    {
        $testData = [
            "username"=> "senthilkumar",
            "password"=> "Infi@123",
        ];
        $result = $this->model->getUser($testData);
        // Check that the result is an array
        $this->assertIsArray($result);
        // Check if the array is not empty
        // $this->assertNotEmpty($result);
    }

    public function testInsertOrUpdatetUser()
    {
        $testData = [[
            "username"=> "yuvansri",  
            "employee_id"=> 36,
            "api_key"=> "e736069c1402952414054e6903170c61dbbcc5af",
            "first_name"=> "Yuvansri Thangavel",
            "last_name"=> "ISS677",
            "email_id"=> "yuvansri@infinitisoftware.net",
            "password"=> "Infi@123",
            "role_id"=> 1,
        ]];
        $result = $this->model->insertOrUpdatetUser($testData);
        $this->assertTrue($result);
    }
}