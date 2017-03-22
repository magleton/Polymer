<?php
/**
 * Created by PhpStorm.
 * User: macro
 * Date: 16-8-26
 * Time: 上午9:24
 */
namespace Polymer\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Monolog\Handler\StreamHandler;

class LoggerProvider implements ServiceProviderInterface
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
        $pimple['logger'] = function (Container $container) {
            $settings = $container['application']->config('slim.settings');
            $logger = new Logger($settings['logger']['name']);
            $logger->pushProcessor(new UidProcessor());
            $logger->pushHandler(new StreamHandler($settings['logger']['path'], $settings['logger']['level']));
            return $logger;
        };
    }
}