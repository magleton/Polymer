<?php
/**
 * User: macro chen <chen_macro@163.com>
 * Date: 17-3-30
 * Time: 下午2:18
 */

namespace Polymer\Tests\App;

use Polymer\Testing\TestCase;

class CompanyServiceTest extends TestCase
{
    /**
     * 模型对象
     *
     * @var null
     */
    protected $service = null;

    protected function setUp()
    {
        parent::setUp();
        $this->service = $this->app->service('company', ['request' => null, 'app' => null], 'Polymer\\Tests\\Services');
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->service = null;
    }

    /**
     * 测试保存数据
     */
    public function testSave()
    {
        $this->assertGreaterThan(1, $this->service->save());
    }

    public function testUpdate()
    {
        $this->assertEquals(15, $this->service->update());
    }
}
