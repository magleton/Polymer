<?php
/**
 * Created by PhpStorm.
 * User: macro
 * Date: 16-8-29
 * Time: 上午7:48
 */

namespace Polymer\Providers;


use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Slim\App;
use Slim\Http\Body;

class InitAppProvider implements ServiceProviderInterface
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
        $pimple['notAllowedHandler'] = function ($container) {
            return function ($request, $response, $methods) use ($container) {
                return $container['response']
                    ->withStatus(405)
                    ->withHeader('Allow', implode(', ', $methods))
                    ->withHeader('Content-type', 'text/html')
                    ->write('Method must be one of: ' . implode(', ', $methods));
            };
        };
        $pimple['notFoundHandler'] = function ($container) {
            return function ($request, $response) use ($container) {
                if ($container['application']->config('customer.is_rest')) {
                    return $container['response']
                        ->withStatus(404)
                        ->withHeader('Content-Type', 'application/json')
                        ->withJson(['code' => 1, 'msg' => '404', 'data' => []]);
                } else {
                    try {
                        $body = new Body(@fopen(TEMPLATE_PATH . '404.twig', 'rb'));
                        return $container['response']
                            ->withStatus(404)
                            ->withHeader('Content-Type', 'text/html')
                            ->withBody($body);
                    } catch (\InvalidArgumentException $e) {
                        return $container['response']
                            ->withStatus(404)
                            ->withHeader('Content-Type', 'text/html')->write($e->getMessage());
                    }
                }
            };
        };
        $pimple['phpErrorHandler'] = function ($container) {
            return $container['errorHandler'];
        };
        $pimple['errorHandler'] = function ($container) {
            return function ($request, $response, $exception) use ($container) {
                $container->register(new LoggerProvider());
                $container['logger']->error($exception->__toString());
                if ($container['application']->config('customer.is_rest')) {
                    return $container['response']
                        ->withStatus(500)
                        ->withHeader('Content-Type', 'application/json')
                        ->withJson(['code' => 500, 'msg' => '500 status', 'data' => []]);
                } else {
                    try {
                        $body = new Body(@fopen(TEMPLATE_PATH . 'error.twig', 'rb'));
                        return $container['response']
                            ->withStatus(500)
                            ->withHeader('Content-Type', 'text/html')
                            ->withBody($body);
                    } catch (\InvalidArgumentException $e) {
                        return $container['response']
                            ->withStatus(500)
                            ->withHeader('Content-Type', 'text/html')->write($e->getMessage());
                    }
                };
            };
        };
        $pimple['app'] = function (Container $container) {
            try {
                return new App($container);
            } catch (\InvalidArgumentException $e) {
                return $container['response']
                    ->withStatus(500)
                    ->withHeader('Content-Type', 'text/html')->write($e->getMessage());
            }
        };

        $pimple['twig_profile'] = function (Container $container) {
            return new \Twig_Profiler_Profile();
        };
    }
}