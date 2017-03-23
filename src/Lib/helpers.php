<?php
use Polymer\Boot\Application;

if (!function_exists('app')) {
    /**
     * 获取应用实例
     * @author <macro_fengye@163.com> macro chen
     * @param null $make 是否返回对象实例
     * @param array $parameters
     * @return Application
     */
    function app($make = null, array $parameters = [])
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
     * @author <macro_fengye@163.com> macro chen
     * @param $message
     * @param array $content
     * @param string $file
     * @param string $log_name
     * @param int $level
     */
    function logger($message, array $content, $file = '', $log_name = "LOG", $level = \Monolog\Logger::WARNING)
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
        $logger = new \Monolog\Logger($log_name);
        $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
        $logger->pushHandler(new \Monolog\Handler\StreamHandler($file ? $file : APP_PATH . '/log/log.log', $level));
        $function_name = $levels[$level];
        $logger->$function_name($message, $content);
    }
}

if (!function_exists('handleShutdown')) {
    /**
     * PHP错误处理函数
     *
     * @author <macro_fengye@163.com> macro chen
     */
    function handleShutdown()
    {
        $error = error_get_last();
        if ($error["type"] === E_ERROR) {
            if (app()->config('logger')) {
                $msg = 'Type : ' . $error["type"] . '\nMessage : ' . $error["message"] . '\nFile : ' . $error["file"] . '\nLine : ' . $error["line"];
                app()->config('logger')->error($msg);
            } else {
                $msg = 'Type : ' . $error["type"] . ' , Message : ' . $error["message"] . ' , File : ' . $error["file"] . ' , Line : ' . $error["line"];
                logger('Fatal Error : ', [$msg], APP_PATH . '/log/fatal_error.log', Monolog\Logger::ERROR);
                if (defined('TEMPLATE_PATH') && file_exists(TEMPLATE_PATH . 'error.twig')) {
                    echo @file_get_contents(TEMPLATE_PATH . 'error.twig');
                } else {
                    echo \GuzzleHttp\json_encode(['code' => 2000, 'msg' => 'Error', 'data' => []]);
                }
            }
        }
    }
}


if (!function_exists('handleError')) {
    /**
     * 自定义的错误处理函数
     * @author <macro_fengye@163.com> macro chen
     * @param $level
     * @param $message
     * @param string $file
     * @param int $line
     * @param array $context
     * @throws ErrorException
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
     * @author <macro_fengye@163.com> macro chen
     * @param ParseError $e
     * @throws ParseError
     */
    function handleException(ParseError $e)
    {
        throw $e;
    }
}

if (PHP_MAJOR_VERSION === 5 && !function_exists('handleException')) {
    /**
     * 自定义的异常处理函数
     * @author <macro_fengye@163.com> macro chen
     * @param Exception $e
     * @throws Exception
     */
    function handleException(Exception $e)
    {
        throw $e;
    }
}

if (!function_exists('debugger')) {
    /**
     * 调试应用的各种性能
     *
     * @param int $level 开发模式
     * @param null $dbName 如果需要开启数据库的记录,则需要传递数据库名字
     * @param string $logPath 记录日志的路径
     */
    function debugger($level = 0, $dbName = null, $logPath = APP_PATH . '/log')
    {
        if ($dbName) {
            $em = app()->db($dbName);
            $doctrineConfig = $em->getConfiguration();
            $doctrineConfig->setSQLLogger(new \Doctrine\DBAL\Logging\DebugStack());
            app()->component('app')->getContainer()->offsetSet('doctrineConfig', $doctrineConfig);
        }
        \RunTracy\Helpers\Profiler\Profiler::enable(true);
        \Tracy\Debugger::enable($level ? \Tracy\Debugger::PRODUCTION : \Tracy\Debugger::DEVELOPMENT, $logPath);
    }
}

if (!function_exists('weChatConfig')) {
    /**
     * 获取微信的配置信息
     *
     * @return array
     */
    function weChatConfig()
    {
        $request = app()->component('request');
        if (PHP_SAPI === 'cli') {
            $appId = $request->getServerParam('argc') >= 2 ? $request->getServerParam('argv')[1] : 0;
        } else {
            $appId = $request->getParam('app_id') ?: 0;
        }
        $weChatConfig = [];
        $wxApp = app()->repository('app', 'db1')->find($appId);
        if (null !== $wxApp) {
            $weChatConfig = [
                'debug' => $wxApp->getDebug(),
                'app_id' => $wxApp->getWxAppId(),
                'secret' => $wxApp->getWxAppKey(),
                'token' => $wxApp->getToken(),
                'oauth' => [
                    'scopes' => [$wxApp->getScopes()],
                    'callback' => $wxApp->getCallbackUrl(),
                ],
                'aes_key' => $wxApp->getAesKey(),
                'name' => $wxApp->getWxUserName()
            ];
        }
        return $weChatConfig;
    }
}