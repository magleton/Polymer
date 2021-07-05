<?php
/**
 * User: macro chen <chen_macro@163.com>
 * Date: 16-8-29
 * Time: 上午7:48
 */

namespace Polymer\Providers;

use DI\Container;
use http\Message\Body;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Factory\AppFactory;

class InitApplicationProvider
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
        $pimpleContainer->set('notAllowedHandler', static function (Container $container) {
            return static function (ServerRequestInterface $request, ResponseInterface $response, $methods) use ($container) {
                $response
                    ->withStatus(405)
                    ->withHeader('Allow', implode(', ', $methods))
                    ->withHeader('Content-type', 'text/html')
                    ->getBody()
                    ->write('Method must be one of: ' . implode(', ', $methods));
                return $response;
            };
        });

        $pimpleContainer->set('notFoundHandler', static function (Container $container) {
            return static function (ServerRequestInterface $request, ResponseInterface $response) use ($container) {
                if ($container['application']->config('app.is_rest', false)) {
                    $response
                        ->withStatus(404)
                        ->withHeader('Content-Type', 'application/json')
                        ->getBody()
                        ->write('{"code" :1, "msg" : "404","data" : null}');
                    return $response;
                }
                try {
                    if (defined('TEMPLATE_PATH') && file_exists(TEMPLATE_PATH . '404.twig')) {
                        $body = new Body(@fopen(TEMPLATE_PATH . '404.twig', 'rb'));
                        $response->withStatus(404)
                            ->withHeader('Content-Type', 'text/html')
                            ->getBody()
                            ->write($body);
                        return $response;
                    }
                    $response
                        ->withStatus(404)
                        ->withHeader('Content-Type', 'text/html')
                        ->getBody()
                        ->write('<h1>404,您知道的</h1>');
                    return $response;
                } catch (InvalidArgumentException $e) {
                    $response
                        ->withStatus(404)
                        ->withHeader('Content-Type', 'text/html')
                        ->getBody()
                        ->write($e->getMessage());
                    return $response;
                }
            };
        });

        $pimpleContainer->set('phpErrorHandler', static function (Container $container) {
            return $container['errorHandler'];
        });

        $pimpleContainer->set('errorHandler', static function (Container $container) {
            return static function (ServerRequestInterface $request, ResponseInterface $response, $exception) use ($container) {
                $container->register(new LoggerProvider());
                $container['logger']->error($exception->__toString());
                if ($container['application']->config('app.is_rest', false)) {
                    $response
                        ->withStatus(500)
                        ->withHeader('Content-Type', 'application/json')
                        ->getBody()
                        ->write('{"code":500 , "msg":"500 status","data":null}');
                    return $response;
                }

                try {
                    if (defined('TEMPLATE_PATH') && file_exists(TEMPLATE_PATH . 'error.twig')) {
                        $body = new Body(@fopen(TEMPLATE_PATH . 'error.twig', 'rb'));
                        $response
                            ->withStatus(500)
                            ->withHeader('Content-Type', 'text/html')
                            ->getBody()
                            ->write($body);
                        return $response;
                    }
                    $response
                        ->withStatus(500)
                        ->withHeader('Content-Type', 'text/html')
                        ->getBody()
                        ->write('<h1>请联系管理员!</h1>');
                    return $response;
                } catch (InvalidArgumentException $e) {
                    $response
                        ->withStatus(500)
                        ->withHeader('Content-Type', 'text/html')
                        ->getBody()
                        ->write($e->getMessage());
                    return $response;
                }
            };
        });

        $pimpleContainer->set(App::class, static function (Container $pimpleContainer) {
            return AppFactory::createFromContainer($pimpleContainer);
            //return Bridge::create($pimpleContainer);
        });
    }
}
