<?php
/**
 * Created by PhpStorm.
 * User: macro
 * Date: 16-8-26
 * Time: 上午9:24
 */

namespace Polymer\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Slim\Flash\Messages;

class FlashP implements ServiceProviderInterface
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
        $pimple['flash'] = function (Container $container) {
            return new Messages();
        };
    }
}