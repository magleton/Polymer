<?php

use Doctrine\Migrations\Tools\Console\Command\DiffCommand;
use Doctrine\Migrations\Tools\Console\Command\ExecuteCommand;
use Doctrine\Migrations\Tools\Console\Command\GenerateCommand;
use Doctrine\Migrations\Tools\Console\Command\MigrateCommand;
use Doctrine\Migrations\Tools\Console\Command\StatusCommand;
use Doctrine\Migrations\Tools\Console\Command\VersionCommand;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Monolog\Logger;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;

date_default_timezone_set('Asia/Shanghai');
defined('APPLICATION_ENV') || define('APPLICATION_ENV', 'development');
defined('DS') || define('DS', DIRECTORY_SEPARATOR);
defined('ROOT_PATH') || define('ROOT_PATH', __DIR__);
defined('APP_NAME') || define('APP_NAME', 'tests');
defined('APP_PATH') || define('APP_PATH', ROOT_PATH . DS . 'app' . DS . APP_NAME . DS);
$loader = require __DIR__ . '/vendor/autoload.php';
$app = new \Polymer\Boot\Application();
try {
    $app->runConsole();
    $em = \Polymer\Boot\Application::getInstance()->db('db1', APP_PATH . 'Entity' . DS . 'Mapping');
    $helperSet = new HelperSet([
        new EntityManagerHelper($em)
    ]);
    $cli = new Application('Doctrine Command Line Interface', 'UNKNOWN');
    $cli->setCatchExceptions(true);
    $cli->setHelperSet($helperSet);
    $cli->addCommands([
            new DiffCommand(),
            new ExecuteCommand(),
            new GenerateCommand(),
            new MigrateCommand(),
            new StatusCommand(),
            new VersionCommand()
        ]
    );
    ConsoleRunner::addCommands($cli);
    $cli->run();
} catch (Exception $e) {
    $app->get(Logger::class)->error($e);
}
