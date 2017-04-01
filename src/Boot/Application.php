<?php
/**
 * User: macro chen <macro_fengye@163.com>
 * Date: 2016/9/21
 * Time: 18:02
 */

namespace Polymer\Boot;

use Doctrine\ORM\ORMException;
use Noodlehaus\Config;
use Noodlehaus\Exception\EmptyDirectoryException;
use Polymer\Providers\InitAppProvider;
use Polymer\Repository\Repository;
use Polymer\Utils\Constants;
use Polymer\Utils\DoctrineExtConfigLoader;
use Slim\Container;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\EventManager;
use Doctrine\ORM\Tools\Setup;
use Interop\Container\Exception\ContainerException;
use Slim\Exception\ContainerValueNotFoundException;

final class Application
{
    /**
     * 整个应用的实例
     *
     * @var $this
     */
    protected static $instance;

    /**
     * 应用的服务容器
     *
     * @var Container
     */
    private $container;

    /**
     * 配置文件对象
     *
     * @var Config $configObject
     */
    private $configObject = null;

    /**
     * 启动WEB应用
     *
     * @author macro chen <macro_fengye@163.com>
     * @throws \Exception
     * @return mixed
     */
    public function start()
    {
        try {
            $this->initEnvironment();
            $this->component('routerFile');
            $this->component('app')->run();
        } catch (\Exception $e) {
            throw $e;
        }
        if ($this->config('app.show_use_memory')) {
            echo '分配内存量 : ' . convert(memory_get_usage(true));
            echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            echo '内存的峰值 : ' . convert(memory_get_peak_usage(true));
        }
        return true;
    }

    /**
     * 启动控制台，包括单元测试及其他的控制台程序(定时任务等...)
     *
     * @author macro chen <macro_fengye@163.com>
     * @throws \Exception
     * @return mixed
     */
    public function startConsole()
    {
        try {
            $this->initEnvironment();
        } catch (\Exception $e) {
            throw $e;
        }
        if ($this->config('app.show_use_memory')) {
            echo '分配内存量 : ' . convert(memory_get_usage(true));
            echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            echo '内存的峰值 : ' . convert(memory_get_peak_usage(true));
        }
        return true;
    }


    /**
     * 初始化应用的环境
     *
     * @author macro chen <macro_fengye@163.com>
     */
    private function initEnvironment()
    {
        if (APPLICATION_ENV === 'production') {
            ini_set('display_errors', 'off');
            error_reporting(0);
        } else {
            ini_set('display_errors', 'on');
            error_reporting(E_ALL);
        }
        set_error_handler('handleError');
        set_exception_handler('handleException');
        register_shutdown_function('handleShutdown');
        $this->container = new Container($this->config('slim'));
        $this->container->register(new InitAppProvider());
        $this->container['application'] = $this;
        static::setInstance($this);
    }

    /**
     * 实例化数据库链接对象
     *
     * @param string $dbName
     * @param mixed $entityFolder 实体文件夹的名字
     * @throws \Doctrine\ORM\ORMException | \InvalidArgumentException | \Exception
     * @return EntityManager
     */
    public function db($dbName = '', $entityFolder = null)
    {
        $dbConfig = $this->config('db.' . APPLICATION_ENV);
        $dbName = $dbName ?: current(array_keys($dbConfig));
        if (isset($dbConfig[$dbName]) && $dbConfig[$dbName] && !$this->component('entityManager-' . $dbName)) {
            $entityFolder = (null !== $entityFolder) ? $entityFolder : $entityFolder = ROOT_PATH . '/entity/Models';
            $configuration = Setup::createAnnotationMetadataConfiguration([
                $entityFolder,
            ], APPLICATION_ENV === 'development', ROOT_PATH . '/entity/Proxies/', null,
                $dbConfig[$dbName]['useSimpleAnnotationReader']);
            DoctrineExtConfigLoader::loadFunctionNode($configuration, DoctrineExtConfigLoader::MYSQL);
            DoctrineExtConfigLoader::load();
            try {
                $entityManager = EntityManager::create($dbConfig[$dbName], $configuration,
                    $this->component('eventManager'));
                $this->container['database_name'] = $dbName;
                $this->container['entityManager-' . $dbName] = $entityManager;
            } catch (\InvalidArgumentException $e) {
                return null;
            }
        }
        return $this->container['entityManager-' . $dbName];
    }

