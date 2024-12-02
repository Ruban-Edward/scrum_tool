<?php


namespace App\Controllers;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\URI;
use CodeIgniter\HTTP\UserAgent;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use App\Models\Backlog\TaskModel;
use Config\App;

class TaskControllerTest extends CIUnitTestCase{

    use ControllerTestTrait;

    protected $taskModelMock;
    public function setUp(): void
    {
        parent::setUp();

        $this->controller = new TaskController();


        $this->taskModelMock =$this->createMock(TaskModel::class);

        $this->setPrivateProperty($this->controller,"taskModel", $this->taskModelMock);

        $this->mockSession();

        $_SESSION['employee_id'] = 31;
    }



    public function testAddTask(){
        $pId = 51;
        $pblId = 5;
        $usId = 29;
        $input = [
            'task_title' => 'task two',
            'task_description' => 'task two description',
            'task_status' => 1,
            'priority' => 'L',
            'assignee_id' => 41,
            'estimated_hours' => 12,
            'start_date' => '2024-09-05',
            'end_date' => '2024-09-07',
            'completed_percentage' => 12
        ];

        $uri = new URI('http://localhost:8080/backlog/addTasks');
        $config = new App();
        $request = new IncomingRequest($config, $uri, null, new UserAgent());
        $request->withMethod('post');
        $request->setGlobal('post', $input);

        // Preparing the POST to send to the function
        $result = $this->withRequest($request)
            ->controller(TaskController::class)
            ->execute('addTasks',$pId, $pblId, $usId);



        // Assert that the result of the query execution is OK
        $this->assertTrue($result->isOK());
        $this->assertJsonStringEqualsJsonString(json_encode(["message" => "Task Created Successfully", 'success' => true]), $result->getJSON());
    }

    public function testUpdateTask(){
        $pId = 51;
        $pblId = 5;
        $taskId = 21366;
        $input = [
            'task_title' => 'task two',
            'task_description' => 'task two description',
            'task_status' => 1,
            'priority' => 'L',
            'assignee_id' => 41,
            'estimated_hours' => 12,
            'start_date' => '2024-09-05',
            'end_date' => '2024-09-07',
            'completed_percentage' => 1001
        ];

        $uri = new URI('http://localhost:8080/backlog/addTasks');
        $config = new App();
        $request = new IncomingRequest($config, $uri, null, new UserAgent());
        $request->withMethod('post');
        $request->setGlobal('post', $input);

        // Preparing the POST to send to the function
        $result = $this->withRequest($request)
            ->controller(TaskController::class)
            ->execute('updateTasks',$pId, $pblId, $taskId);



        // Assert that the result of the query execution is OK
        $this->assertTrue($result->isOK());
        $this->assertJsonStringEqualsJsonString(json_encode(["message" => "Task updated successfully", 'success' => true]), $result->getJSON());
    }
    public function testDeleteTask(){
        $pId = 51;
        $pblId = 5;
        $taskId = 21365;
        $uri = new URI('http://localhost:8080/backlog/deletetask/');
        $config = new App();
        $request = new IncomingRequest($config,$uri,null,new UserAgent());

        $result = $this->withRequest($request)
        ->controller(TaskController::class)
        ->execute('deleteTasks',$pId,$pblId,$taskId);

        $this->assertTrue($result->isOK());

        $this->assertJsonStringEqualsJsonString(json_encode(['success' => true]), $result->getJSON());
    }


}