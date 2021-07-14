<?php


namespace Polymer\Middleware;


use Polymer\Boot\Application;
use Polymer\Exceptions\InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tuupola\Middleware\JwtAuthentication;

class GXJwtAuthenticationMiddleware
{
    public function create(ContainerInterface $container): JwtAuthentication
    {
        return new JwtAuthentication([
            'header' => $container->get(Application::class)->getConfig('app.jwt.token', 'token'),
            'regexp' => $container->get(Application::class)->getConfig('app.jwt.regexp', '/(.*)/'),
            'secure' => $container->get(Application::class)->getConfig('app.jwt.secure', false),
            'secret' => $container->get(Application::class)->getConfig('app.jwt.secret', '62f47d0439a14f8bddb465dff4317fdb'),
            'path' => $container->get(Application::class)->getConfig('app.jwt.jwt_path'),
            'passthrough' => $container->get(Application::class)->getConfig('app.jwt.pass_through'),
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
            'callback' => function (ServerRequestInterface $request, ResponseInterface $response, $arguments) use ($container) {
                $container->set('jwtData', $arguments['decoded']);
            }
        ]);
    }
}