<?php
$common_config = [
    //应用配置
    'slim' => [
        'mode' => APPLICATION_ENV,
        'settings' => [
            'httpVersion' => '1.1',
            'responseChunkSize' => 4096,
            'outputBuffering' => 'append',
            'addContentLengthHeader' => false,
            'routerCacheFile' => APP_PATH . 'Routers/routerCacheFile.router',
            'determineRouteBeforeAppMiddleware' => true,
            'displayErrorDetails' => true,
            'logger' => [
                'name' => 'macro_php',
                'level' => Monolog\Logger::DEBUG,
                'path' => APP_PATH . 'log/error.log',
            ],

            'tracy' => [
                'showPhpInfoPanel' => 0,
                'showSlimRouterPanel' => 0,
                'showSlimEnvironmentPanel' => 0,
                'showSlimRequestPanel' => 0,
                'showSlimResponsePanel' => 1,
                'showSlimContainer' => 0,
                'showEloquentORMPanel' => 0,
                'showTwigPanel' => 0,
                'showIdiormPanel' => 0,// > 0 mean you enable logging
                // but show or not panel you decide in browser in panel selector
                'showDoctrinePanel' => 'em',// here also enable logging and you must enter your Doctrine container name
                // and also as above show or not panel you decide in browser in panel selector
                'showProfilerPanel' => 0,
                'showVendorVersionsPanel' => 0,
                'showXDebugHelper' => 0,
                'showIncludedFiles' => 1,
                'showConsolePanel' => 0,
                'configs' => [
                    // XDebugger IDE key
                    'XDebugHelperIDEKey' => 'PHPSTORM',
                    // Disable login (don't ask for credentials, be careful) values( 1 || 0 )
                    'ConsoleNoLogin' => 0,
                    // Multi-user credentials values( ['user1' => 'password1', 'user2' => 'password2'] )
                    'ConsoleAccounts' => [
                        'dev' => '34c6fceca75e456f25e7e99531e2425c6c1de443'// = sha1('dev')
                    ],
                    // Password hash algorithm (password must be hashed) values('md5', 'sha256' ...)
                    'ConsoleHashAlgorithm' => 'sha1',
                    // Home directory (multi-user mode supported) values ( var || array )
                    // '' || '/tmp' || ['user1' => '/home/user1', 'user2' => '/home/user2']
                    'ConsoleHomeDirectory' => ['dev' => dirname(dirname(__DIR__))],
                    // terminal.js full URI
                    'ConsoleTerminalJs' => '/js/jquery.terminal.min.js',
                    // terminal.css full URI
                    'ConsoleTerminalCss' => '/css/jquery.terminal.min.css',

                    'ProfilerPanel' => [
                        // Memory usage 'primaryValue' set as Profiler::enable() or Profiler::enable(1)
//                    'primaryValue' =>                   'effective',    // or 'absolute'
                        'show' => [
                            'memoryUsageChart' => 1, // or false
                            'shortProfiles' => true, // or false
                            'timeLines' => true // or false
                        ]
                    ]
                ]
            ]
        ],
    ],

    //Doctrine配置
    'doctrine' => [
        'query_cache' => [
            'is_open' => true,
            'cache_name' => 'redisCacheDriver',
            'database' => 15
        ],
        'result_cache' => [
            'is_open' => true,
            'cache_name' => 'redisCacheDriver',
            'database' => 15
        ],
        'metadata_cache' => [
            'is_open' => true,
            'cache_name' => 'redisCacheDriver',
            'database' => 15
        ],
    ],

    // Cookie配置
    'cookies' => [
        'expires' => '60 minutes',
        'path' => '/',
        'domain' => null,
        // 'secure' => true,
        'httponly' => true,
        'name' => 'macro_php',
        'secret' => '',
        'cipher' => MCRYPT_RIJNDAEL_256,
        'cipher_mode' => MCRYPT_MODE_CBC,
    ],

    // Session配置
    'session' => [
        'manager' => [
            'remember_me_seconds' => 1200,
            'name' => 'macro_php',
            // 'phpSaveHandler' => 'redis',
            // 'savePath' => 'tcp://127.0.0.1:6379?weight=1&timeout=1',
            'use_cookies' => true,
            //'cookie_secure'=>true,
            'cookie_domain' => 'xiaofei.com',
            'cookie_httponly' => true,
            //'cookie_lifetime' => 3600
        ],
        'container' => [
            'namespace' => 'macro_php',
        ],
    ],

    //自定义配置
    'customer' => [
        'encrypt_key' => 'xxxxx',//加密的KEY
        'cache_router' => true, //是否缓存路由文件
        'router_cache_file' => APP_PATH . 'Routers/routerCacheFile.router', //路由缓存文件的路径
        'is_rest' => true, //接口形式提供服务
        'is_api_rate_limit' => false,  // API速率限制
        'show_use_memory' => false,
        'initial_epoch' => 1476614506000, //用于SnowFlake产生ID
    ],

    //定义数据缓存的Cache
    'data_cache' => [
        'redis_cache' => [
            'is_open' => true,
            'cache_name' => 'redisCache'
        ],
        'memcache_cache' => [
            'is_open' => true,
            'cache_name' => 'memcacheCache'
        ],
    ],

    //IP地址白名单列表
    'ip_list' => [
        '127.0.0.1'
    ],

    //Session的处理类
    'session_handler' => [
        'cls' => \Polymer\Session\SecureHandler::class,
        'params' => ''
    ]
];

return $common_config;