<?php
/**
 * User: macro chen <macro_fengye@163.com>
 * Date: 17-7-6
 * Time: 上午11:22
 */

namespace Polymer\Providers;

use DI\Container;
use Polymer\Boot\ApplicationAspectKernel;

class AopProvider
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
        $diContainer->set('aop', static function (Container $diContainer) {
            $aspectKernel = ApplicationAspectKernel::getInstance();
            $aspectKernel->init(array_merge($diContainer->get('application')->config('aop.init', []), $diContainer->get('application')->config('app.aop.init', [])));
            return $aspectKernel;
        });
    }
}
