<?php

use Doctrine\Migrations\Tools\Console\Command\DiffCommand;
use Doctrine\Migrations\Tools\Console\Command\ExecuteCommand;
use Doctrine\Migrations\Tools\Console\Command\GenerateCommand;
use Doctrine\Migrations\Tools\Console\Command\MigrateCommand;
use Doctrine\Migrations\Tools\Console\Command\StatusCommand;
use Doctrine\Migrations\Tools\Console\Command\VersionCommand;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;

try {
    date_default_timezone_set('Asia/Shanghai');
    defined('APPLICATION_ENV') || define('APPLICATION_ENV', 'development');
    defined('ROOT_PATH') || define('ROOT_PATH', __DIR__);
    defined('APP_NAME') || define('APP_NAME', 'tests');
    defined('APP_PATH') || define('APP_PATH', ROOT_PATH . '/' . APP_NAME . '/');
    require ROOT_PATH . '/vendor/autoload.php';
    $app = new \Polymer\Boot\Application();
    try {
        $app->runConsole();
    } catch (Exception $e) {
    }
    $em = app()->db('db1', APP_PATH . '/Entity/Models');
    $helperSet = new HelperSet(array(
        'em' => new EntityManagerHelper($em),
        'db' => new Doctrine\DBAL\Tools\Console\ConnectionProvider\SingleConnectionProvider($em->getConnection()),
        'dialog' => new QuestionHelper(),
    ));
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
}
