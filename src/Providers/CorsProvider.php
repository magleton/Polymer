<?php
/**
 * User: macro chen <chen_macro@163.com>
 * Date: 16-8-26
 * Time: 下午3:52
 */

namespace Polymer\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Tuupola\Middleware\Cors;

class CorsProvider implements ServiceProviderInterface
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
        $pimple['cors'] = function ($container) {
            return new Cors([
                'origin' => ['*'],
                'methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
                'headers.allow' => [
                    'token',
                    'Content-Type',
                    'Accept',
                    'Origin',
                    'User-Agent',
                    'DNT',
                    'Cache-Control',
                    'X-Mx-ReqToken',
                    'Keep-Alive',
                    'X-Requested-With',
                    'If-Modified-Since'
                ],
                'headers.expose' => ['Etag'],
                'credentials' => true,
                'cache' => 0,
                'error' => function (Request $request, Response $response, $arguments) {
                    $data['status'] = 'error';
                    $data['msg'] = $arguments['message'];
                    $data['code'] = 99;
                    try {
                        return $response
                            ->withHeader('Content-Type', 'application/json')
                            ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
                    } catch (\InvalidArgumentException $e) {
                        return null;
                    }
                }
            ]);
        };
    }
}