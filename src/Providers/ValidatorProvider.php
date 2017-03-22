<?php
/**
 * User: macro chen <chen_macro@163.com>
 * Date: 17-3-21
 * Time: 下午1:25
 */

namespace Polymer\Providers;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\Validator\Validation;

class ValidatorProvider implements ServiceProviderInterface
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
        $pimple['validator'] = function (Container $container) {
            //AnnotationRegistry::registerFile(ROOT_PATH . "/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php");
            //AnnotationRegistry::registerAutoloadNamespace("Symfony\Component\Validator\Constraint", ROOT_PATH."/entity/Models");
            //AnnotationRegistry::registerAutoloadNamespace("Entity\\Models\\", ROOT_PATH . "/entity/Models");
            try {
                AnnotationRegistry::registerLoader('class_exists');
                $reader = new AnnotationReader();
                AnnotationReader::addGlobalIgnoredName('dummy');
                return Validation::createValidatorBuilder()->enableAnnotationMapping($reader)->getValidator();
            } catch (\Exception $e) {
                return null;
            }
        };
    }
}