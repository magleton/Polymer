<?php

namespace Polymer\Middleware;

use DI\Annotation\Inject;
use JsonException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\App;

class GXParseRequestJSONMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    private ContainerInterface $container;

    /**
     * @Inject
     * @var App
     */
    private App $app;

    /**
     *
     * GXParseRequestJSONMiddleware constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     * @throws JsonException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $contentType = $request->getHeaderLine('Content-Type');
        if (strpos($contentType, 'application/json') !== false) {
            $contents = json_decode(file_get_contents('php://input'), true, 512, JSON_THROW_ON_ERROR);
            if (json_last_error() === JSON_ERROR_NONE) {
                $request = $request->withParsedBody($contents);
            }
        }
        return $handler->handle($request);
    }
}
