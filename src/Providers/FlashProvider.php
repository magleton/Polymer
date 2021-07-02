<?php
/**
 * User: macro chen <chen_macro@163.com>
 * Date: 16-8-26
 * Time: 上午9:24
 */

namespace Polymer\Providers;

use DI\Container;
use InvalidArgumentException;
use RuntimeException;
use Slim\Flash\Messages;

class FlashProvider
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
        $pimpleContainer['flash'] = function (Container $container) {
            try {
                $container['application']->component('session');
                return new Messages();
            } catch (InvalidArgumentException $e) {
                return null;
            } catch (RuntimeException $e) {
                return null;
            }
        };
    }
}