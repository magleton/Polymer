<?php
/**
 * User: macro chen <chen_macro@163.com>
 * Date: 17-3-21
 * Time: 下午1:25
 */

namespace Polymer\Providers;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\ApcuCache;
use Doctrine\Common\Cache\ArrayCache;
use Exception;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\Cache\Adapter\DoctrineAdapter;
use Symfony\Component\Validator\Mapping\Cache\DoctrineCache;
use Symfony\Component\Validator\Validation;

class ValidatorProvider implements ServiceProviderInterface
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
        $pimpleContainer['validator'] = function (Container $container) {
            try {
                //AnnotationRegistry::registerLoader('class_exists');
                $reader = new AnnotationReader();
                AnnotationReader::addGlobalIgnoredName('dummy');
                if (extension_loaded('apcu')) {
                    $cache = new ApcuCache();
                } else {
                    $cache = new ArrayCache();
                }
                return Validation::createValidatorBuilder()->setMappingCache(new DoctrineAdapter($cache))->enableAnnotationMapping($reader)->getValidator();
                //return Validation::createValidatorBuilder()->setMetadataCache(new DoctrineCache($cache))->enableAnnotationMapping($reader)->getValidator();
            } catch (Exception $e) {
                return null;
            }
        };
    }
}
