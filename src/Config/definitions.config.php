<?php

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\EventManager;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Polymer\Boot\Application;
use Polymer\Middleware\GXCORSMiddleware;
use Polymer\Middleware\GXCsrfMiddleware;
use Polymer\Middleware\GXParseRequestJSONMiddleware;
use Polymer\Middleware\GXTwigMiddleware;
use Polymer\Providers\RouterFileProvider;
use Polymer\Providers\SessionProvider;
use Polymer\Providers\ViewProvider;
use Polymer\Support\Collection;
use Polymer\Validator\GXValidator;
use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Views\TwigMiddleware;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\DoctrineAdapter;
use Symfony\Component\Cache\DoctrineProvider;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use Tuupola\Middleware\CorsMiddleware;

return [
    GXValidator::class => DI\create(GXValidator::class),
    RouterFileProvider::class => static function (ContainerInterface $container) {
        return new RouterFileProvider($container);
    },
    'errorCollection' => DI\create(Collection::class),
    EventManager::class => DI\create(EventManager::class),
    RecursiveValidator::class => static function (ContainerInterface $container) {
        $reader = new AnnotationReader();
        AnnotationReader::addGlobalIgnoredName('dummy');
        $cache = new DoctrineProvider(new ArrayAdapter());
        return Validation::createValidatorBuilder()->setMappingCache(new DoctrineAdapter($cache))->enableAnnotationMapping($reader)->getValidator();
    },
    'validator' => DI\get(RecursiveValidator::class),
    App::class => static function (ContainerInterface $container) {
        $app = AppFactory::createFromContainer($container);
        $app->addRoutingMiddleware();
        $app->addErrorMiddleware(true, true, true);
        $routeCollector = $app->getRouteCollector();
        //$routeCollector->setCacheFile();
        return $app;
    },
    Logger::class => static function (ContainerInterface $container) {
        $settings = $container->get(Application::class)->getConfig('slim.settings');
        $logger = new Logger($settings['logger']['name']);
        $logger->pushProcessor(new UidProcessor());
        $logger->pushHandler(new StreamHandler($settings['logger']['path'], $settings['logger']['level']));
        return $logger;
    },
    'application' => DI\get(Application::class),
    CorsMiddleware::class => DI\factory(function (ContainerInterface $c) {
        $middleware = new GXCORSMiddleware();
        return $middleware->create($c);
    }),
    'corsMiddleware' => DI\get(CorsMiddleware::class),
    'csrf' => DI\factory(function (ContainerInterface $c) {
        $middleware = new GXCsrfMiddleware();
        return $middleware->create($c);
    }),
    Serializer::class => DI\factory(static function (ContainerInterface $container) {
        $encoders = array(new XmlEncoder(), new JsonEncoder(), new YAMLEncoder());
        $normalizers = array(new ObjectNormalizer(), new GetSetMethodNormalizer(), new PropertyNormalizer());
        return new Serializer($normalizers, $encoders);
    }),
    SessionProvider::class => DI\factory(function (ContainerInterface $container) {
        $sessionProvider = new SessionProvider();
        return $sessionProvider->create($container);
    }),
    ViewProvider::class => DI\factory(static function (ContainerInterface $container) {
        $viewProvider = new ViewProvider();
        return $viewProvider->create($container);
    }),
    TwigMiddleware::class => DI\factory(static function (ContainerInterface $container) {
        $middleware = new GXTwigMiddleware();
        return $middleware->create($container);
    }),
    'parseRequestJSONMiddleware' => DI\factory(static function (ContainerInterface $container) {
        return new  GXParseRequestJSONMiddleware($container);
    }),
];