<?php
/**
* @desc CLI模式启动文件
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年8月13日    下午5:24:52
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
    echo "app cli stop! \n";
});





