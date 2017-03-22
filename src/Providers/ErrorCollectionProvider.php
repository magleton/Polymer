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
    public function register(Container $pimple)
    {
        $pimple['error_collection'] = function ($container) {
            return new Collection();
        };
    }
}