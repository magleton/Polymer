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
    protected $model = null;

    protected function setUp()
    {
        parent::setUp();
        $this->model = $this->app->model('company', [], 'Polymer\\Tests\\Models');
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->model = null;
    }

    /**
     * 测试保存数据
     */
    public function testSave()
    {
        $data = [
            'name' => 'test',
            'address' => 'chengdu',
            'phone' => '13800138000',
            'created' => time(),
            'updated' => time()
        ];
        $this->assertGreaterThan(1, $this->model->save($data));
    }

    public function testUpdate()
    {
        $data = ['name' => 'update'];
        $this->assertEquals(15, $this->model->update($data));
    }
}
