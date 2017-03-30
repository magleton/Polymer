<?php
/**
 * 数据库配置
 */
return [
    'db' => [
        // 开发模式
        'development' => [
            'db1' => [
                'wrapperClass' => Doctrine\DBAL\Sharding\PoolingShardConnection::class,
                'shardChoser' => Doctrine\DBAL\Sharding\ShardChoser\MultiTenantShardChoser::class,
                'driver' => 'pdo_mysql',
                'host' => '127.0.0.1',
                'port' => 3306,
                'user' => 'root',
                'password' => 'root',
                'dbname' => 'polymer',
                'charset' => 'UTF8',
                'global' => [
                    'driver' => 'pdo_mysql',
                    'host' => '127.0.0.1',
                    'port' => 3306,
                    'dbname' => 'polymer',
                    'user' => 'root',
                    'password' => 'root',
                    'charset' => 'UTF8',
                ],
                'shards' => [
                    [
                        'id' => 1,
                        'driver' => 'pdo_mysql',
                        'host' => '10.0.25.5',
                        'user' => 'wechat',
                        'password' => 'YNpG2KASHO94abIn',
                        'dbname' => 'YNpG2KASHO94abIn',
                        'charset' => 'UTF8',
                        'port' => 3306,
                    ],
                    [
                        'id' => 2,
                        'driver' => 'pdo_mysql',
                        'host' => '10.0.25.5',
                        'user' => 'wechat',
                        'password' => 'YNpG2KASHO94abIn',
                        'dbname' => 'wechat',
                        'charset' => 'UTF8',
                        'port' => 3306,
                    ],
                ],
                'useSimpleAnnotationReader' => false,
            ],

            'crm' => [
                'driver' => 'pdo_mysql',
                'host' => '10.0.25.2',
                'port' => '3308',
                'user' => 'root',
                'password' => '111111',
                'dbname' => 'xiaofei',
                'charset' => 'UTF8',
                'useSimpleAnnotationReader' => false,
            ],
        ]
    ],
];