<?php
/**
* @desc 容器类，是抽象类，不能直接使用
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年6月6日    下午11:55:36
*/

namespace framework;

use framework\traits\ArrayAccess;

abstract class Container implements \ArrayAccess
{
    use ArrayAccess;
    
    /**
     * 通过闭包注入，简单限制必须是闭包，这个最强，简单不搞多种方式了，那样子麻烦效果又是一样的。
     * @param unknown $key
     * @param \Closure $closure
     * @throws \InvalidArgumentException
     * @throws \Exception
     * @return boolean
     */
    public function offsetSet($key, $value)
    {
        if (empty($key) || !is_string($key)) {
            throw new \InvalidArgumentException("Invalid key, it must be a not empty string.");
        }
        if (isset($this->containers[$key])) {
            throw new \Exception("key: $key had already set.");
        }
        if (!$value instanceof \Closure) {
            throw new \InvalidArgumentException("Invalid value, it must be a closure.");
        }
        $this->containers[$key] = $value;
        return true;
    }
    
    public function offsetGet($key)
    {
        if (empty($key) || !is_string($key)) {
            throw new \InvalidArgumentException("Invalid key, it must be a not empty string.");
        }
        if (!isset($this->containers[$key])) {
            return null;
        }
        
        $reflection = new \ReflectionFunction($this->containers[$key]);
        return $reflection->invoke();
    }
    
}