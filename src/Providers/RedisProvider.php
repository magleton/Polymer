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
            $cacheConfig = $container['application']->config('cache');
            $serverName = $container->has('server_name') ? $container->get('server_name') : 'server1';
            $type = 'redis';
            $redis = new \Redis();
            $redis->connect($cacheConfig[$type][$serverName]['server']['host'],
                $cacheConfig[$type][$serverName]['server']['port'],
                $cacheConfig[$type][$serverName]['server']['timeout']);
            return $redis;
        };
    }
}
