<?php
/**
 * User: macro chen <chen_macro@163.com>
 * Date: 16-8-26
 * Time: 上午9:24
 */

namespace Polymer\Providers;

use DI\Container;
use Polymer\Boot\Application;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use Twig\Extension\DebugExtension;
use Twig\Extension\ProfilerExtension;
use Twig\TwigFunction;
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
     * @param Container $container A container instance
     */
    public function create(Container $container): Twig
    {
        $twigConfig = $container->get(Application::class)->getConfig('twig') ?: [];
        $twig = Twig::create(TEMPLATE_PATH, $twigConfig);
        $twig->addExtension(new TwigExtension($container->get('router'), $container->get('request')->getUri()));
        $twig->addExtension(new ProfilerExtension($container->get('twig_profile')));
        $twig->addExtension(new DebugExtension());
        $twig->getEnvironment()->addFunction(new TwigFunction('app', 'app'));
        return $twig;
    }
}
