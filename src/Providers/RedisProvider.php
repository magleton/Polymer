<?php
/**
 * Created by PhpStorm.
 * User: Administrator
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
            $server_name = $container->has('server_name') ? $container->get('server_name') : 'server1';
            $type = 'redis';
            $redis = new \Redis();
            $redis->connect($cacheConfig[$type][$server_name]['server']['host'], $cacheConfig[$type][$server_name]['server']['port'], $cacheConfig[$type][$server_name]['server']['timeout']);
            return $redis;
        };
    }

}