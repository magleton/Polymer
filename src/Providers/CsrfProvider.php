<?php
/**
 * User: macro chen <chen_macro@163.com>
 * Date: 16-8-26
 * Time: 上午9:24
 */

namespace Polymer\Providers;

use DI\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
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
     * @param Container $diContainer A container instance
     */
    public function register(Container $diContainer): void
    {
        $diContainer->set(__CLASS__, static function () use ($diContainer) {
            try {
                $guard = new Guard();
                $guard->setFailureCallable(function (ServerRequestInterface $request, ResponseInterface $response, $next) {
                    $request = $request->withAttribute('csrf_status', false);
                    return $next($request, $response);
                });
                return $guard;
            } catch (RuntimeException $e) {
                return null;
            }
        });
    }
}