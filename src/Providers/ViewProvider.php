<?php
/**
 * User: macro chen <chen_macro@163.com>
 * Date: 16-8-26
 * Time: 上午9:24
 */

namespace Polymer\Providers;

use DI\Container;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use Twig_Extension_Debug;
use Twig_Extension_Profiler;
use Twig_SimpleFunction;

class ViewProvider
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
        $pimpleContainer['view'] = function (Container $container) {
            $twig_config = $container['application']->config('twig') ?: [];
            $view = new Twig(TEMPLATE_PATH, $twig_config);
            $view->addExtension(new TwigExtension($container['router'], $container['request']->getUri()));
            $view->addExtension(new Twig_Extension_Profiler($container['twig_profile']));
            $view->addExtension(new Twig_Extension_Debug());
            $view->getEnvironment()->addFunction(new Twig_SimpleFunction('app', 'app'));
            return $view;
        };
    }
}
