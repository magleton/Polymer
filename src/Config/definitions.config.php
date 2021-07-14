<?php

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\EventManager;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Polymer\Boot\Application;
use Polymer\Providers\RouterFileProvider;
use Polymer\Support\Collection;
use Polymer\Validator\GXValidator;
use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\DoctrineAdapter;
use Symfony\Component\Cache\DoctrineProvider;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\RecursiveValidator;

return [
    GXValidator::class => DI\create(GXValidator::class),
    RouterFileProvider::class => static function (ContainerInterface $container) {
        return new RouterFileProvider($container);
    },
    'errorCollection' => DI\create(Collection::class),
    EventManager::class => DI\create(EventManager::class),
    RecursiveValidator::class => static function (ContainerInterface $container) {
        /**
         * $reader = new AnnotationReader();
         * AnnotationReader::addGlobalIgnoredName('dummy');
         * if (extension_loaded('apcu')) {
         * $cache = new ApcuCache();
         * } else {
         * $cache = new ArrayCache();
         * }
         * return Validation::createValidatorBuilder()->setMappingCache(new DoctrineAdapter($cache))->enableAnnotationMapping($reader)->getValidator();
         */
        $reader = new AnnotationReader();
        AnnotationReader::addGlobalIgnoredName('dummy');
        $cache = new DoctrineProvider(new ArrayAdapter());
        return Validation::createValidatorBuilder()->setMappingCache(new DoctrineAdapter($cache))->enableAnnotationMapping($reader)->getValidator();
    },
    App::class => static function (ContainerInterface $container) {
        return AppFactory::createFromContainer($container);
    },
    Logger::class => static function (ContainerInterface $container) {
        $settings = $container->get('application')->getConfig('slim.settings');
        $logger = new Logger($settings['logger']['name']);
        $logger->pushProcessor(new UidProcessor());
        $logger->pushHandler(new StreamHandler($settings['logger']['path'], $settings['logger']['level']));
        return $logger;
    },
    'application' => DI\get(Application::class)
];