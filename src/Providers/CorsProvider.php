<?php
/**
 * User: macro chen <chen_macro@163.com>
 * Date: 16-8-26
 * Time: 下午3:52
 */

namespace Polymer\Providers;

use InvalidArgumentException;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuupola\Middleware\CorsMiddleware;

class CorsProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $pimpleContainer A container instance
     */
    public function register(Container $pimpleContainer): void
    {
        $pimpleContainer['cors'] = static function (Container $container) {
            return new CorsMiddleware([
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
                'error' => function (ServerRequestInterface $request, ResponseInterface $response, $arguments) {
                    $data['status'] = 'error';
                    $data['msg'] = $arguments['message'];
                    $data['code'] = 99;
                    try {
                        $response
                            ->withHeader('Content-Type', 'application/json')
                            ->getBody()
                            ->write(json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
                        return $response;
                    } catch (InvalidArgumentException $e) {
                        return null;
                    }
                }
            ]);
        };
    }
}