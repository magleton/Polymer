<?php
namespace Polymer\Utils;

use Polymer\Boot\Application;
use Doctrine\Common\EventManager;

class CoreUtils
{
    const ENTITY = "entityManager";

    const REDIS = "redis";
    const MEMCACHE = "memcache";
    const MEMCACHED = 'memcached';


    /**
     * 根据不同的数据库链接类型，实例化不同的数据库链接对象
     * @param $dbName string
     * @throws \Doctrine\ORM\ORMException
     * @return \Doctrine\ORM\EntityManager
     */
    public static function getDbInstance($dbName)
    {
        return self::getApplication()->db($dbName);
    }

    /**
     * 获取指定键的配置文件
     *
     * @author macro chen <macro_fengye@163.com>
     * @param string $key
     * @return mixed
     */
    public static function getConfig($key)
    {
        return self::getApplication()->config($key);
    }

    /**
     * 添加自定义监听器
     *
     * @author macro chen <macro_fengye@163.com>
     * @param array $params
     * @throws \Exception
     * @return EventManager
     */
    public static function addEvent(array $params = [])
    {
        return self::getApplication()->addEvent($params);
    }

    /**
     * 添加自定义订阅器
     *
     * @author macro chen <macro_fengye@163.com>
     * @param array $params
     * @throws \Exception
     * @return EventManager
     */
    public static function addSubscriber(array $params = [])
    {
        return self::getApplication()->addSubscriber($params);
    }

    /**
     * 获取拥有命名明空间的缓存实例
     *
     * @param $cacheType
     * @param array $params
     * @deprecated
     * @throws \Exception
     * @return mixed
     */
    public static function getCacheInstanceHaveNamespace($cacheType, array $params = [])
    {
        return self::getApplication()->getCacheInstanceHaveNamespace($cacheType, $params);
    }

    /**
     * 获取指定组件名字的对象
     *
     * @param $componentName
     * @param array $param
     * @return mixed|null
     */
    public static function getContainer($componentName, $param = [])
    {
        return self::getApplication()->component($componentName, $param);
    }

    /**
     * 获取应用实例
     *
     * @author macro chen <macro_fengye@163.com>
     * @return static
     */
    public static function getApplication()
    {
        return Application::getInstance();
    }
}