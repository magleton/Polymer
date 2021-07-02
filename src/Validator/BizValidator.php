<?php
/**
 * User: macro chen <macro_fengye@163.com>
 * Date: 17-3-7
 * Time: 上午9:57
 */

namespace Polymer\Validator;

use Exception;
use Polymer\Exceptions\FieldValidateErrorException;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use Polymer\Boot\Application;
use Symfony\Component\Validator\Exception\NoSuchMetadataException;

class BizValidator
{
    /**
     * 应用APP
     *
     * @var ?Application
     */
    protected ?Application $application = null;

    /**
     * 验证组件
     *
     * @var ?RecursiveValidator
     */
    protected ?RecursiveValidator $validator = null;

    /**
     * Validator constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        $this->application = app();
        $this->validator = $this->application->component('validator');
    }

    /**
     * 根据自定义的规则验证数据字段
     *
     * @param array $data 验证数据
     * @param array $rules 验证规则
     * @param array|null $groups 验证组
     * @param string $key 错误信息的key，用于获取错误信息
     * @return null
     * @throws FieldValidateErrorException
     */
    public function validateField(array $data = [], array $rules = [], array $groups = null, string $key = 'error'): void
    {
        $errorData = [];
        foreach ($data as $property => $val) {
            if (isset($rules[$property])) {
                $constraints = $this->propertyConstraints($property, $rules);
                $errors = $this->validator->validate($val, $constraints, $groups);
                if (count($errors)) {
                    foreach ($errors as $error) {
                        $errorData[$property] = $error->getMessage();
                    }
                }
            }
        }
        if ($errorData) {
            $this->application->component('error_collection')->set($key, $errorData);
            throw new FieldValidateErrorException('数据验证失败');
        }
    }

    /**
     * 实例化指定属性的验证器类
     *
     * @param string $property
     * @param array $rules 验证规则
     * @return array
     */
    private function propertyConstraints(string $property, array $rules): array
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
    private function getConstraintClass(string $cls = '')
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

    /**
     * 给对象赋值并且验证对象的值是否合法
     *
     * @param Object $validateObject 要验证的对象
     * @param array $rules 验证规则
     * @param array|null $groups 验证组
     * @return boolean
     */
    public function validateObject(object $validateObject, array $rules = [], array $groups = null): bool
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
            return $this->validator->validate($validateObject, null, $groups);
        } catch (NoSuchMetadataException $e) {
            throw $e;
        }
    }
}
