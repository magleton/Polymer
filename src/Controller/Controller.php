<?php
/**
 * User: macro chen <macro_fengye@163.com>
 *
 * 所有控制器必须集成该类
 *
 * @author macro chen <macro_fengye@163.com>
 */

namespace Polymer\Controller;

use DI\Annotation\Inject;
use DI\Container;
use JsonException;
use Polymer\Boot\Application;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Controller
{
    /**
     * Slim框架自动注册的Container
     * @var ContainerInterface
     */
    protected ContainerInterface $ci;

    /**
     * 整个框架的应用
     * @Inject
     * @var Application
     */
    protected Application $application;

    /**
     * @Inject
     * @var Container
     */
    protected Container $diContainer;

    /**
     * @Inject
     * @var ServerRequestInterface
     */
    protected ServerRequestInterface $request;

    /**
     * 模板渲染
     *
     * @param string $template 模板文件
     * @param ResponseInterface $response
     * @param array $data 传递到模板的数据
     * @return ResponseInterface
     * @author macro chen <macro_fengye@163.com>
     */
    protected function render(string $template, ResponseInterface $response, array $data = []): ResponseInterface
    {
        $response->getBody()->write("");
        return $response;
    }

    /**
     * Json.
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * This method prepares the response object to return an HTTP Json
     * response to the client.
     *
     * @param mixed $data The data
     * @param ResponseInterface $response
     * @param int|null $status The HTTP status code.
     * @param int $encodingOptions Json encoding options
     * @return ResponseInterface
     */
    protected function withJson($data, ResponseInterface $response, int $status = 200, int $encodingOptions = 0): ResponseInterface
    {
        $retData = ['code' => $status, 'msg' => '', 'data' => $data];
        try {
            $body = json_encode($retData, JSON_THROW_ON_ERROR | $encodingOptions);
        } catch (JsonException $e) {
            $body = '{"code":500 , "msg":' . $e->getMessage() . ' , "data":null}';
        }
        $response->withHeader('Content-Type', 'application/json')->getBody()->write($body);
        return $response;
    }
}
