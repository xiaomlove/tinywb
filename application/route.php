<?php
/**
* @desc 路由注册文件。必须命名为route.php,且位于应用根目录下。一个应用一个路由注册文件
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年6月2日    下午11:04:55
*/

use framework\Route;

Route::get('/', 'controllers\IndexController@index');
Route::get('user/{id}/{age}.html', 'controllers\UserController@profile', ['id' => '[\d]+']);
Route::get('user/list', 'controllers\UserController@list');
Route::get('user/welcome.html', 'controllers\UserController@welcome');
Route::get('user/{name}', 'controllers\UserController@info');
