<?php
/**
 * User: macro chen <macro_fengye@163.com>
 * Date: 16-12-12
 * Time: 上午8:51
 */

namespace Polymer\Presenter;

abstract class Presenter
{
    /**
     * 实体对象
     *
     * @var Object
     */
    protected $entity;

    /**
     * 构造函数.
     *
     * @param $entity
     */
    public function __construct($entity)
    {
        $this->entity = $entity;
    }

    /**
     * 检索该对象的所有属性
     * is utilized for reading data from inaccessible members.
     *
     * @param $property string
     * @return mixed
     * @link http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members
     */
    public function __get($property)
    {
        $method = 'get' . ucfirst(str_replace(' ', '', lcfirst(ucwords(str_replace('_', ' ', $property)))));
        if (method_exists($this->entity, $method)) {
            return $this->entity->{$method}();
        }
        return '';
    }

    /**
     * run when writing data to inaccessible members.
     *
     * @param $name string
     * @param $value mixed
     * @return void
     * @link http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.members
     */
    public function __set($name, $value)
    {
        // TODO: Implement __set() method.
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
        // TODO: Implement __isset() method.
    }
}