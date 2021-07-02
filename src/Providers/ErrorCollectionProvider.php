<?php
/**
 * User: macro chen <macro_chen@163.com>
 * Date: 2017/2/26
 * Time: 16:45
 */

namespace Polymer\Providers;

use DI\Container;
use Polymer\Support\Collection;

class ErrorCollectionProvider
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
        $pimpleContainer['error_collection'] = function (Container $container) {
            return new Collection();
        };
    }
}