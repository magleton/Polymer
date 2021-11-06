<?php
//数据库配置
use Doctrine\DBAL\Connections\PrimaryReadReplicaConnection;
use Doctrine\ORM\Mapping\DefaultNamingStrategy;

return [
    'db' => [
        // 开发模式
        'development' => [
            'db1' => [
                'wrapperClass' => PrimaryReadReplicaConnection::class,
                'namingStrategy' => DefaultNamingStrategy::class,
                'driver' => 'pdo_mysql',
                'primary' => ['user' => '', 'password' => '', 'host' => '192.168.56.101', 'dbname' => 'mydb', 'port' => 3306, "charset" => "UTF8"],
                'replica' => [
                    ['user' => 'replica1', 'password', 'host' => '', 'dbname' => ''],
                    ['user' => 'replica2', 'password', 'host' => '', 'dbname' => '']
                ],
                'useSimpleAnnotationReader' => false,
                'emCacheKey' => 'abc',
            ],
            'db2' => [
                'wrapperClass' => PrimaryReadReplicaConnection::class,
                'namingStrategy' => DefaultNamingStrategy::class,
                'driver' => 'pdo_mysql',
                'primary' => ['user' => '', 'password' => '', 'host' => '192.168.56.101', 'dbname' => 'mydb', 'port' => 3306, "charset" => "UTF8"],
                'replica' => [
                    ['user' => 'replica1', 'password', 'host' => '', 'dbname' => ''],
                    ['user' => 'replica2', 'password', 'host' => '', 'dbname' => '']
                ],
                'useSimpleAnnotationReader' => true,
                'emCacheKey' => 'abc',
            ]],
        // 生产模式
        'production' => [
            'db1' => [
                'wrapperClass' => PrimaryReadReplicaConnection::class,
                'namingStrategy' => DefaultNamingStrategy::class,
                'driver' => 'pdo_mysql',
                'primary' => ['user' => '', 'password' => '', 'host' => '192.168.56.101', 'dbname' => 'mydb', 'port' => 3306, "charset" => "UTF8"],
                'replica' => [
                    ['user' => 'replica1', 'password', 'host' => '', 'dbname' => ''],
                    ['user' => 'replica2', 'password', 'host' => '', 'dbname' => '']
                ],
                'useSimpleAnnotationReader' => false,
                'emCacheKey' => 'abc',
            ],
            'db2' => [
                'wrapperClass' => PrimaryReadReplicaConnection::class,
                'namingStrategy' => DefaultNamingStrategy::class,
                'driver' => 'pdo_mysql',
                'primary' => ['user' => '', 'password' => '', 'host' => '192.168.56.101', 'dbname' => 'mydb', 'port' => 3306, "charset" => "UTF8"],
                'replica' => [
                    ['user' => 'replica1', 'password', 'host' => '', 'dbname' => ''],
                    ['user' => 'replica2', 'password', 'host' => '', 'dbname' => '']
                ],
                'useSimpleAnnotationReader' => true,
                'emCacheKey' => 'abc',
            ]],
    ],
];