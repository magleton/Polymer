<?php
/**
 * User: macro chen <chen_macro@163.com>
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
        $pimple['redisCache'] = function (Container $container) {
            $redisCache = new RedisCache();
            $namespace = $container->has('namespace') ? $container->get('namespace') : 'redisCache';
            $database = $container->has('database') ? $container->get('database') : 0;
            //设置缓存的命名空间
            $type = 'redis';
            $redisCache->setNamespace($namespace);
            $redisCache->setRedis($container['application']->component($type, ['database' => $database]));
            return $redisCache;
        };
    }
}
