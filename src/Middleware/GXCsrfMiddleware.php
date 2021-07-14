<?php

namespace Polymer\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Csrf\Guard;
use Tuupola\Http\Factory\ResponseFactory;

class GXCsrfMiddleware
{
    public function create(ContainerInterface $container): Guard
    {
        $guard = new Guard(new ResponseFactory());
        $guard->setFailureHandler(function (ServerRequestInterface $request, ResponseInterface $response, $next) {
            $request = $request->withAttribute('csrf_status', false);
            return $next($request, $response);
        });
        return $guard;
    }
}