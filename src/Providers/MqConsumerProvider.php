<?php
/**
 * User: macro chen <macro_fengye@163.com>
 * Date: 17-5-26
 * Time: 上午10:18
 */

namespace Polymer\Providers;

use DI\Container;

class MqConsumerProvider
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
        $pimpleContainer['mq_consumer'] = function (Container $container) {
            //return new Consumer($container['application']->component('mq_receivers'), $container['application']->component('mq_middleware'));
            return new Consumer($container['application']->component('mq_receivers'), $container['application']->component('mq_event_dispatcher'));
        };
    }
}
