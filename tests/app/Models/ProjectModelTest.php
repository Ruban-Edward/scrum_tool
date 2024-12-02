<?php
namespace Tests\app\Models;
use CodeIgniter\Test\CIUnitTestCase;

class ProjectModelTest extends CIUnitTestCase
{
    protected $model;

    protected function setUp(): void
    {
        parent::setUp();
        // Initialize the model
        $this->model = service('projects');
    }

    //test to get all products from redmine 
    public function testGetAllProductsFromRedmine()
    {
        $result = $this->model->getAllProductsFromRedmine();
        // print_r($result);
        $this->assertIsArray($result);


    }
    //unit testing to get all the product users from the redmine 
    public function testGetAllProductUsersFromRedmine()
    {
        $result = $this->model->getAllProductUsersFromRedmine();
        $this->assertIsArray($result);
    }
    //testing whether the comming data is correct or not 
    public function testWhetherProductDataCorrectOrNot(){
        $result=$this->model->getAllProductsFromRedmine();
        // $this->assertIsArray($result);
        $this->assertArrayHasKey("external_project_id",$result[0]);
        $this->assertContains("AgencyAuto - One Order product",$result[60]);
        // print_r($result);



    }

    public function testWhetherProductUserDataCorrectOrNot(){
        $result=$this->model->getAllProductUsersFromRedmine();
        // print_r($result);
        // $this->assertArrayHasKey("")
        $this->assertContains("313",$result[5288]);
        $this->assertArrayHasKey("external_user_id",$result[0]);
        $this->assertArrayHasKey("external_project_id",$result[0]);

    }

}