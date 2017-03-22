<?php
/**
 * User: macro chen <macro_fengye@163.com>
 * Date: 16-12-12
 * Time: 上午8:55
 */

namespace Polymer\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;
use Exception;
use Polymer\Boot\Application;
use Polymer\Exceptions\EntityValidateErrorException;
use Polymer\Exceptions\PresenterException;
use Polymer\Utils\Constants;

class Repository extends EntityRepository
{
    /**
     * View presenter instance
     *
     * @var mixed
     */
    protected $presenterInstance;

    /**
     * 全局应用实例
     *
     * @var Application
     */
    protected $app = null;

    /**
     * Initializes a new <tt>EntityRepository</tt>.
     *
     * @param EntityManager $em The EntityManager to use.
     * @param Mapping\ClassMetadata $class The class descriptor.
     */
    public function __construct(EntityManager $em, Mapping\ClassMetadata $class)
    {
        parent::__construct($em, $class);
        $this->app = app();
    }

    /**
     * 验证查询字段的值
     *
     * @param array $data 需要验证的数据
     * @param array $rules 验证数据的规则
     * @throws Exception
     * @return $this
     */
    public function validate(array $data = [], array $rules = [])
    {
        $validator = app()->component('validator')->setProperty('validateRules', $rules);
        $validator->validate(Constants::MODEL_FIELD, $data);
        if (app()->component('error_collection')->get('error')) {
            throw new EntityValidateErrorException('实体验证错误!');
        }
        return $this;
    }

    /**
     * Prepare a new or cached presenter instance
     *
     * @param $entity
     * @return mixed
     * @throws PresenterException
     */
    public function present($entity)
    {
        if (!$this->presenter || !class_exists($this->presenter)) {
            throw new PresenterException('Please set the $presenter property to your presenter path.');
        }
        if (!$this->presenterInstance) {
            $this->presenterInstance = new $this->presenter($entity);
        }
        return $this->presenterInstance;
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
