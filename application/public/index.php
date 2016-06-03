<?php
/**
* @desc 入口文件
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年5月30日    上午1:21:20
*/

ini_set('display_errors', 1);

//定义应用根目录。遵循PHP中__DIR__，定义路径都不带右/
define('APP_PATH', dirname(__DIR__));

//引入配置文件
$config = require APP_PATH . '/configs/main.php';

//引入框架启动文件
require dirname(APP_PATH) . '/framework/Bootstrap.php';

//运行应用运行
App::run($config);








