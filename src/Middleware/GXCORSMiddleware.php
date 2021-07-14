<?php

namespace Polymer\Middleware;

use Psr\Container\ContainerInterface;
use Tuupola\Middleware\CorsMiddleware;

class GXCORSMiddleware
{
    public function create(ContainerInterface $container): CorsMiddleware
    {
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
    }
}