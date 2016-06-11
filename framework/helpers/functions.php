<?php
/**
* @desc 全局函数。PHP强大的函数和数组是其特色，不要说直接这么定义函数显得low!简单易用是王道!
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年6月4日    下午5:36:49
*/

use framework\Config;
use framework\Request;
use framework\App;
use framework\exceptions\AppExceptionHandler;

/**
 * 打印变量
 * @param unknown $data
 * @param string $die
 */
function dump() {
   $todumps = todump('__dump');
   $vars = array_merge($todumps, func_get_args());
   AppExceptionHandler::getInstance()->dump($vars);
}

/**
 * 存储待打印的变量
 * @return multitype:
 */
function todump() {
    static $vars = [];
    $action = func_get_arg(0);
    if ($action === '__dump') {
        return $vars;
    } elseif ($action === '__clean') {
       return $vars = [];
    }
    $vars = array_merge($vars, func_get_args());
}

/**
 * 配置的读取与设置
 * @return Ambigous <boolean, NULL, multitype:>|boolean
 */
function config() {
    $args = func_get_args();
    $counts = count($args);
    switch ($counts) {
        case 0:
            return Config::get();
        case 1:
            return Config::get($args[0]);
        case 2:
            return Config::set($args[0], $args[1]);
        default:
            return false;
    }
}

/**
 * 获取客户端IP
 * @return Ambigous <string, unknown>
 */
function getClientIp() {
    return Request::getInstance()->getClientIp();
}

/**
 * 快速获取注入的结果
 * @param string $key
 * @return \framework\App|NULL
 */
function app($key = null) {
    if (is_null($key)) {
        return App::getInstance();
    } elseif (is_string($key) && !empty($key)) {
        $app = App::getInstance();
        return $app[$key];
    }
    return null;
}