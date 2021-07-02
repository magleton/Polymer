<?php
/**
 * User: macro chen <chen_macro@163.com>
 * Date: 16-8-26
 * Time: 下午3:55
 */

namespace Polymer\Providers;

use DI\Container;
use InvalidArgumentException;
use Slim\Middleware\JwtAuthentication;

class JwtProvider
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
        $pimpleContainer['jwt'] = function (Container $container) {
            return new JwtAuthentication([
                'header' => $container['application']->config('app.jwt.token', 'token'),
                'regexp' => $container['application']->config('app.jwt.regexp', '/(.*)/'),
                'secure' => $container['application']->config('app.jwt.secure', false),
                'secret' => $container['application']->config('app.jwt.secret', '62f47d0439a14f8bddb465dff4317fdb'),
                'path' => $container['application']->config('app.jwt.jwt_path'),
                'passthrough' => $container['application']->config('app.jwt.pass_through'),
                'error' => function (Request $request, Response $response, $arguments) {
                    $data['status'] = 'error';
                    $data['message'] = var_export($arguments, true);
                    try {
                        return $response
                            ->withHeader('Content-Type', 'application/json')
                            ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
                    } catch (InvalidArgumentException $e) {
                        return null;
                    }
                },
                'callback' => function (Request $request, Response $response, $arguments) use ($container) {
                    $container['jwtData'] = $arguments['decoded'];
                }
            ]);
        };
    }
}