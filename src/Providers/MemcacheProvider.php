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
     * @param Container $pimple A container instance
     */
    public function register(Container $pimple)
    {
        $pimple['memcache'] = function (Container $container) {
            $cacheConfig = $container['application']->config('cache');
            $serverName = 'server1';
            $type = 'memcache';
            if ($container['application']->component('server_name')) {
                $serverName = $container['application']->component('server_name');
            }
            $memcache = new \Memcache();
            foreach ($cacheConfig[$type][$serverName]['servers'] as $key => $server) {
                $memcache->addServer($server['host'], $server['port'], $server['timeout']);
            }
            return $memcache;
        };
    }

}