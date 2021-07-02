<?php
/**
 * User: <macro_fengye@163.com> Macro Chen
 * Date: 16-9-8
 * Time: 上午8:38
 */
namespace Polymer\Providers;

use Polymer\Session\Session;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class SessionProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $pimpleContainer A container instance
     */
    public function register(Container $pimpleContainer)
    {
        $pimpleContainer['session'] = function (Container $container) {
            ini_set('session.save_handler', 'files');
            $sessionHandler = $container['application']->config('session_handler.cls');
            if (class_exists($sessionHandler)) {
                $handler = new $sessionHandler($container['application']->config('session_handler.params'));
                session_set_save_handler($handler, true);
                $session = new Session();
                $session->start();
                return $session;
            }
            return null;
        };
    }
}