<?php
/**
 * User: macro chen <macro_fengye@163.com>
 * Date: 2017/3/11
 * Time: 17:49
 */

use Blog\Services\TestService;

class TestServiceTest extends \Polymer\Testing\TestCase
{
    /**
     * @var TestService
     */
    private $service = null;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->service = $this->app->service('test');
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->service = null;
    }

    public function testAdd()
    {
        $content = $this->client->request('POST', '/', [
            'form_params' => [
                'address' => 'dhda',
            ]
        ]);
    }
}
