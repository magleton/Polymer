<?php
/**
 * Created by PhpStorm.
 * User: macro
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
     * @param Container $pimple A container instance
     * @return mixed
     */
    public function register(Container $pimple)
    {
        return new StdClass;
    }
}