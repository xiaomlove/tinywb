<?php
/**
* @desc 框架约定配置。可以自己配置文件（就是入口run()方法中引入的那个配置文件）中覆盖之
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年6月4日    下午6:11:37
*/

return [
    'app_debug' => true,//默认开启debug模式
    
    'show_running_info' => true,//默认显示运行信息
    
    'use_framework_exception_handler' => 1,//是否使用框架的异常处理机制，为0不使用

    'use_framework_error_handler' => 1,//是否使用框架的错误处理机制，为0不使用
    
    'default_method_parameter' => '_method',//默认浏览器模拟PUT,DELETE等方法时使用参数名
    
    'default_view_path' => APP_PATH . '/views',//默认视图目录
    
    'default_layout_path' => APP_PATH . '/views/layouts',//默认布局文件目录
    
    //默认命名空间
    'default_namespaces' => [
        'controllers' => APP_PATH . '/controllers',
        'models' => APP_PATH . '/models',
        'services' => APP_PATH . '/services',
    ],
    
    //默认提供者
    'default_providers' => [
        'framework\providers\RequestProvider',
        'framework\providers\AppProvider',
        'framework\providers\ConfigProvider',
        'framework\providers\AutoloadProvider',
        'framework\providers\RouteProvider',
        'framework\providers\EventProvider',
    ],
    
    //默认事件
    'default_events' => [
        'app:start',
        'before:route',
        'after:route',
        'before:controller',
        'after:controller',
        'before:action',
        'after:action',
        'app:stop',
    ],
];