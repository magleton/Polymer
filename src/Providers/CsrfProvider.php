<?php
/**
 * User: macro chen <chen_macro@163.com>
 * Date: 16-8-26
 * Time: 上午9:24
 */
namespace Polymer\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Csrf\Guard;

class CsrfProvider implements ServiceProviderInterface
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
        $pimple['csrf'] = function (Container $container) {
            try {
                $guard = new Guard();
                $guard->setFailureCallable(function (Request $request, Response $response, $next) {
                    $request = $request->withAttribute('csrf_status', false);
                    return $next($request, $response);
                });
                return $guard;
            } catch (\RuntimeException $e) {
                return null;
            }
        };
    }
}