<?php
/**
* @desc 启动文件，主要是注册自动加载！ 
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年6月3日    上午2:34:52
*/

namespace framework;

if (version_compare(PHP_VERSION, '5.4.0', '<')) {
    die('最低需要PHP 5.4.0 !');
}

if (!defined('APP_PATH') || !is_dir(APP_PATH)) {
    die('请先定义应用目录常量APP_PATH!');
}

define('APP_START_TIME', microtime(true));

require 'Autoload.php';

$loader = Autoload::getInstance();
$loader->register();
$loader->addClassMap(require('ClassMaps.php'));
$loader->addNamespace('framework', __DIR__);


//完成初始化之前才定义相关的常量、函数等。在此之前的都不依赖于它们，它们的定义依赖于框架初始化完
require 'helpers/functions.php';

// print_r($loader->getPrefixes());
// print_r($loader->getClassMaps());








