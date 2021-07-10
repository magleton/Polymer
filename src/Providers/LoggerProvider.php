<?php
/**
 * User: macro chen <chen_macro@163.com>
 * Date: 16-8-26
 * Time: 上午9:24
 */

namespace Polymer\Providers;

use DI\Container;
use Exception;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;

class LoggerProvider
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $diContainer A container instance
     */
    public function register(Container $diContainer): void
    {
        $diContainer->set(__CLASS__, static function () use ($diContainer) {
            try {
                $settings = $diContainer->get('application')->config('slim.settings');
                $logger = new Logger($settings['logger']['name']);
                $logger->pushProcessor(new UidProcessor());
                $logger->pushHandler(new StreamHandler($settings['logger']['path'], $settings['logger']['level']));
                return $logger;
            } catch (Exception $e) {
                return null;
            }
        });
    }
}