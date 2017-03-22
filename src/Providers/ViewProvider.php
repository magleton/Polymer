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
use Slim\Views\Twig;
use Slim\Views\TwigExtension;

class ViewProvider implements ServiceProviderInterface
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
        $pimple['view'] = function (Container $container) {
            $twig_config = $container['application']->config('twig') ? $container['application']->config('twig') : [];
            $view = new Twig(TEMPLATE_PATH, $twig_config);
            $view->addExtension(new TwigExtension($container['router'], $container['request']->getUri()));
            $view->addExtension(new \Twig_Extension_Profiler($container['twig_profile']));
            $view->addExtension(new \Twig_Extension_Debug());
            $view->getEnvironment()->addFunction(new \Twig_SimpleFunction('app', 'app'));
            return $view;
        };
    }
}