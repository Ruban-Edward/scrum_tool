<?php

namespace Tests\app\Models;

use App\Models\Admin\ProductOwnerModel;
use CodeIgniter\Test\CIUnitTestCase;

class ProductOwnerModelTest extends CIUnitTestCase
{
    protected $productOwnerModel;
    public function setUp(): void
    {
        parent::setUp();
        $this->productOwnerModel = new ProductOwnerModel();
    }

    public function testSetProductOwner()
    {
        $testData = [
            'r_product_id' => 1,
            'r_user_id' => 802
        ];

        $result = $this->productOwnerModel->setProductOwner($testData);

        // Assertions to verify the expected results
        $this->assertTrue($result);
    }

    public function testSetProductOwnerInvalidInput()
    {
        $testData = [
            'r_product_id' => 1,
        ];

        $result = $this->productOwnerModel->setProductOwner($testData);

        // Assertions to verify the expected results
        $this->assertTrue($result);
    }

    public function testGetProductOwner()
    {
        $testId = 1;
        $result = $this->productOwnerModel->getProductOwner($testId);

        $this->assertIsArray($result);

        $this->assertNotEmpty($result);

        $expected_output = [
            ['r_user_id' => 802]
        ];
        $this->assertEquals($expected_output, $result);
    }

    public function testGetProductOwnerInvalidInput()
    {
        $testId = "abc";
        $result = $this->productOwnerModel->getProductOwner($testId);

        $this->assertIsArray($result);

        $this->assertEmpty($result);

        $expected_output = [];
        $this->assertEquals($expected_output, $result);
    }
}