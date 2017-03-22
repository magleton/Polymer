<?php
date_default_timezone_set('Asia/Shanghai');
defined('APPLICATION_ENV') || define('APPLICATION_ENV', 'development');
define('ROOT_PATH', __DIR__);
define('APP_NAME', 'task');
define('APP_PATH', ROOT_PATH . '/' . APP_NAME . '/');
define('CONFIG_PATH', ROOT_PATH . '/' . APP_NAME . '/');
require ROOT_PATH . '/vendor/autoload.php';
$app = new \Polymer\Boot\Application();
$app->startConsole();
$em = app()->db('db1', ROOT_PATH . '/entity/Mapping');
$helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
    'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em),
    'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($em->getConnection()),
    'dialog' => new \Symfony\Component\Console\Helper\QuestionHelper(),
));
$cli = new \Symfony\Component\Console\Application('Doctrine Command Line Interface', \Doctrine\ORM\Version::VERSION);
$cli->setCatchExceptions(true);
$cli->setHelperSet($helperSet);
\Doctrine\ORM\Tools\Console\ConsoleRunner::addCommands($cli);
$cli->addCommands([
        new \Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand(),
        new \Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand(),
        new \Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand(),
        new \Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand(),
        new \Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand(),
        new \Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand()
    ]
);
$cli->run();
