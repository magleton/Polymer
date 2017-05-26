<?php
/**
 * User: macro chen <macro_fengye@163.com>
 * Date: 17-5-26
 * Time: 上午10:18
 */

namespace Polymer\Providers;

use Bernard\Consumer;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class MqConsumerProvider implements ServiceProviderInterface
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
        $pimple['mq_consumer'] = function (Container $container) {
            return new Consumer($container['application']->component('mq_receivers'), $container['application']->component('mq_middleware'));
        };
    }
}
