<?php

namespace Polymer\Middleware;

use Polymer\Providers\SessionProvider;
use Psr\Container\ContainerInterface;
use Slim\Flash\Messages;

class GXFlashMiddleware
{
    public function create(ContainerInterface $container): Messages
    {
        $container->get(SessionProvider::class);
        return new Messages();
    }
}