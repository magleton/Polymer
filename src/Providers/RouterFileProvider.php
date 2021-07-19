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
            $routerContents .= 'use Tuupola\Middleware\CorsMiddleware;' . "\n";
            $routerContents .= 'use Polymer\Middleware\GXParseRequestJSONMiddleware;' . "\n";
            $routerContents .= '$app = Application::getInstance()->get(App::class);' . "\n";
            $routerContents .= '$app->add(Application::getInstance()->get(CorsMiddleware::class));' . "\n";
            //$routerContents .= '$app->add(Application::getInstance()->get(GXParseRequestJSONMiddleware::class));' . "\n";
            //$routerContents .= '$app->add(Application::getInstance()->getDiContainer()->get(\'csrf\'));';
            if (Application::getInstance()->getConfig('middleware')) {
                foreach (Application::getInstance()->getConfig('middleware') as $key => $middleware) {
                    if (function_exists($middleware) && is_callable($middleware)) {
                        $routerContents .= "\n" . '$app->add("' . $middleware . '");';
                    } elseif (Application::getInstance()->get($middleware)) {
                        $routerContents .= "\n" . '$app->add(Application::getInstance()->get("' . $middleware . '"));';
                    } elseif (Application::getInstance()->get($key)) {
                        $routerContents .= "\n" . '$app->add(Application::getInstance()->get("' . $key . '"));';
                    } elseif (class_exists($middleware)) {
                        $routerContents .= "\n" . '$app->add("' . $middleware . '");';
                    }
                }
            }
            $routerContents .= "\n";
            foreach (glob(Application::getInstance()->getConfig('app.router_path.router_files',
                Application::getInstance()->getConfig('router_path.router_files'))) as $key => $file_name) {
                $contents = file_get_contents($file_name);
                preg_match_all('/app->[\s\S]*/', $contents, $matches);
                foreach ($matches[0] as $vv) {
                    $routerContents .= '$' . $vv . "\n";
                }
            }
            file_put_contents($routerFilePath, $routerContents);
            file_put_contents(Application::getInstance()->getConfig('app.router_path.lock',
                Application::getInstance()->getConfig('router_path.lock')),
                Application::getInstance()->getConfig('current_version'));
        }
        if (file_exists($routerFilePath)) {
            require_once $routerFilePath;
            return;
        }
        throw new Exception("路由文件不存在~~~");
    }
}
