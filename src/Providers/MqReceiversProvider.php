<?php
/**
 * User: macro chen <macro_fengye@163.com>
 * Date: 17-5-26
 * Time: 上午11:20
 */

namespace Polymer\Providers;

use Bernard\Router\SimpleRouter;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class MqReceiversProvider implements ServiceProviderInterface
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
        $pimple['mq_receivers'] = function (Container $container) {
            return new SimpleRouter($container['receivers']);
        };
    }
}
