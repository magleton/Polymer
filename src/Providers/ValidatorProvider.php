<?php
/**
 * User: macro chen <chen_macro@163.com>
 * Date: 17-3-21
 * Time: 下午1:25
 */

namespace Polymer\Providers;

use DI\Container;
use Doctrine\Common\Annotations\AnnotationReader;
use Exception;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\DoctrineAdapter;
use Symfony\Component\Cache\DoctrineProvider;
use Symfony\Component\Validator\Validation;

class ValidatorProvider
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
        $diContainer->set(__CLASS__, static function () use ($diContainer) {
            try {
                $reader = new AnnotationReader();
                AnnotationReader::addGlobalIgnoredName('dummy');
                $cache = new DoctrineProvider(new ArrayAdapter());
                return Validation::createValidatorBuilder()->setMappingCache(new DoctrineAdapter($cache))->enableAnnotationMapping($reader)->getValidator();
            } catch (Exception $e) {
                return null;
            }
        });
    }
}
