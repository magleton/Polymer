<?php

use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\NoopWordInflector;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Noodlehaus\Exception\EmptyDirectoryException;
use Polymer\Boot\Application;

if (!function_exists('app')) {
    /**
     * 获取全局的Application对象
     * @return Application
     */
    function app(): Application
    {
        return Application::getInstance();
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
            if (is_null(Application::getInstance()->getConfig('logger'))) {
                return;
            }
            Application::getInstance()->getConfig('logger')->error($msg);
            return;
        }
        if ($error['type'] === E_ERROR) {
            if (Application::getInstance()->getConfig('logger')) {
                $msg = 'Type : ' . $error['type'] . '\nMessage : ' . $error['message'] . '\nFile : ' . $error['file'] . '\nLine : ' . $error['line'];
                Application::getInstance()->getConfig('logger')->error($msg);
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
        $routerLockFile = Application::getInstance()->getConfig('app.router_path.lock', Application::getInstance()->getConfig('router_path.lock'));
        return !file_exists($routerLockFile) || Application::getInstance()->getConfig('app.router_path.generate_router', false);
    }
}

if (!function_exists('getInflector')) {

    /**
     * 将下划线转为驼峰
     * table_name =>  tableName
     *
     * @return Inflector
     */
    function getInflector(): Inflector
    {
        return new Inflector(new NoopWordInflector(), new NoopWordInflector());
    }
}
