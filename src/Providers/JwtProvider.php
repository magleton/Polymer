<?php
/**
 * User: macro chen <chen_macro@163.com>
 * Date: 16-8-26
 * Time: 下午3:55
 */

namespace Polymer\Providers;

use DI\Container;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuupola\Middleware\JwtAuthentication;

class JwtProvider
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $diContainer
     */
    public function register(Container $diContainer): void
    {
        $diContainer->set(__CLASS__, static function () use ($diContainer) {
            return new JwtAuthentication([
                'header' => $diContainer->get('application')->config('app.jwt.token', 'token'),
                'regexp' => $diContainer->get('application')->config('app.jwt.regexp', '/(.*)/'),
                'secure' => $diContainer->get('application')->config('app.jwt.secure', false),
                'secret' => $diContainer->get('application')->config('app.jwt.secret', '62f47d0439a14f8bddb465dff4317fdb'),
                'path' => $diContainer->get('application')->config('app.jwt.jwt_path'),
                'passthrough' => $diContainer->get('application')->config('app.jwt.pass_through'),
                'error' => function (ServerRequestInterface $request, ResponseInterface $response, $arguments) {
                    $data['status'] = 'error';
                    $data['message'] = var_export($arguments, true);
                    try {
                        return $response
                            ->withHeader('Content-Type', 'application/json')
                            ->getBody()
                            ->write(json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
                    } catch (InvalidArgumentException $e) {
                        return null;
                    }
                },
                'callback' => function (ServerRequestInterface $request, ResponseInterface $response, $arguments) use ($diContainer) {
                    $diContainer->set('jwtData', $arguments['decoded']);
                }
            ]);
        });
    }
}