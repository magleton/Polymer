<?php
/**
 * User: macro chen <macro_fengye@163.com>
 * Date: 2016/9/21
 * Time: 18:02
 */

namespace Polymer\Boot;

use Composer\Autoload\ClassLoader;
use DI\Annotation\Inject;
use DI\Container;
use DI\ContainerBuilder;
use DI\Definition\Helper\DefinitionHelper;
use DI\Definition\Source\DefinitionArray;
use DI\DependencyException;
use DI\NotFoundException;
use Doctrine\Common\EventManager;
use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\NoopWordInflector;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\Setup;
use Exception;
use Go\Core\AspectKernel;
use InvalidArgumentException;
use Noodlehaus\Config;
use Noodlehaus\Exception\EmptyDirectoryException;
use Polymer\Providers\RouterFileProvider;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Factory\ServerRequestCreatorFactory;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\DoctrineProvider;
use Throwable;

final class Application
{
    /**
     * 应用实例
     *
     * @var ?Application
     */
    protected static ?Application $instance = null;

    /**
     * @Inject
     *
     * @var EventManager
     */
    protected EventManager $eventManager;

    /**
     * 应用的服务容器
     *
     * @var ?Container
     */
    private ?Container $diContainer = null;

    /**
     * 配置文件对象
     *
     * @var Config $config
     */
    private Config $config;

    /**
     * @Inject
     * @var ClassLoader
     */
    private ClassLoader $classLoader;

    /**
     * Application constructor.
     * @throws Exception
     */
    public function __construct()
    {
        self::setInstance($this);
        $this->init();
    }

