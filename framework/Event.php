<?php
/**
* @desc 事件
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年7月8日    上午12:29:45
*/

namespace framework;

use framework\traits\Singleton;

class Event
{
    use Singleton;
    
    private static $events = [];
    
    /**
     * 创建事件
     * @param string|array $name
     * @throws \InvalidArgumentException
     * @return boolean
     */
    public function create($name)
    {
        $nameArr = (array)$name;
        foreach ($nameArr as $value)
        {
            if (empty($value) || !is_string($value))
            {
                throw new \InvalidArgumentException("Invalid event name, it should be a not empty string.");
            }
            if (!isset(self::$events[$value]))
            {
                self::$events[$value] = [];
            }
        }
        return true;
    }
    
    /**
     * 为一个事件添加绑定
     * @param string $name 事件名
     * @param mixed $handler 绑定
     * @param int $priority 优先级
     * @param mixed $data 附加数据
     * @throws \InvalidArgumentException
     * @return boolean
     */
    public function on($name, $handler, $priority = 100, $data = null)
    {
        if (empty($name) || !is_string($name))
        {
            throw new \InvalidArgumentException("Invalid parameter: name, it should be a not empty string.");
        }
        if (!ctype_digit(strval($priority)))
        {
            throw new \InvalidArgumentException("Invalid parameter: priority, it should be a number.");
        }
        if (!isset(self::$events[$name]))
        {
            throw new \OutOfBoundsException("not create event: $name yet.");
        }
        $idx = $this->build_unique_idx($handler);
        if (empty($idx))
        {
            throw new \InvalidArgumentException("Invalid parameter: handler.");
        }
        self::$events[$name][(int)$priority][$idx] = array('handler' => $handler, 'data' => $data);
        return true;
    }
    
    /**
     * 解除绑定
     * @param string $name
     * @param mixed $handler
     * @throws \InvalidArgumentException
     * @return boolean
     */
    public function off($name, $handler = null)
    {
        if (empty($name) || !is_string($name))
        {
            throw new \InvalidArgumentException("Invalid parameter: name, it should be a not empty string.");
        }
        if (is_null($handler))
        {
            unset(self::$events[$name]);
            return true;
        }
        $idx = $this->build_unique_idx($handler);
        if (empty($idx))
        {
            throw new \InvalidArgumentException("Invalid parameter: handler.");
        }
        foreach (self::$events[$name] as $priority => $item)
        {
            if (isset($item[$idx]))
            {
                unset(self::$events[$name][$priority][$idx]);
            }
        }
        return true;
    }
    
    /**
     * 触发事件
     * @param string $name
     * @return boolean
     */
    public function trigger($name)
    {
        if (empty(self::$events[$name]))
        {
            return true;
        }
        krsort(self::$events[$name]);
        reset(self::$events[$name]);
        do 
        {
            foreach (current(self::$events[$name]) as $bind)
            {
                $result = call_user_func_array($bind['handler'], (array)$bind['data']);
                if ($result === false)
                {
                    break 2;
                }
            }
        }
        while (next(self::$events[$name]) !== false);
        return true;
    }
    
    /**
     * 获得事件信息
     * @param string $name
     * @return multitype:|NULL
     */
    public function getEvent($name = null)
    {
        if (is_null($name))
        {
            return self::$events;
        }
        if (isset(self::$events[$name]))
        {
            return self::$events[$name];
        }
        return null;
    }
    
    /**
     * @desc 创建回调的唯一标识
     * @see http://www.xiaomlove.com/2015/11/01/wordpress%E7%9A%84%E6%A0%B8%E5%BF%83-action%E4%B8%8Efilter/
     * @param unknown $handler
     * @return unknown|string|boolean
     */
    private function build_unique_idx($handler)
    {
        if (is_string($handler))
        {
            return $handler;
        }
        elseif (is_object($handler) || ($handler instanceof \Closure))
        {
            return spl_object_hash($handler);
        }
        elseif (is_array($handler))
        {
            if (is_object($handler[0]))
            {
                return spl_object_hash($handler[0]) . $handler[1];
            }
            elseif (is_string($handler[0]))
            {
                return $handler[0] . '::' . $handler[1];
            }
        }
        return false;
    }
}