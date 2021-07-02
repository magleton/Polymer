<?php
/**
 * User: macro chen <chen_macro@163.com>
 * Date: 16-8-26
 * Time: 上午9:24
 */

namespace Polymer\Providers;

use DI\Container;
use RuntimeException;
use Slim\Csrf\Guard;

class CsrfProvider
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
        $pimpleContainer['csrf'] = static function (Container $container) {
            try {
                $guard = new Guard();
                $guard->setFailureCallable(function (Request $request, Response $response, $next) {
                    $request = $request->withAttribute('csrf_status', false);
                    return $next($request, $response);
                });
                return $guard;
            } catch (RuntimeException $e) {
                return null;
            }
        };
    }
}