    /**
     * 获取指定键的配置文件
     *
     * @author macro chen <macro_fengye@163.com>
     * @param string $key
     * @param mixed | array $default
     * @return mixed
     */
    public function config($key, $default = null)
    {
        $configPaths = [dirname(__DIR__) . '/Config'];
        if (defined('ROOT_PATH') && file_exists(ROOT_PATH . '/config') && is_dir(ROOT_PATH . '/config')) {
            $configPaths[] = ROOT_PATH . '/config';
        }
        if (defined('APP_PATH') && file_exists(APP_PATH . '/Config') && is_dir(APP_PATH . '/Config')) {
            $configPaths[] = APP_PATH . '/Config';
        }
        try {
            if (null === $this->configObject) {
                $this->configObject = new Config($configPaths);
            }
            return $this->configObject->get($key, $default);
        } catch (EmptyDirectoryException $e) {
            return null;
        } catch (\Exception $e) {
            return $default;
        }
    }

    /**
     * 添加自定义监听器
     *
     * @author macro chen <macro_fengye@163.com>
     * @param array $params
     * @throws \Exception
     * @return EventManager
     */
    public function addEvent(array $params = [])
    {
        try {
            return $this->addEventOrSubscribe($params, 1);
        } catch (\Exception $e) {
            throw $e;
        }
    }


    /**
     * 添加自定义订阅器
     *
     * @author macro chen <macro_fengye@163.com>
     * @param array $params
     * @throws \Exception
     * @return EventManager
     */
    public function addSubscriber(array $params = [])
    {
        try {
            return $this->addEventOrSubscribe($params, 0);
        } catch (\InvalidArgumentException $e) {
            throw $e;
        }
    }

    /**
     * 添加事件监听器或者订阅器
     *
     * @param array $params
     * @param int $listener 0 添加事件订阅器 1 添加事件监听器
     * @return mixed|null
     * @throws ORMException | \InvalidArgumentException | \Exception
     */
    private function addEventOrSubscribe(array $params, $listener)
    {
        $method = $listener ? 'addEventListener' : 'addEventSubscriber';
        $eventManager = $this->component('eventManager');
        foreach ($params as $key => $value) {
            if (!isset($value['class_name'])) {
                throw new \InvalidArgumentException('class_name必须设置');
            }
            $className = $value['class_name'];
            $params = isset($value['params']) ? $value['params'] : [];
            $listener === 1 ? $eventManager->{$method}($key, new $className($params)) : $eventManager->{$method}(new $className($params));
        }
        return $eventManager;
    }

    /**
     * 获取指定组件名字的对象
     *
     * @param $componentName
     * @param array $param
     * @return mixed|null
     */
    public function component($componentName, array $param = [])
    {
        if (!$this->container->has($componentName)) {
            $tmpClassName = ucfirst(str_replace(' ', '', lcfirst(ucwords(str_replace('_', ' ', $componentName)))));
            $providersPath = array_merge($this->config('providersPath'), $this->config('app.providersPath') ?: []);
            $isClassExist = 0;
            foreach ($providersPath as $namespace) {
                $className = $namespace . '\\' . $tmpClassName . 'Provider';
                if (class_exists($className)) {
                    $this->container->register(new $className(), $param);
                    $isClassExist = 1;
                    break;
                }
            }
            if (!$isClassExist) {
                return null;
            }
        }
        try {
            $retObj = $this->container->get($componentName);
            if ($componentName === Constants::REDIS) {
                $database = (isset($param['database']) && $param['database']) ? $param['database'] : 0;
                $retObj->select($database);
            }
            return $retObj;
        } catch (ContainerValueNotFoundException $e) {
            return null;
        } catch (ContainerException $e) {
            return null;
        }
    }

