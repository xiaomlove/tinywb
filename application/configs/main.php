<?php

return [
    'show_running_info' => true,//展示运行信息
    
    //命名空间，名称 => 路径
	'namespaces' => [],
    
    //提供者，填写提供者完整类名
    'providers' => [],
    
    'db' => [
        'default' => [
            'dsn' => 'mysql:dbname=test;host=127.0.0.1;charset=utf8', 
            'user' => 'root', 
            'password' => '',
        ],
    ],
];