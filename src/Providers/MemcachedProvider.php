<?php
/**
 * User: macro chen <chen_macro@163.com>
 * Date: 2016/9/1
 * Time: 19:46
 */

namespace Polymer\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class MemcachedProvider implements ServiceProviderInterface
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
        $pimple['memcached'] = function (Container $container) {
            try {
                $serverName = $container->offsetExists('memcached_server') ? $container->offsetGet('memcached_server') : 'server1';
                $cacheConfig = $container['application']->config('cache.memcached.' . $serverName);
                $serversConfig = isset($cacheConfig['servers']) ? $cacheConfig['servers'] : [];
                if ($serversConfig) {
                    $memcached = new \Memcached();
                    foreach ($serversConfig as $key => $server) {
                        $memcached->addServer($server['host'], $server['port'], $server['timeout']);
                    }
                    return $memcached;
                }
                return null;
            } catch (\Exception $e) {
                throw $e;
            }
        };
    }
}
