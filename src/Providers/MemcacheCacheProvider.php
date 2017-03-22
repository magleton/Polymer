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
use Doctrine\Common\Cache\MemcacheCache;

class MemcacheCacheProvider implements ServiceProviderInterface
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
        $pimple['memcacheCache'] = function (Container $container) {
            $namespace = 'memcacheCache';
            if ($container['application']->component('namespace')) {
                $namespace = $container['application']->component('namespace');
            }
            $memcacheCacheDriver = new MemcacheCache();
            $memcacheCacheDriver->setNamespace($namespace);
            $memcacheCacheDriver->setMemcache($container['application']->component('memcache'));
            return $memcacheCacheDriver;
        };
    }
}