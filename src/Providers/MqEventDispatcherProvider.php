<?php
/**
 * User: macro chen <macro_fengye@163.com>
 * Date: 17-5-27
 * Time: 上午8:44
 */

namespace Polymer\Providers;

use DI\Container;
use Symfony\Component\EventDispatcher\EventDispatcher;

class MqEventDispatcherProvider
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
        $pimpleContainer['mq_event_dispatcher'] = function (Container $container) {
            $dispatcher = new EventDispatcher();
            $dispatcher->addSubscriber(new ErrorLogSubscriber());
            //$dispatcher->addSubscriber(new FailureSubscriber($container['application']->component('mq_producer')));
            return $dispatcher;
        };
    }
}