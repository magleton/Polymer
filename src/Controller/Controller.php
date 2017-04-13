<?php
/**
 * User: macro chen <macro_fengye@163.com>
 *
 * 所有控制器必须集成该类
 *
 * @author macro chen <macro_fengye@163.com>
 */
namespace Polymer\Controller;

use Interop\Container\ContainerInterface;
use Slim\Http\Response;

class Controller
{
    /**
     * Slim框架自动注册的Container
     * @var ContainerInterface
     */
    protected $ci;

    /**
     * 整个框架的应用
     * @var \Polymer\Boot\Application
     */
    protected $app;

    /**
     * Controller constructor.
     * @param ContainerInterface $ci
     * @throws \Psr\Container\ContainerExceptionInterface
     */

    public function __construct(ContainerInterface $ci)
    {
        $this->app = $ci->get('application');
    }

    /**
     * 模板渲染
     *
     * @author macro chen <macro_fengye@163.com>
     * @param string $template 模板文件
     * @param array $data 传递到模板的数据
     */
    protected function render($template, array $data = [])
    {
        return $this->app->component('view')->render($this->app->component('response'), $template, $data);
    }

    /**
     * Json.
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * This method prepares the response object to return an HTTP Json
     * response to the client.
     *
     * @param  mixed $data The data
     * @param  int $status The HTTP status code.
     * @param  int $encodingOptions Json encoding options
     * @return Response|string
     */
    protected function withJson($data, $status = null, $encodingOptions = 0)
    {
        try {
            return $this->app->component('response')->withJson($data, $status, $encodingOptions);
        } catch (\Exception $e) {
            return json_encode(['msg' => $e->getMessage()], $encodingOptions);
        }
    }
}
