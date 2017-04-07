<?php
/**
 * User: macro chen <macro_chen@163.com>
 * Date: 2017/2/26
 * Time: 16:45
 */

namespace Polymer\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Polymer\Support\Collection;

class ErrorCollectionProvider implements ServiceProviderInterface
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
        $pimple['error_collection'] = function (Container $container) {
            return new Collection();
        };
    }
}