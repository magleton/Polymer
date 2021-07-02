<?php
/**
 * User: macro chen <macro_fengye@163.com>
 * Date: 17-5-26
 * Time: 上午11:17
 */

namespace Polymer\Providers;

use DI\Container;

class MqMiddlewareProvider
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
        $pimpleContainer['mq_middleware'] = function (Container $container) {
            $chain = new MiddlewareBuilder();
            $chain->push(new ErrorLogFactory());
            $chain->push(new FailuresFactory($container['application']->component('mq_factory')));
            return $chain;
        };
    }
}

