<?php
/**
 * User: macro chen <chen_macro@163.com>
 * Date: 17-3-31
 * Time: 上午9:33
 */

namespace Polymer\Tests\App;

use Polymer\Testing\TestCase;
use Polymer\Tests\Services\OrderService;

class OrderServiceTest extends TestCase
{
    /**
     * @var OrderService
     */
    protected $service = null;

    public function testAdd()
    {
        $this->assertGreaterThan(1 , $this->service->add());
    }

    protected function setUp()
    {
        parent::setUp();
        $this->service = $this->app->service('order', 'Polymer\Tests\Services', []);
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->service = null;
    }
    
    
}
