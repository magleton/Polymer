<?php
/**
 * User: macro chen <macro_fengye@163.com>
 * Date: 17-5-26
 * Time: 上午11:15
 */

namespace Polymer\Providers;

use DI\Container;

class MqFactoryProvider
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
        $pimpleContainer['mq_factory'] = function (Container $container) {
            return new PersistentFactory($container['application']->component('mq_driver'), $container['application']->component('mq_serializer'));
        };
    }
}
