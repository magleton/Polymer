<?php
/**
 * User: macro chen <chen_macro@163.com>
 * Date: 16-8-26
 * Time: 下午3:55
 */

namespace Polymer\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Middleware\JwtAuthentication;

class JwtProvider implements ServiceProviderInterface
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
        $pimple['jwt'] = function ($container) {
            return new JwtAuthentication([
                'header' => 'token',
                'regexp' => '/(.*)/',
                'secure' => false,
                'secret' => app()->component('application')->component('session') ? app()->component('application')->component('session')->get('secret') : '62f47d0439a14f8bddb465dff4317fdb',
                'path' => ['/user', '/loan', '/merchant'],
                'passthrough' => [
                    '/user/generateCaptcha',
                    '/user/sendSMS',
                    '/user/login',
                    '/user/register',
                    '/user/retrievePassword',
                    '/user/logout'
                ],
                'error' => function (Request $request, Response $response, $arguments) {
                    $data['status'] = 'error';
                    $data['message'] = var_export($arguments, true);
                    try {
                        return $response
                            ->withHeader('Content-Type', 'application/json')
                            ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
                    } catch (\InvalidArgumentException $e) {
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