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

require 'Autoload.php';

spl_autoload_register([new Autoload, 'load']);

App::start();


