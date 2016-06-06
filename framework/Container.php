<?php
/**
* @desc 容器类，是抽象类，不能直接使用
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年6月6日    下午11:55:36
*/

namespace framework;

abstract class Container
{
    private static $dependencies = [];
    
    /**
     * 通过闭包注入，简单限制必须是必包，这个最强，简单不搞多种方式了，那样子麻烦效果又是一样的。
     * @param unknown $key
     * @param \Closure $closure
     * @throws \InvalidArgumentException
     * @throws \Exception
     * @return boolean
     */
    public function set($key, \Closure $closure)
    {
        if (empty($key) || !is_string($key)) {
            throw new \InvalidArgumentException("Invalid key, it must be a not empty string.");
        }
        if (isset(self::$dependencies[$key])) {
            throw new \Exception("key: $key had already set.");
        }
        self::$dependencies[$key] = $closure;
        return true;
    }
    
    public function get($key)
    {
        if (empty($key) || !is_string($key)) {
            throw new \InvalidArgumentException("Invalid key, it must be a not empty string.");
        }
        if (!isset(self::$dependencies[$key])) {
            return null;
        }
        
        
    }
    
}