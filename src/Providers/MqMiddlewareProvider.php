<?php
/**
 * User: macro chen <macro_fengye@163.com>
 * Date: 17-5-26
 * Time: 上午11:17
 */

namespace Polymer\Providers;

use Bernard\Middleware\ErrorLogFactory;
use Bernard\Middleware\FailuresFactory;
use Bernard\Middleware\MiddlewareBuilder;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class MqMiddlewareProvider implements ServiceProviderInterface
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
        $pimple['mq_middleware'] = function (Container $container) {
            $chain = new MiddlewareBuilder();
            $chain->push(new ErrorLogFactory());
            $chain->push(new FailuresFactory($container['application']->component('mq_factory')));
            return $chain;
        };
    }
}

