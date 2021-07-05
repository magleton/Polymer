<?php
/**
 * User: macro chen <chen_macro@163.com>
 * Date: 16-8-26
 * Time: 下午4:03
 */

namespace Polymer\Providers;

use DI\Container;
use stdClass;

class JwtDataProvider
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $diContainer
     * @return void
     */
    public function register(Container $diContainer): void
    {
        $diContainer->set(__CLASS__, function () use ($diContainer) {
            return new stdClass();
        });
    }
}