<?php

namespace App\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\URI;
use CodeIgniter\HTTP\UserAgent;
use Config\App;

class SprintControllerTest extends CIUnitTestCase
{
    use ControllerTestTrait;
    // use DatabaseTestTrait;

    protected $backlogModel;
    protected $sprintModel;
    protected $historyModel;
    protected $notesModelObj;
    protected $sprintRestrospectiveObj;
    protected $scrumSprintModelobj;
    protected $sprintTaskModelobj;
    protected $sprintPlanningObj;
    protected $scrumDiaryObj;
    protected $sprintReviewObj;
    protected $sprintUserModelobj;
    protected $codeReviewObj;
    protected $taskModelObj;
    protected $dbMock;
    protected $sprintModelMock;

    protected function setUp(): void
    {
        parent::setUp();

        // Create duplicate objects for the models
        $this->adminModelMock = $this->createMock(\App\Models\Admin\AdminModel::class);
        $this->roleModelMock = $this->createMock(\App\Models\Admin\RoleModel::class);
        $this->backlogModelMock = $this->createMock(\App\Models\Backlog\BacklogModel::class);
        $this->sprintModelMock = $this->createMock(\App\Models\SprintModel::class);
        $this->historyModelMock = $this->createMock(\App\Models\HistoryModel::class);
        $this->notesModelObjMock = $this->createMock(\App\Models\Sprint\NoteModel::class);
        $this->codeReviewObjMock = $this->createMock(\App\Models\Sprint\CodeReviewModel::class);
        $this->sprintPlanningObjMock = $this->createMock(\App\Models\Sprint\SprintPlanning::class);
        $this->sprintUserModelobjMock = $this->createMock(\App\Models\Sprint\SprintUser::class);
        $this->sprintTaskModelobjMock = $this->createMock(\App\Models\Sprint\SprintTask::class);
        $this->scrumSprintModelobjMock = $this->createMock(\App\Models\Sprint\ScrumSprintModel::class);
        $this->scrumDiaryObjMock = $this->createMock(\App\Models\Sprint\DailyScrumModel::class);
        $this->sprintReviewObjMock = $this->createMock(\App\Models\Sprint\SprintReview::class);
        $this->sprintRestrospectiveObjMock = $this->createMock(\App\Models\Sprint\SprintRetrospective::class);
        $this->taskModelObjMock = $this->createMock(\App\Models\Backlog\TaskModel::class);

        // Creating an instance of the controller with mock models
        $this->controller = new \App\Controllers\SprintController();

        // Inject the mock models into the controller
        $this->setPrivateProperty($this->controller, 'backlogModel', $this->backlogModel);
        $this->setPrivateProperty($this->controller, 'sprintModel', $this->sprintModel);
        $this->setPrivateProperty($this->controller, 'historyModel', $this->historyModel);
        $this->setPrivateProperty($this->controller, 'notesModelObj', $this->notesModelObj);
        $this->setPrivateProperty($this->controller, 'codeReviewObj', $this->codeReviewObj);
        $this->setPrivateProperty($this->controller, 'sprintPlanningObj', $this->sprintPlanningObj);
        $this->setPrivateProperty($this->controller, 'sprintUserModelobj', $this->sprintUserModelobj);
        $this->setPrivateProperty($this->controller, 'sprintTaskModelobj', $this->sprintTaskModelobj);
        $this->setPrivateProperty($this->controller, 'scrumSprintModelobj', $this->scrumSprintModelobj);
        $this->setPrivateProperty($this->controller, 'scrumDiaryObj', $this->scrumDiaryObj);
        $this->setPrivateProperty($this->controller, 'sprintReviewObj', $this->sprintReviewObj);
        $this->setPrivateProperty($this->controller, 'sprintRestrospectiveObj', $this->sprintRestrospectiveObj);
        $this->setPrivateProperty($this->controller, 'taskModelObj', $this->taskModelObj);
    }

    public function testGetSprintList()
    {
        // Set the URI for the request
        $result = $this->withURI('http://localhost:8080/sprint/sprintlist')
            ->controller(\App\Controllers\SprintController::class) // controller name
            ->execute('getSprintList'); // method name to check in the controller

        $this->assertTrue($result->isOK());
    }

    public function testGetProductTasks()
    {
        // Set the URI for the request
        $result = $this->withURI('http://localhost:8080/sprint/getProductTasks')
            ->controller(\App\Controllers\SprintController::class) // controller name
            ->execute('getProductTasks'); // method name to check in the controller

        $this->assertTrue($result->isOK());
    }

    public function testGetSprintPlanning()
    {
        // Duplicate the GET data for the test
        $testData = ['id' => 1];

        // creating the POST form Data to send to controller
        $uri = new URI('http://localhost:8080/sprint/sprintplanning');
        $config = new App();
        $request = new IncomingRequest($config, $uri, null, new UserAgent());
        $request->withMethod('get');
        $request->setGlobal('get', $testData);

        // creating connection with model to fetch
        $this->sprintModelMock->method('getSprintPlanning')
            ->with($this->equalTo($testData))
            ->willReturn(true);

        // preparing the POST to send to the function
        $result = $this->withRequest($request)
            ->controller(\App\Controllers\SprintController::class)
            ->execute('getSprintPlanning');

        // Assert that the result of the query execution is OK
        $this->assertTrue($result->isOK());

        $this->assertJsonStringEqualsJsonString(json_encode(['success' => true]), $result->getJSON());
    }
}