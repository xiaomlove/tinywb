<?php
/**
* @desc 入口文件
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年5月30日    上午1:21:20
*/

//定义应用根目录。遵循PHP中__DIR__，定义路径都不带右/
define('APP_PATH', dirname(__DIR__));

//引入框架启动文件
require dirname(APP_PATH) . '/framework/Bootstrap.php';

//引入配置文件
$config = require APP_PATH . '/configs/main.php';

//创建应用程序，并使用配置初始化
$app = framework\App::getInstance()->init($config);

//引入程序启动文件
require APP_PATH . '/boot.php';

//处理请求
$app->handle();







