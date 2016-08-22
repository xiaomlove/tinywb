<?php
/**
* @desc 路由注册文件。必须命名为route.php,且位于应用根目录下。一个应用一个路由注册文件
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年6月2日    下午11:04:55
*/

use framework\Route;

Route::get('/', 'controllers\Index@index');
Route::get('user/{id}/{age}.html', 'controllers\User@profile', ['id' => '/^[\d]+$/', 'age' => '/^[\d]+$/']);
Route::get('user/list', 'controllers\User@list');
Route::get('user/welcome.html', 'controllers\User@welcome');
Route::get('user/{name}', 'controllers\User@info');

Route::get('tag/{tagName}', 'controllers\Index@tag');
Route::get('article/{id}.html', 'controllers\Index@detail', ['id' => '/^[\d]+$/']);
Route::get('console', 'controllers\Index@console');
Route::post('console', 'controllers\Index@consoleSubmit');
Route::get('test', 'controllers\Test@index');
Route::get('search', 'controllers\Index@search');
Route::get('swoole', 'controllers\Test@swoole');
Route::get('gearman', 'controllers\Test@gearman');
Route::get('url', 'controllers\Test@url');

Route::get('topicpv', 'controllers\Test@topicpv');