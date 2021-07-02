<?php
/**
 * User: macro chen <chen_macro@163.com>
 * Date: 16-8-26
 * Time: 上午9:24
 */

namespace Polymer\Providers;

use DI\Container;
use Exception;

class RedisCacheProvider
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $pimpleContainer A container instance
     */
    public function register(Container $pimpleContainer)
    {
        $pimpleContainer['redisCache'] = function (Container $container) {
            try {
                $redisCache = new RedisCache();
                $namespace = $container->offsetExists('redis_namespace') ? $container->offsetGet('redis_namespace') : 'redisCache';
                $database = $container->offsetExists('redis_database') ? $container->offsetGet('redis_database') : 0;
                $redisCache->setNamespace($namespace);
                $redisCache->setRedis($container['application']->component('redis', ['database' => (int)$database]));
                return $redisCache;
            } catch (Exception $e) {
                throw $e;
            }
        };
    }
}
