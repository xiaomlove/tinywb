<?php
/**
* @desc 框架约定配置。可以自己配置文件（就是入口run()方法中引入的那个配置文件）中覆盖之
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年6月4日    下午6:11:37
*/

return [
    'use_framework_exception_handler' => 1,//是否使用框架的异常处理机制，为0不使用

    'use_framework_error_handler' => 1,//是否使用框架的错误处理机制，为0不使用
    
    'method_parameter' => '_method',//浏览器模拟PUT,DELETE等方法时使用参数名
    
    //默认提供者
    'default_providers' => [
        'framework\providers\RequestProvider',
        'framework\providers\AppProvider',
        'framework\providers\ConfigProvider',
    ],
];