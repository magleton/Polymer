<?php
/**
 * User: macro chen <chen_macro@163.com>
 * Date: 16-8-26
 * Time: 下午4:28
 */

namespace Polymer\Providers;

use DI\Annotation\Inject;
use Exception;
use Noodlehaus\Exception\EmptyDirectoryException;
use Polymer\Boot\Application;
use Psr\Container\ContainerInterface;

class RouterFileProvider
{
    /**
     * @Inject
     * RouterFileProvider constructor.
     * @param ContainerInterface $container
     * @throws EmptyDirectoryException
     */
    public function __construct(ContainerInterface $container)
    {
        $routerFilePath = $container->get(Application::class)->getConfig('app.router_path.router',
            $container->get(Application::class)->getConfig('router_path.router'));
        if (routeGeneration()) {
            if (file_exists($container->get(Application::class)->getConfig('slim.settings.routerCacheFile'))) {
                @unlink($container->get(Application::class)->getConfig('slim.settings.routerCacheFile'));
            }
            $routerContents = '<?php' . "\n";
            $routerContents .= 'use Polymer\Boot\Application;' . "\n";
            $routerContents .= 'use Slim\App;' . "\n";
            $routerContents .= '$app = Application::getInstance()->getDiContainer()->get(App::class);' . "\n";
            $routerContents .= '$app->add(Application::getInstance()->getDiContainer()->get(\'corsMiddleware\'));' . "\n";
            //$routerContents .= '$app->add(Application::getInstance()->getDiContainer()->get(\'csrf\'));';
            if ($container->get(Application::class)->getConfig('middleware')) {
                foreach ($container->get(Application::class)->getConfig('middleware') as $key => $middleware) {
                    if (function_exists($middleware) && is_callable($middleware)) {
                        $routerContents .= "\n" . '$app->add("' . $middleware . '");';
                    } elseif ($container->get(Application::class)->get($middleware)) {
                        $routerContents .= "\n" . '$app->add($container->get("application")->get("' . $middleware . '"));';
                    } elseif ($container->get(Application::class)->get($key)) {
                        $routerContents .= "\n" . '$app->add($container->get("application")->get("' . $key . '"));';
                    } elseif (class_exists($middleware)) {
                        $routerContents .= "\n" . '$app->add("' . $middleware . '");';
                    }
                }
            }
            $routerContents .= "\n";
            foreach (glob($container->get(Application::class)->getConfig('app.router_path.router_files',
                $container->get(Application::class)->getConfig('router_path.router_files'))) as $key => $file_name) {
                $contents = file_get_contents($file_name);
                preg_match_all('/app->[\s\S]*/', $contents, $matches);
                foreach ($matches[0] as $kk => $vv) {
                    $routerContents .= '$' . $vv . "\n";
                }
            }
            file_put_contents($routerFilePath, $routerContents);
            file_put_contents($container->get(Application::class)->getConfig('app.router_path.lock',
                $container->get(Application::class)->getConfig('router_path.lock')),
                $container->get(Application::class)->getConfig('current_version'));
        }
        if (file_exists($routerFilePath)) {
            require_once $routerFilePath;
            return;
        }
        throw new Exception("路由文件不存在~~~");
    }
}
