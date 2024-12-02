<?php

namespace Tests\app\Models;

use App\Models\Admin\AdminModel;
use CodeIgniter\Test\CIUnitTestCase;

class AdminModelTest extends CIUnitTestCase
{
    protected $AdminModel;

    protected function setUp(): void
    {
        parent::setUp();
        // Initialize the model
        $this->AdminModel = new AdminModel();
    }

    public function testGetUsers()
    {
        $result = $this->AdminModel->getUsers();

        // Check that the result is an array
        $this->assertIsArray($result);

        // Check if the array is not empty
        $this->assertNotEmpty($result);

        $this->assertEquals("scrum", $result[0]['first_name']);
        $this->assertEquals('scrumdemo@gmail.com', $result[0]['email_id']);
    }

    public function testGetPermissions()
    {
        $result = $this->AdminModel->getPermissions();

        // Check that the result is an array
        $this->assertIsArray($result);

        // Check if the array is not empty
        $this->assertNotEmpty($result);

        $this->assertEquals("PERMISSION_TEST", $result[0]['permission_name']);
        $this->assertEquals('login', $result[0]['module_name']);
    }

    public function testUpdateUserRole()
    {
        $testData = [
            'selectUser' => 1,
            'userId' => 1
        ];
        $result = $this->AdminModel->updateUserRole($testData);

        // Check that the result is an array
        $this->assertTrue($result);
    }

    public function testUserFilter()
    {
        $testData = "anish";
        $result = $this->AdminModel->userFilter($testData);

        // Check that the result is an array
        $this->assertIsArray($result);

        // Check if the array is not empty
        $this->assertNotEmpty($result);

        $expected_output = [
            [
                "user_id" => 44,
                "first_name" => "Anish Valanarasu",
                "last_name" => "ISS503",
                "email_id" => "anishvalanarasu@infinitisoftware.net",
                "role_id" => 8,
                "role_name" => "Developer",
            ]
        ];

        $this->assertEquals($expected_output, $result);
    }

    public function testGetLastSync()
    {
        $result = $this->AdminModel->getLastSync();

        // Check that the result is an array
        $this->assertIsArray($result);

        // Check if the array is not empty
        $this->assertNotEmpty($result);

        $this->assertEquals("2024-09-04 16:14:44", $result[0]['sync_datetime']);
    }

    public function testGetModule()
    {
        $result = $this->AdminModel->getModule();

        // Check that the result is an array
        $this->assertIsArray($result);

        // Check if the array is not empty
        $this->assertNotEmpty($result);

        $this->assertEquals("login", $result[0]['module_name']);
    }
}