<?php
/**
 * User: macro chen <chen_macro@163.com>
 * Date: 16-8-26
 * Time: 下午4:03
 */

namespace Polymer\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class JwtDataProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $pimpleContainer A container instance
     * @return mixed
     */
    public function register(Container $pimpleContainer)
    {
        $pimpleContainer['jwtData'] = function (Container $container) {
            return new \stdClass();
        };
    }
}