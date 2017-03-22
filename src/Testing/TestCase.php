<?php
/**
 * User: macro chen <macro_fengye@163.com>
 * Date: 2017/3/11
 * Time: 21:08
 */

namespace Polymer\Testing;

use GuzzleHttp\Client;
use Polymer\Boot\Application;

class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Application
     */
    protected $app;

    /**
     * Constructs a test case with the given name.
     *
     * @param string $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->app = app();
        $this->client = new Client($this->app->config('testing.config', []));
    }
}