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
     * @param Container $diContainer A container instance
     */
    public function register(Container $diContainer): void
    {
        $diContainer->set(__CLASS__, static function () use ($diContainer) {
            try {
                $diContainer->get(SessionProvider::class);
                return new Messages();
            } catch (InvalidArgumentException | RuntimeException $e) {
                return null;
            }
        });
    }
}