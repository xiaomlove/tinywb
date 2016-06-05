<?php
/**
* @desc 启动文件，主要是注册自动加载！ 
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年6月3日    上午2:34:52
*/

namespace framework;

if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    die('最低需要PHP 5.5.0 !');
}

if (!defined('APP_PATH') || !is_dir(APP_PATH)) {
    die('请先定义应用目录常量APP_PATH!');
}

require 'Constants.php';
require 'Autoload.php';
require 'helpers/functions.php';

$loader = Autoload::getInstance();
$loader->register();
$loader->addClassMap(require('ClassMaps.php'));
$loader->addNamespace('framework', FRAMEWORK_PATH);


// print_r($loader->getPrefixes());
// print_r($loader->getClassMaps());






