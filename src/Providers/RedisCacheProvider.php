<?php
/**
 * Created by PhpStorm.
 * User: macro
 * Date: 16-8-26
 * Time: 上午9:24
 */
namespace Polymer\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Doctrine\Common\Cache\RedisCache;

class RedisCacheProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $pimple A container instance
     */
    public function register(Container $pimple)
    {
        $pimple['redisCacheDriver'] = function (Container $container) {
            $redisCacheDriver = new RedisCache();
            $namespace = $container->has('namespace') ? $container->get('namespace') : 'redisCacheDriver';
            $database = $container->has('database') ? $container->get('database') : 0;
            //设置缓存的命名空间
            $type = 'redis';
            $redisCacheDriver->setNamespace($namespace);
            $redisCacheDriver->setRedis($container['application']->component($type, ['database' => $database]));
            return $redisCacheDriver;
        };
    }
}