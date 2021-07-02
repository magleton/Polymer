<?php

use Doctrine\Migrations\Tools\Console\Command\DiffCommand;
use Doctrine\Migrations\Tools\Console\Command\ExecuteCommand;
use Doctrine\Migrations\Tools\Console\Command\GenerateCommand;
use Doctrine\Migrations\Tools\Console\Command\MigrateCommand;
use Doctrine\Migrations\Tools\Console\Command\StatusCommand;
use Doctrine\Migrations\Tools\Console\Command\VersionCommand;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Doctrine\ORM\Version;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;

date_default_timezone_set('Asia/Shanghai');
defined('APPLICATION_ENV') || define('APPLICATION_ENV', 'development');
const ROOT_PATH = __DIR__;
const APP_NAME = 'tests';
const APP_PATH = ROOT_PATH . '/' . APP_NAME . '/';
require ROOT_PATH . '/vendor/autoload.php';
$app = new \Polymer\Boot\Application();
try {
    $app->startConsole();
} catch (Exception $e) {
}
$em = app()->db('db1', APP_PATH . '/Entity/Models');
$helperSet = new HelperSet(array(
    'em' => new EntityManagerHelper($em),
    'db' => new Doctrine\DBAL\Tools\Console\ConnectionProvider\SingleConnectionProvider($em->getConnection()),
    'dialog' => new QuestionHelper(),
));
$cli = new Application('Doctrine Command Line Interface', Version::VERSION);
$cli->setCatchExceptions(true);
$cli->setHelperSet($helperSet);
ConsoleRunner::addCommands($cli);
$cli->addCommands([
        new DiffCommand(),
        new ExecuteCommand(),
        new GenerateCommand(),
        new MigrateCommand(),
        new StatusCommand(),
        new VersionCommand()
    ]
);
try {
    $cli->run();
} catch (Exception $e) {
}
