<?php
/**
 * User: macro chen <macro_fengye@163.com>
 * Date: 17-5-26
 * Time: 上午11:30
 */

namespace Polymer\Providers;

use Bernard\Driver\PhpRedisDriver;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class MqDriverProvider implements ServiceProviderInterface
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
        $pimple['mq_driver'] = function (Container $container) {
            $redis = $container['application']->component('redis');
            $redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_NONE);
            $redis->setOption(\Redis::OPT_PREFIX, 'bernard:');
            return new PhpRedisDriver($redis);
        };
    }
}
