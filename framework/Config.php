<?php
/**
* @desc 配置读取与设置类,纯静态
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年6月3日    上午12:52:29
*/

namespace framework;

use framework\traits\Singleton;

class Config
{
    use Singleton;
    
    private static $isInitialized = false;//是否初始化
    
    private static $configs = [];
    
    public static function init(array $config)
    {
        if (self::$isInitialized) {
            return false;
        }
        $conventionsConfig = require(FRAMEWORK_PATH . '/Conventions.php');
        $config = array_merge($conventionsConfig, $config);
        self::$configs = $config;
        self::$isInitialized = true;
        return true;
    }
    
    public static function set($key, $value)
    {
        if (empty($key) || !is_string($key)) {
            throw new \InvalidArgumentException("parameter key must be a not empty string");
        }
        $key = trim($key, '.');
        $key = 'self::$configs[\'' . str_replace('.', '\'][\'', $key) . '\'] = $value;';
        $result = eval($key);
        return $result === null ? true : false;
    }
    
    public static function get($key = null)
    {
        if (is_null($key)) {
            return self::$configs;
        } elseif (empty($key) || !is_string($key)) {
            throw new \InvalidArgumentException("parameter key must be a not empty string");
            return false;
        } else {
            $result = null;
            $key = trim($key, '.');
            $valueStr = 'self::$configs[\'' . str_replace('.', '\'][\'', $key) . '\']';
            $getValueStr = '$result = isset('. $valueStr . ') ? ' . $valueStr . ' : null;';
            $evalRes = eval($getValueStr);
            if ($evalRes !== null) {
                return false;
            }
            return $result;
        }
    }
    
}