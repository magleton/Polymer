<?php
/**
 * User: macro chen <macro_fengye@163.com>
 * Date: 17-5-26
 * Time: 上午11:15
 */

namespace Polymer\Providers;

use Bernard\Driver\PhpRedisDriver;
use Bernard\QueueFactory\PersistentFactory;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class MqFactoryProvider implements ServiceProviderInterface
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
        $pimple['mq_factory'] = function (Container $container) {
            return new PersistentFactory($container['application']->component('mq_driver'), $container['application']->component('mq_serializer'));
        };
    }
}
