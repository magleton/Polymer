<?php
/**
 * User: macro chen <macro_fengye@163.com>
 * Date: 17-5-26
 * Time: 上午10:14
 */

namespace Polymer\Providers;

use Bernard\Producer;
use Bernard\QueueFactory\PersistentFactory;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class MqProducerProvider implements ServiceProviderInterface
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
        $pimple['mq_producer'] = function (Container $container) {
            //return new Producer($container['application']->component('mq_factory'), $container['application']->component('mq_middleware'));  //0.x版本
            return new Producer($container['application']->component('mq_factory'), $container['application']->component('mq_event_dispatcher')); //1.x版本
        };
    }
}
