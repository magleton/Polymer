<?php
/**
 * User: macro chen <chen_macro@163.com>
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
     * @param Container $pimpleContainer A container instance
     */
    public function register(Container $pimpleContainer)
    {
        $pimpleContainer['memcacheCache'] = function (Container $container) {
            try {
                $namespace = $container->offsetExists('memcache_namespace') ? $container->offsetGet('memcache_namespace') : 'memcacheCache';
                $memcacheCache = new MemcacheCache();
                $memcacheCache->setNamespace($namespace);
                $memcacheCache->setMemcache($container['application']->component('memcache'));
                return $memcacheCache;
            } catch (\Exception $e) {
                throw $e;
            }
        };
    }
}