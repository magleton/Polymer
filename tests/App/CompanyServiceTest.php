<?php
/**
 * User: macro chen <chen_macro@163.com>
 * Date: 17-3-30
 * Time: 下午2:18
 */

namespace Polymer\Tests\App;

use Polymer\Testing\TestCase;
use Polymer\Tests\Entity\Models\Company;
use Polymer\Tests\Services\CompanyService;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;

class CompanyServiceTest extends TestCase
{
    /**
     * 模型对象
     *
     * @var CompanyService
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

    public function testGetData()
    {
        $array =  $this->service->getData();
        $this->assertInternalType('array', $array);
    }
}