    /**
     * 初始化应用环境
     *
     * @throws Exception
     * @author macro chen <macro_fengye@163.com>
     */
    public function init(): void
    {
        try {
            set_error_handler('handleError');
            set_exception_handler(static function (Throwable $throwable) {
                print_r($throwable);
            });
            register_shutdown_function('handleShutdown');
            $builder = new ContainerBuilder();
            $builder->useAnnotations(true)->addDefinitions(new DefinitionArray($this->initConfig()));
            if ($this->diContainer === null) {
                $this->diContainer = $builder->build();
            }
            $this->diContainer->set(__CLASS__, self::getInstance());
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 将配置文件合并为一个数组
     *
     * @return array
     */
    private function initConfig(): array
    {
        $configPaths = [dirname(__DIR__) . DS . 'Config'];
        if (defined('ROOT_PATH') && file_exists(ROOT_PATH . DS . 'config') && is_dir(ROOT_PATH . DS . 'config')) {
            $configPaths[] = ROOT_PATH . DS . 'config';
        }
        if (defined('APP_PATH') && file_exists(APP_PATH . DS . 'Config') && is_dir(APP_PATH . DS . 'Config')) {
            $configPaths[] = APP_PATH . DS . 'Config';
        }
        $this->config = new Config($configPaths);
        return $this->config->all();
    }

    /**
     * 获取全局应用实例
     *
     * @return static
     */
    public static function getInstance(): Application
    {
        return self::$instance;
    }

    /**
     * 设置全局可用的应用实例
     *
     * @param Application|null $application
     * @return Application|null
     */
    public static function setInstance(Application $application = null): ?Application
    {
        return self::$instance = $application;
    }

    /**
     * @return ClassLoader
     */
    public function getClassLoader(): ClassLoader
    {
        return $this->classLoader;
    }

    /**
     * 启动WEB应用
     *
     * @throws Exception
     * @author macro chen <macro_fengye@163.com>
     */
    public function run(): void
    {
        try {
            $aspectKernel = $this->initAOP();
            $this->diContainer->get(RouterFileProvider::class);
            $app = $this->diContainer->get(App::class);
            $serverRequestCreator = ServerRequestCreatorFactory::create();
            $request = $serverRequestCreator->createServerRequestFromGlobals();
            $this->diContainer->set(ServerRequestInterface::class, $request);
            $app->run($request);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 初始化AOP
     * @return AspectKernel
     * @throws EmptyDirectoryException
     */
    private function initAOP(): AspectKernel
    {
        $aspectKernel = ApplicationAspectKernel::getInstance();
        $aspectKernel->init(array_merge($this->getConfig('aop.init', []), $this->getConfig('app.aop.init', [])));
        return $aspectKernel;
    }

    /**
     * 获取指定键的配置文件
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     * @throws Exception
     * @throws EmptyDirectoryException
     * @author macro chen <macro_fengye@163.com>
     */
    public function getConfig(string $key, $default = null)
    {
        try {
            if ($this->config->get($key)) {
                return $this->config->get($key);
            }
            return $default;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 定义一个对象或者值到容器
     *
     * @param string $name Entry name
     * @param mixed|DefinitionHelper $value Value, use definition helpers to define objects
     */
    public function set(string $name, $value): void
    {
        $this->getDiContainer()->set($name, $value);
    }

    /**
     * @return Container
     */
    public function getDiContainer(): Container
    {
        return $this->diContainer;
    }

    /**
     * 启动控制台,包括单元测试及其他的控制台程序(定时任务等...)
     *
     * @throws Exception
     * @author macro chen <macro_fengye@163.com>
     */
    public function runConsole(): void
    {
        // 占位
    }

    /**
     * 添加自定义监听器
     *
     * @param array $params
     * @return EventManager|null
     * @throws Exception
     * @author macro chen <macro_fengye@163.com>
     */
    public function addEvent(array $params = []): ?EventManager
    {
        try {
            return $this->addEventOrSubscribe($params, 1);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 添加事件监听器或者订阅器
     *
     * @param array $params
     * @param int $listener 0 添加事件订阅器 1 添加事件监听器
     * @return mixed
     */
    private function addEventOrSubscribe(array $params, int $listener)
    {
        $methods = ['addEventSubscriber', 'addEventListener'];
        $this->eventManager = self::getInstance()->get(EventManager::class);
        foreach ($params as $key => $value) {
            if (!isset($value['class_name'])) {
                throw new InvalidArgumentException('class_name必须设置');
            }
            $className = $value['class_name'];
            $data = $value['params'] ?? [];
            $listener === 1 ? $this->eventManager->{$methods[$listener]}($key, new $className($data)) : $this->eventManager->{$methods[$listener]}(new $className($data));
        }
        return $this->eventManager;
    }

    /**
     * 获取容器中的对象
     *
     * @param $provider
     * @return object|null
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function get($provider): ?object
    {
        return $this->getDiContainer()->get($provider);
    }

    /**
     * 添加自定义订阅器
     *
     * @param array $params
     * @return EventManager|null
     * @author macro chen <macro_fengye@163.com>
     */
    public function addSubscriber(array $params = []): ?EventManager
    {
        try {
            return $this->addEventOrSubscribe($params, 0);
        } catch (InvalidArgumentException $e) {
            throw $e;
        }
    }

    /**
     * 将下划线转为驼峰
     * table_name =>  tableName
     *
     * @return Inflector
     */
    public function getInflector(): Inflector
    {
        return new Inflector(new NoopWordInflector(), new NoopWordInflector());
    }

    /**
     * 实例化数据库链接对象
     *
     * @param string $dbName
     * @param mixed|null $entityFolder 实体文件夹的名字
     * @return EntityManager
     * @throws ORMException | InvalidArgumentException | Exception
     */
    public function db(string $dbName = '', string $entityFolder = null): EntityManager
    {
        try {
            $current = current(array_keys($this->getConfig('db.' . APPLICATION_ENV)));
            if ($dbName === '' || $dbName === null) {
                $dbName = $current;
            }
            $cacheKey = 'em' . '.' . $this->getConfig('db.' . APPLICATION_ENV . '.' . $dbName . '.emCacheKey', str_replace([':', DS], ['', ''], APP_PATH)) . '.' . $dbName;
            if ($this->getConfig('db.' . APPLICATION_ENV . '.' . $dbName) && !$this->diContainer->has($cacheKey)) {
                $entityFolder = $entityFolder ?: ROOT_PATH . DS . APP_NAME . DS . 'Entity' . DS . 'Mapping';
                $cache = APPLICATION_ENV === 'development' ? null : new DoctrineProvider(new ArrayAdapter());
                $configuration = Setup::createAnnotationMetadataConfiguration([
                    $entityFolder,
                ], APPLICATION_ENV === 'development',
                    ROOT_PATH . DS . 'entity' . DS . 'Proxies' . DS,
                    $cache,
                    $this->getConfig('db.' . APPLICATION_ENV . '.' . $dbName . '.' . 'useSimpleAnnotationReader'));
                $entityManager = EntityManager::create($this->getConfig('db.' . APPLICATION_ENV . '.' . $dbName), $configuration, $this->diContainer->get(EventManager::class));
                $this->diContainer->set($cacheKey, $entityManager);
            }
            return $this->diContainer->get($cacheKey);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
