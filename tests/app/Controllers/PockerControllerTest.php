<?php

namespace App\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\URI;
use CodeIgniter\HTTP\UserAgent;
use Config\App;

class PockerControllerTest extends CIUnitTestCase
{
    use ControllerTestTrait;
    protected function setUp(): void
    {
        parent::setUp();
        $this->mockSession();
        $_SESSION['employee_id'] = 31;
        // $this->controller = new \App\Controllers\PockerController();
    }

    public function testInsertPokerPlanning()
    {
        // Duplicate the POST data for the test
        $testData = [
            'fibonacciUserStoryId' => 1,
            'fibonacciNumber' => 8,
            'poker_description' => "dummy",
        ];
        // creating the POST form Data to send to controller
        $uri = new URI('http://localhost:8080/backlog/insertPokerPlanning');
        $config = new App();
        $request = new IncomingRequest($config, $uri, null, new UserAgent());
        $request->withMethod('post');
        $request->setGlobal('post', $testData);
        // preparing the POST to send to the function
        $result = $this->withRequest($request)
            ->controller(\App\Controllers\PockerController::class)
            ->execute('insertPokerPlanning');
        // Assert that the result of the query execution is OK
        $this->assertTrue($result->isOK());  
        $expectedResult = json_encode(['success' => true, 'message' => 'Poker planning added successfully']);  
        $this->assertJsonStringEqualsJsonString($expectedResult, $result->getJSON());
    }

    public function testGetPokerPlanning()
    {
        // Duplicate the POST data for the test
        $testData = [
            'userStory' => [1]
        ];
        // creating the POST form Data to send to controller
        $uri = new URI('http://localhost:8080/backlog/getpoker');
        $config = new App();
        $request = new IncomingRequest($config, $uri, null, new UserAgent());
        $request->withMethod('post');
        $request->setGlobal('post', $testData);
        // preparing the POST to send to the function
        $result = $this->withRequest($request)
            ->controller(\App\Controllers\PockerController::class)
            ->execute('getPokerPlanning');
        // Assert that the result of the query execution is OK
        $this->assertTrue($result->isOK());
        // $expectedResult = json_encode([
        //     'success' => true, 
        //     'data' => [[[
        //             "added_date"=> "2024-09-03 13:04:08",
        //             "card_points"=> "8",
        //             "name"=> "Senthilkumar L ISS182",
        //             "r_user_story_id"=> "1",
        //             "reason"=> "dummy",
        //             "reveal" => "N"
        //     ]]]
        // ]);  
        // $this->assertJsonStringEqualsJsonString($expectedResult, $result->getJSON());
    }

    public function testUpdatePokerReveal()
    {
        // Duplicate the POST data for the test
        $testData = [
            'userStory' => 1
        ];
        // creating the POST form Data to send to controller
        $uri = new URI('http://localhost:8080/backlog/updatereveal');
        $config = new App();
        $request = new IncomingRequest($config, $uri, null, new UserAgent());
        $request->withMethod('post');
        $request->setGlobal('post', $testData);
        // preparing the POST to send to the function
        $result = $this->withRequest($request)
            ->controller(\App\Controllers\PockerController::class)
            ->execute('updatePokerReveal');
        // Assert that the result of the query execution is OK
        $this->assertTrue($result->isOK());
        // $expectedResult = json_encode([
        //     'success' => true, 
        //     'message' => true
        // ]);  
        // $this->assertJsonStringEqualsJsonString($expectedResult, $result->getJSON());
    }

    public function testAddUserStoryPoint(){
        // Duplicate the POST data for the test
        $testData = [
            'userStoryId' => 1,
            'storyPoint'=> 8
        ];
        // creating the POST form Data to send to controller
        $uri = new URI('http://localhost:8080/backlog/addUserStoryPoint');
        $config = new App();
        $request = new IncomingRequest($config, $uri, null, new UserAgent());
        $request->withMethod('post');
        $request->setGlobal('post', $testData);
        // preparing the POST to send to the function
        $result = $this->withRequest($request)
            ->controller(\App\Controllers\UserStoryController::class)
            ->execute('addUserStoryPoint');
        // Assert that the result of the query execution is OK
        $this->assertTrue($result->isOK());
    }

}