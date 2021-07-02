<?php

use Polymer\Session\SecureHandler;

return [
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
                'name' => 'polymer',
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
                        //'primaryValue' =>                   'effective',    // or 'absolute'
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
    //服务供给者的命名空间
    'providers_path' => ['Polymer\\Providers'],

    //生成的路由文件存放路径
    'router_path' => [
        'router' => APP_PATH . 'Routers' . DIRECTORY_SEPARATOR . 'router.php',
        'router_files' => APP_PATH . 'Routers' . DIRECTORY_SEPARATOR . '*_router.php',
        'lock' => APP_PATH . DIRECTORY_SEPARATOR . 'Routers' . DIRECTORY_SEPARATOR . 'router.lock'
    ],

    //Session的处理类
    'session_handler' => [
        'cls' => SecureHandler::class,
        'params' => 'AAAAA'
    ],
    //AOP配置信息
    'aop' => [
        'init' => [
            'debug' => true,
            'cacheDir' => APP_PATH . '/log/aop/',
            'excludePaths' => [
                ROOT_PATH . '/vendor/',
            ],
        ]
    ]
];
