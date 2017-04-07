<?php
/**
 * User: macro chen <chen_macro@163.com>
 * Date: 17-2-17
 * Time: 下午1:13
 */
namespace Polymer\Service;

use Polymer\Boot\Application;
use Slim\Http\Request;

class Service
{
    /**
     * 请求对象
     *
     * @var Request
     */
    protected $request;

    /**
     * 全局应用
     *
     * @var Application
     */
    protected $app;

    /**
     * Service constructor.
     *
     * @param array $params
     */

    public function __construct(array $params = [])
    {
        $this->app = isset($params['app']) ? $params['app'] : app();
        $this->request = isset($params['request']) ? $params['request'] : $this->app->component('request');
    }

    /**
     * 给对象新增属性
     *
     * @param $propertyName
     * @param $value
     * @return $this
     */
    protected function setProperty($propertyName, $value)
    {
        $this->$propertyName = $value;
        return $this;
    }

    /**
     * 获取对象属性
     *
     * @param $propertyName
     * @return mixed
     */
    protected function getProperty($propertyName)
    {
        if (isset($this->$propertyName)) {
            return $this->$propertyName;
        }
        return null;
    }
}