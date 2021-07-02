<?php
/**
 * User: macro chen <chen_macro@163.com>
 * Date: 16-8-26
 * Time: 下午4:28
 */

namespace Polymer\Providers;

use Exception;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class RouterFileProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $pimpleContainer A container instance
     */
    public function register(Container $pimpleContainer): void
    {
        $pimpleContainer['routerFile'] = static function (Container $container) {
            $routerFilePath = $container['application']->config('app.router_path.router', $container['application']->config('router_path.router'));
            if (routeGeneration()) {
                if (file_exists($container['application']->config('slim.settings.routerCacheFile'))) {
                    @unlink($container['application']->config('slim.settings.routerCacheFile'));
                }

                $routerContents = '<?php' . "\n";
                $routerContents .= 'use Polymer\Boot\Application;' . "\n";
                $routerContents .= '$app = Application::getInstance()->component("app");';
                if ($container['application']->config('middleware')) {
                    foreach ($container['application']->config('middleware') as $key => $middleware) {
                        if (function_exists($middleware) && is_callable($middleware)) {
                            $routerContents .= "\n" . '$app->add("' . $middleware . '");';
                        } elseif ($container['application']->component($middleware)) {
                            $routerContents .= "\n" . '$app->add($container[\'application\']->component("' . $middleware . '"));';
                        } elseif ($container['application']->component($key)) {
                            $routerContents .= "\n" . '$app->add($container[\'application\']->component("' . $key . '"));';
                        } elseif (class_exists($middleware)) {
                            $routerContents .= "\n" . '$app->add("' . $middleware . '");';
                        }
                    }
                }
                $routerContents .= "\n";
                foreach (glob($container['application']->config('app.router_path.router_files', $container['application']->config('router_path.router_files'))) as $key => $file_name) {
                    $contents = file_get_contents($file_name);
                    preg_match_all('/app->[\s\S]*/', $contents, $matches);
                    foreach ($matches[0] as $kk => $vv) {
                        $routerContents .= '$' . $vv . "\n";
                    }
                }
                file_put_contents($routerFilePath, $routerContents);
                file_put_contents($container['application']->config('app.router_path.lock', $container['application']->config('router_path.lock')), $container['application']->config('current_version'));
            }
            if (file_exists($routerFilePath)) {
                require_once $routerFilePath;
            }
            throw new Exception("路由文件不存在~~~");
        };
    }
}