    /**
     * 获取全局可用的应用实例
     *
     * @return static
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static;
        }
        return static::$instance;
    }

    /**
     * 设置全局可用的应用实例
     *
     * @param Application $application
     * @return static
     */
    public static function setInstance($application = null)
    {
        return static::$instance = $application;
    }

    /**
     * 获取业务模型实例
     *
     * @param string $modelName 模型的名字
     * @param array $parameters 实例化时需要的参数
     * @param mixed $modelNamespace 模型命名空间
     * @return mixed
     */
    public function model($modelName, array $parameters = [], $modelNamespace = null)
    {
        $modelNamespace = (null !== $modelNamespace) ? $modelNamespace : $modelNamespace = APP_NAME . '\\Models';
        $className = ucfirst(str_replace(' ', '', lcfirst(ucwords(str_replace('_', ' ', $modelName)))));
        $className = $modelNamespace . '\\' . ucfirst($className) . 'Model';
        if (class_exists($className)) {
            return new $className($parameters);
        }
        return null;
    }

    /**
     * 获取实体模型实例
     * @param $tableName
     * @param mixed $entityNamespace 实体的命名空间
     * @return bool
     */
    public function entity($tableName, $entityNamespace = null)
    {
        $entityNamespace = (null !== $entityNamespace) ? $entityNamespace : $entityNamespace = 'Entity\\Models';
        $className = ucfirst(str_replace(' ', '', lcfirst(ucwords(str_replace('_', ' ', $tableName)))));
        $className = $entityNamespace . '\\' . ucfirst($className);
        if (class_exists($className)) {
            return new $className;
        }
        return null;
    }

    /**
     * 获取EntityRepository
     *
     * @param string $entityName 实体的名字
     * @param string $dbName 数据库的名字
     * @param null $entityFolder 实体文件的路径
     * @param mixed $entityNamespace 实体的命名空间
     * @param mixed $repositoryNamespace Repository的命名空间
     * @throws \Exception
     * @return \Doctrine\ORM\EntityRepository | Repository | NULL
     */
    public function repository($entityName, $dbName = '', $entityFolder = null, $entityNamespace = null, $repositoryNamespace = null)
    {
        $repositoryNamespace = (null !== $repositoryNamespace) ? $repositoryNamespace : $repositoryNamespace = 'Entity\\Repositories';
        $entityNamespace = (null !== $entityNamespace) ? $entityNamespace : $entityNamespace = 'Entity\\Models';
        $className = ucfirst(str_replace(' ', '', lcfirst(ucwords(str_replace('_', ' ', $entityName)))));
        $repositoryClassName = $repositoryNamespace . '\\' . ucfirst($className) . 'Repository';
        if (class_exists($repositoryClassName)) {
            try {
                $dbConfig = $this->config('db.' . APPLICATION_ENV);
                $dbName = $dbName ?: current(array_keys($dbConfig));
                return $this->db($dbName, $entityFolder)->getRepository($entityNamespace . '\\' . ucfirst($className));
            } catch (ORMException $e) {
                return null;
            } catch (\InvalidArgumentException $e) {
                return null;
            }
        }
        return null;
    }

    /**
     * 获取服务组件
     *
     * @param string $serviceName
     * @param array $params
     * @param mixed $serviceNamespace
     * @return null | Object
     */
    public function service($serviceName, array $params = [], $serviceNamespace = null)
    {
        $serviceNamespace = (null !== $serviceNamespace) ? $serviceNamespace : $serviceNamespace = APP_NAME . '\\Services';
        $className = ucfirst(str_replace(' ', '', lcfirst(ucwords(str_replace('_', ' ', $serviceName)))));
        $className = $serviceNamespace . '\\' . ucfirst($className) . 'Service';
        if (class_exists($className)) {
            return new $className($params);
        }
        return null;
    }
}