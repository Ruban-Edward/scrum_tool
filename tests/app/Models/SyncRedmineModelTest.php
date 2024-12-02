<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Models\SyncRedmineModel;
use CodeIgniter\Test\DatabaseTestTrait;

class SyncRedmineModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    // protected $refresh = true;
    // protected $seed = 'Tests\Support\Database\Seeds\YourSeederClass'; // Replace with your seeder if needed

    /**
     * @var SyncRedmineModel
     */
    protected $syncModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->syncModel = new SyncRedmineModel();
    }

    //test function to check the login sync activity 

    public function testLogSyncActivitySuccess()
    {
        $result = $this->syncModel->logSyncActivity('task_sync', true, 123);

        $this->assertTrue($result);
        $this->syncModel->logSyncActivity('tasksync',1,36);
    }

    //test to check the log sync failure 
    public function testLogSyncActivityFailure()
    {
        $result = $this->syncModel->logSyncActivity('task_sync', false, null);

        $this->assertTrue($result);
        $this->syncModel->logSyncActivity('tasksync',1,0);
    }

    //test to get all products from the local


    public function testGetAllProductUsersFromLocal()
    {
        $result = $this->syncModel->getAllProductUsersFromLocal();

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

    //testing to update the product user sync 

    public function testUpdateProductUserSync()
    {
        $data = [
            ['external_project_id' => 1, 'external_user_id' => 101],
            ['external_project_id' => 2, 'external_user_id' => 102],
        ];

        $result = $this->syncModel->updateProductUserSync($data);

        $this->assertTrue($result);
    
    }

    //test to the update the product sync 

    public function testUpdateProductSync()
    {
        $data = [
            [
                'external_project_id' => 1,
                'product_name' => 'Test Product',
                'parent_id' => 0,
                'created_date' => '2024-09-01 00:00:00',
                'updated_date' => '2024-09-01 00:00:00'
            ]
        ];

        $result = $this->syncModel->updateProductSync($data);

        $this->assertTrue($result);
        
    }

    public function testGetLocalProduct()
    {
        $result = $this->syncModel->getLocalProduct();

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }
     //test to update the task sync 
    public function testUpdateTaskSync()
    {
        $data = [
            [
                'r_user_story_id' => 1,
                'task_title' => 'Test Task',
                'task_description' => 'This is a test task description',
                'external_reference_task_id' => 'EXT123',
                'priority' => 'High',
                'assignee_id' => 1001,
                'r_user_id_created' => 1002,
                'r_user_id_updated' => 1003,
                'created_date' => '2024-09-01 00:00:00',
                'updated_date' => '2024-09-01 00:00:00',
                'task_status' => 'In Progress',
                'completed_percentage' => 50,
                'start_date' => '2024-09-01',
                'end_date' => '2024-09-10',
                'estimated_hours' => 8,
                'tracker_id' => 1
            ]
        ];

        $result = $this->syncModel->updateTaskSync($data);

        $this->assertTrue($result);
       
    }

    //test to check the last updates 

    public function testGetLastUpdates()
    {
        $result = $this->syncModel->getLastUpdates();

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }
    //test to check customer sync 
    public function testUpdateCustomerSync()
    {
        $data = ['Customer A', 'Customer B'];

        $result = $this->syncModel->updateCustomerSync($data);

        $this->assertTrue($result);
      
    }
    
}

