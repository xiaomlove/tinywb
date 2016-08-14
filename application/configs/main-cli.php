<?php

$config = [
    //命名空间，名称 => 路径
	'namespaces' => [
	    'providers' => APP_PATH . '/providers',
	    'cli' => APP_PATH . '/cli',
	],
    
    //提供者，填写提供者完整类名
    'providers' => [
        'providers\SphinxProvider',
        'providers\SwooleClientProvider',
        'providers\GearmanClientProvider',
        'providers\AsyncTaskProvider',
    ],
    
    'db' => [
        'default' => [
            'dsn' => 'mysql:dbname=test;host=127.0.0.1;charset=utf8', 
            'user' => 'root', 
            'password' => '',
        ],
    ],
];

return array_merge($config, require(dirname(__DIR__) . '/config-local.php'));