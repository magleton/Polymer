<?php
/**
 * User: macro chen <macro_fengye@163.com>
 * Date: 17-5-26
 * Time: 上午10:14
 */

namespace Polymer\Providers;

use DI\Container;

class MqProducerProvider
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
        $pimpleContainer['mq_producer'] = function (Container $container) {
            //return new Producer($container['application']->component('mq_factory'), $container['application']->component('mq_middleware'));  //0.x版本
            return new Producer($container['application']->component('mq_factory'), $container['application']->component('mq_event_dispatcher')); //1.x版本
        };
    }
}
