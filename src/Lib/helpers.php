<?php

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Noodlehaus\Exception\EmptyDirectoryException;
use Polymer\Boot\Application;

if (!function_exists('app')) {
    /**
     * 获取应用实例
     *
     * @param null $make 是否返回对象实例
     * @param array $parameters
     * @return Application
     * @author <macro_fengye@163.com> macro chen
     */
    function app($make = null, array $parameters = []): Application
    {
        if (null === $make) {
            return Application::getInstance();
        }
        return Application::getInstance()->component($make, $parameters);
    }
}

if (!function_exists('logger')) {
    /**
     * 记录日志，便于调试
     *
     * @param $message
     * @param array $content
     * @param string $file
     * @param string $log_name
     * @param int $level
     * @author <macro_fengye@163.com> macro chen
     */
    function logger($message, array $content, $file = '', $log_name = 'LOG', $level = Logger::WARNING)
    {
        $levels = [
            100 => 'debug',
            200 => 'info',
            250 => 'notice',
            300 => 'warning',
            400 => 'error',
            500 => 'critical',
            550 => 'alert',
            600 => 'emergency'
        ];
        $logger = new Logger($log_name);
        $logger->pushProcessor(new UidProcessor());
        $logger->pushHandler(new StreamHandler($file ? $file : APP_PATH . '/log/log.log', $level));
        $function_name = $levels[$level];
        $logger->$function_name($message, $content);
    }
}

if (!function_exists('handleShutdown')) {
    /**
     * PHP错误处理函数
     *
     * @throws EmptyDirectoryException
     * @throws JsonException
     * @author <macro_fengye@163.com> macro chen
     */
    function handleShutdown()
    {
        $error = error_get_last();
        if (empty($error)) {
            $msg = "错误数组为空";
            if (is_null(app()->config('logger'))) {
                return;
            }
            app()->config('logger')->error($msg);
            return;
        }
        if ($error['type'] === E_ERROR) {
            if (app()->config('logger')) {
                $msg = 'Type : ' . $error['type'] . '\nMessage : ' . $error['message'] . '\nFile : ' . $error['file'] . '\nLine : ' . $error['line'];
                app()->config('logger')->error($msg);
            } else {
                $msg = 'Type : ' . $error['type'] . ' , Message : ' . $error['message'] . ' , File : ' . $error['file'] . ' , Line : ' . $error['line'];
                logger('Fatal Error : ', [$msg], APP_PATH . '/log/fatal_error.log', Monolog\Logger::ERROR);
                if (defined('TEMPLATE_PATH') && file_exists(TEMPLATE_PATH . 'error.twig')) {
                    echo @file_get_contents(TEMPLATE_PATH . 'error.twig');
                } else {
                    echo json_encode(['code' => 2000, 'msg' => 'Error', 'data' => []], JSON_THROW_ON_ERROR);
                }
            }
        }
    }
}


if (!function_exists('handleError')) {
    /**
     * 自定义的错误处理函数
     *
     * @param $level
     * @param $message
     * @param string $file
     * @param int $line
     * @param array $context
     * @throws ErrorException
     * @author <macro_fengye@163.com> macro chen
     */
    function handleError($level, $message, $file = '', $line = 0, $context = [])
    {
        if (error_reporting() & $level) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }
    }
}


if (PHP_MAJOR_VERSION === 7 && !function_exists('handleException')) {
    /**
     * 自定义的异常处理函数
     *
     * @param mixed $e
     * @throws Exception
     * @author <macro_fengye@163.com> macro chen
     */
    function handleException($e)
    {
        throw $e;
    }
}

if (PHP_MAJOR_VERSION === 5 && !function_exists('handleException')) {
    /**
     * 自定义的异常处理函数
     *
     * @param Exception $e
     * @throws Exception
     * @author <macro_fengye@163.com> macro chen
     */
    function handleException(Exception $e)
    {
        throw $e;
    }
}

if (!function_exists('routeGeneration')) {
    /**
     * 是否重新生成路由文件
     *
     * @return bool
     * @throws EmptyDirectoryException
     */
    function routeGeneration(): bool
    {
        $routerLockFile = app()->config('app.router_path.lock', app()->config('router_path.lock'));
        return !file_exists($routerLockFile) || app()->config('app.generate_router', false);
    }
}
