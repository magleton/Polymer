<?php
/**
 * User: macro chen <macro_fengye@163.com>
 * Date: 17-3-7
 * Time: 上午9:57
 */

namespace Polymer\Validator;

use Symfony\Component\Validator\Validator\RecursiveValidator;
use Polymer\Boot\Application;
use Polymer\Utils\Constants;
use Symfony\Component\Validator\Exception\NoSuchMetadataException;

class BizValidator
{
    /**
     * 应用APP
     *
     * @var Application
     */
    protected $app = null;

    /**
     * 验证组件
     *
     * @var RecursiveValidator
     */
    protected $validator = null;

    /**
     * Validator constructor.
     */
    public function __construct()
    {
        $this->app = app();
        $this->validator = $this->app->component('validator');
    }

    /**
     * 根据自定义的规则验证数据字段
     *
     * @param array $data 验证数据
     * @param array $rules 验证规则
     * @param string $key 错误信息的key，用于获取错误信息
     * @return boolean
     */
    public function verifyField(array $data = [], array $rules = [], $key = 'error')
    {
        $returnData = [];
        foreach ($data as $property => $val) {
            if (isset($rules[$property])) {
                $constraints = $this->propertyConstraints($property, $rules);
                $errors = $this->validator->validate($val, $constraints);
                if (count($errors)) {
                    foreach ($errors as $error) {
                        $returnData[$property] = $error->getMessage();
                    }
                }
            }
        }
        if ($returnData) {
            $this->app->component('error_collection')->set($key, $returnData);
            return false;
        }
        return true;
    }

    /**
     * 给对象赋值并且验证对象的值是否合法
     *
     * @param Object $validateObject 要验证的对象
     * @param array $rules 验证规则
     * @param string $key 错误信息的key,用于获取错误信息
     * @throws NoSuchMetadataException
     * @return boolean
     */
    public function verifyObject($validateObject, array $rules = [], $key = 'error')
    {
        try {
            $classMetadata = $this->validator->getMetadataFor($validateObject);
            if ($rules) {
                foreach ($classMetadata->getReflectionClass()->getProperties() as $val) {
                    $property = $val->getName();
                    if (isset($rules[$property])) {
                        $constraints = $this->propertyConstraints($property, $rules);
                        $classMetadata->addPropertyConstraints($property, $constraints);
                    }
                }
            }
            $errors = $this->validator->validate($validateObject);
            if (count($errors)) {
                foreach ($errors as $error) {
                    $returnData[$error->getPropertyPath()] = $error->getMessage();
                }
                $this->app->component('error_collection')->set($key, $returnData);
                return false;
            }
            return true;
        } catch (NoSuchMetadataException $e) {
            throw $e;
        }
    }

    /**
     * 实例化指定属性的验证器类
     *
     * @param string $property
     * @param array $rules 验证规则
     * @return array
     */
    private function propertyConstraints($property, array $rules)
    {
        $constraints = [];
        foreach ($rules[$property] as $cls => $params) {
            if (is_numeric($cls)) {
                $cls = $params;
                $params = null;
            }
            $class = $this->getConstraintClass($cls);
            if (!empty(trim($class))) {
                $constraints[] = new $class($params);
            }
        }
        return $constraints;
    }

    /**
     * 根据类名获取类的全名
     *
     * @param string $cls
     * @return string
     */
    private function getConstraintClass($cls = '')
    {
        $class = '';
        if (class_exists('\\Symfony\\Component\\Validator\\Constraints\\' . $cls)) {
            $class = '\\Symfony\\Component\\Validator\\Constraints\\' . $cls;
            return $class;
        } elseif (class_exists(APP_NAME . '\\Constraints\\' . $cls)) {
            $class = APP_NAME . '\\Constraints\\' . $cls;
            return $class;
        } elseif (class_exists('Polymer\\Constraints\\' . $cls)) {
            $class = 'Polymer\\Constraints\\' . $cls;
            return $class;
        }
        return $class;
    }
}