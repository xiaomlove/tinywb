<?php

$config = [
    'show_running_info' => true,//展示运行信息
    
    //命名空间，名称 => 路径
	'namespaces' => [
	    'providers' => APP_PATH . '/providers',
	    'widgets' => APP_PATH . '/widgets',
	],
    
    //提供者，填写提供者完整类名
    'providers' => [
        'providers\SphinxProvider',
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