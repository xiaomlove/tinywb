<?php
/**
* @desc 全局函数。PHP强大的函数和数组是其特色，不要说直接这么定义函数显得low!简单易用是王道!
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年6月4日    下午5:36:49
*/

use framework\Config;
use framework\Request;

/**
 * 打印变量
 * @param unknown $data
 * @param string $die
 */
function dump($data, $die = true) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    if ($die) {
        die;
    }
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
function getClientIp()
{
    return Request::getInstance()->getClientIp();
}