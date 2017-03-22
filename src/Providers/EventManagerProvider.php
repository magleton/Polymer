<?php
/**
 * User: macro chen <macro_fengye@163.com>
 * Date: 16-8-26
 * Time: 上午9:24
 */

namespace Polymer\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Doctrine\Common\EventManager;

class EventManagerProvider implements ServiceProviderInterface
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
        $pimple['eventManager'] = function (Container $container) {
            return new EventManager();
        };
    }
}