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
     * @param Request|null $request
     * @param Application|null $app
     */

    public function __construct(Request $request = null, Application $app = null)
    {
        $this->request = $request ?: app()->component('request');
        $this->app = $app ?: app();
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

    /**
     * is utilized for reading data from inaccessible members.
     *
     * @param $name string
     * @return mixed
     * @link http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members
     */
    public function __get($name)
    {
        return isset($this->$name) ? $this->$name : null;
    }

    /**
     * run when writing data to inaccessible members.
     *
     * @param $name string
     * @param $value mixed
     * @return $this
     * @link http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members
     */
    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    /**
     * is triggered by calling isset() or empty() on inaccessible members.
     *
     * @param $name string
     * @return bool
     * @link http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members
     */
    public function __isset($name)
    {
        return isset($this->$name);
    }
}