<?php

namespace Polymer\Middleware;

use Psr\Container\ContainerInterface;
use Slim\Views\TwigMiddleware;

class GXTwigMiddleware
{
    public function create(ContainerInterface $container): TwigMiddleware
    {
        return TwigMiddleware::createFromContainer($container);
    }
}