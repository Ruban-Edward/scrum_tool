<?php

namespace Tests\app\Models;

use App\Models\Admin\PermissionModel;
use CodeIgniter\Test\CIUnitTestCase;

class PermissionModelTest extends CIUnitTestCase
{
    protected $permissionModel;

    protected function setUp(): void
    {
        parent::setUp();
        // Initialize the model
        $this->permissionModel = new PermissionModel();
    }

    public function testAddNewPermission()
    {
        $testData = [
            'permission_name' => 'Test_Permission',
            'r_module_id' => 1,
            'routes_url' => 'test/test',
        ];

        // Insert the meeting details using the model function
        $result = $this->permissionModel->addNewPermission($testData);

        // Assertions to verify the expected results
        $this->assertTrue($result);
    }

    public function testAddNewPermissionInvalidInput()
    {
        $testData = [
            'permission_name' => 'Test_Permission',
            'r_module_id' => 1,
        ];

        // Insert the meeting details using the model function
        $result = $this->permissionModel->addNewPermission($testData);

        // Assertions to verify the expected results
        $this->assertFalse($result);
    }

    public function testGetPermissions()
    {
        $result = $this->permissionModel->getPermissions();

        // Check that the result is an array
        $this->assertIsArray($result);

        // Check if the array is not empty
        $this->assertNotEmpty($result);

        $this->assertEquals("ADD_BACKLOG", $result[0]['permission_name']);
    }

    public function testDeletePermission()
    {
        $testId = 50;

        $result = $this->permissionModel->deletePermission($testId);

        $this->assertTrue($result);
    }

    public function testGetPermissionDetailsById()
    {
        $testId = 20;
        $result = $this->permissionModel->getPermissionDetailsById($testId);

        $this->assertIsArray($result);

        $this->assertNotEmpty($result);

        $expected_output = [
            'permission_name' => 'VIEW_SPRINT_DETAILS',
            'r_module_id' => 8,
            'routes_url' => 'sprint/navsprintview'
        ];
        $this->assertEquals($expected_output, $result);
    }

    public function testGetPermissionDetailsByIdInvalidInput()
    {
        $testId = "asaad";
        $result = $this->permissionModel->getPermissionDetailsById($testId);

        $this->assertIsArray($result);

        $this->assertEmpty($result);

        $expected_output = [];
        $this->assertEquals($expected_output, $result);
    }
}