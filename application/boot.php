<?php
/**
* @desc 程序启动文件。
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年7月23日    下午4:20:13
*/
$app = app();
$event = $app['event'];
$loader = $app['autoload'];

//添加一个命名空间
$loader->addNamespace('common', APP_PATH . '/common');

//为事件添加一个绑定
$event->on('app:start', function() {
    require APP_PATH . '/common/functions.php';
});

$event->on('app:stop', function() {
    //往异步队列添加任务，如写访问日志。。。
});




