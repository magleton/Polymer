<?php
/**
 * User: macro chen <chen_macro@163.com>
 * Date: 17-2-17
 * Time: 下午1:13
 */

namespace Polymer\Service;

use Exception;
use Polymer\Boot\Application;
use Psr\Http\Message\ServerRequestInterface;

class Service
{
    /**
     * 请求对象
     *
     * @var ServerRequestInterface
     */
    protected ServerRequestInterface $request;

    /**
     * 全局应用
     *
     * @var Application
     */
    protected Application $application;

    /**
     * 验证规则
     *
     * @var array
     */
    protected array $rules = [];

    /**
     * Service constructor.
     *
     * @param array $params
     */

    public function __construct(array $params = [])
    {
        $this->application = $params['app'] ?? app();
        $this->request = $params['request'] ?? $this->application->component('request');
    }

    /**
     * 验证字段的值
     *
     * @param array $data 需要验证的数据
     * @param array $rules 验证数据的规则
     * @param array $groups 验证组
     * @param string $key 存储错误信息的键
     * @return $this
     * @throws Exception
     */
    protected function validate(array $data = [], array $rules = [], array $groups = [], $key = 'error')
    {
        try {
            $rules = $rules ?: $this->getProperty('rules');
            $this->application->component('biz_validator')->validateField($data, $rules, $groups, $key);
        } catch (Exception $e) {
            throw $e;
        }
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
}