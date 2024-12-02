<?php

namespace Tests\app\Models;

use App\Models\Admin\RoleModel;
use CodeIgniter\Test\CIUnitTestCase;

class RoleModelTest extends CIUnitTestCase
{
    protected $roleModel;
    public function setUp(): void
    {
        parent::setUp();
        $this->roleModel = new RoleModel();
    }

    public function testGetRoles()
    {
        $result = $this->roleModel->getRoles();

        // Check that the result is an array
        $this->assertIsArray($result);

        // Check if the array is not empty
        $this->assertNotEmpty($result);

        $this->assertEquals("Super Admin", $result[0]['role_name']);
    }

    public function testInsertRole()
    {
        $testData = [
            'role_name' => 'a'
        ];

        // Insert the meeting details using the model function
        $result = $this->roleModel->insertRole($testData);

        // Assertions to verify the expected results
        $this->assertTrue($result);
        $this->assertIsBool($result);
        $this->assertEquals(true, $result);
    }

    public function testDeleteRole()
    {
        $testData = 35;

        // Insert the meeting details using the model function
        $result = $this->roleModel->deleteRole($testData);

        // Assertions to verify the expected results
        $this->assertTrue($result);
        $this->assertIsBool($result);
        $this->assertEquals(true, $result);
    }
}