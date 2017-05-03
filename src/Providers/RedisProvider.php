<?php
/**
 * User: macro chen <chen_macro@163.com>
 * Date: 2016/9/1
 * Time: 19:40
 */
namespace Polymer\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class RedisProvider implements ServiceProviderInterface
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
        $pimple['redis'] = function (Container $container) {
            try {
                $serverName = $container->offsetExists('redis_server') ? $container->offsetGet('redis_server') : 'server1';
                $serversConfig = $container['application']->config('cache.redis.' . $serverName);
                $redis = new \Redis();
                $redis->connect($serversConfig['server']['host'], $serversConfig['server']['port'], $serversConfig['server']['timeout']);
                (isset($serversConfig['server']['password']) && $serversConfig['server']['password']) && $redis->auth($serversConfig['server']['password']);
                return $redis;
            } catch (\Exception $e) {
                return null;
            }
        };
    }
}
