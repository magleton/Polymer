<?php
/**
 * User: macro chen <macro_fengye@163.com>
 * Date: 17-7-6
 * Time: 上午11:26
 */

namespace Polymer\Boot;

use Exception;
use Go\Core\AspectContainer;
use Go\Core\AspectKernel;

class ApplicationAspectKernel extends AspectKernel
{
    /**
     * Configure an AspectContainer with advisors, aspects and pointcuts
     *
     * @param AspectContainer $container
     * @return void
     * @throws Exception
     */
    protected function configureAop(AspectContainer $container): void
    {
        try {
            $aspect = (array)Application::getInstance()->getConfig('app.aop.aspect');
            foreach ($aspect as $clazz) {
                if (class_exists($clazz)) {
                    $container->registerAspect(new $clazz());
                }
            }
        } catch (Exception $e) {
            throw  $e;
        }
    }
}