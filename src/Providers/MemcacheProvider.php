<?php
/**
 * User: macro chen <chen_macro@163.com>
 * Date: 2016/9/1
 * Time: 19:45
 */
namespace Polymer\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class MemcacheProvider implements ServiceProviderInterface
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
        $pimpleContainer['memcache'] = function (Container $container) {
            try {
                $serverName = $container->offsetExists('memcache_server') ? $container->offsetGet('memcache_server') : 'server1';
                $cacheConfig = $container['application']->config('cache.memcache.' . $serverName);
                $serversConfig = isset($cacheConfig['servers']) ? $cacheConfig['servers'] : [];
                if ($serversConfig) {
                    $memcache = new \Memcache();
                    foreach ($serversConfig as $key => $server) {
                        $memcache->addServer($server['host'], $server['port'], $server['timeout']);
                    }
                    return $memcache;
                }
                return null;
            } catch (\Exception $e) {
                throw $e;
            }
        };
    }
}
