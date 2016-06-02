<?php
/**
* @desc 实现数组式访问对象
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年6月2日    下午11:39:44
*/

namespace framework\traits;

trait ArrayAccess
{
    private  $containers = [];
    
    public function offsetExists($offset)
    {
        return isset($this->containers[$offset]);
    }
    
    public function offsetUnset($offset)
    {
        unset($this->containers[$offset]);
    }
    
    public function offsetSet($offset, $value)
    {
        if (is_null($offset))
        {
            $this->containers[] = $value;
        }
        else
        {
            $this->containers[$offset] = $value;
        }
    }
    
    public function offsetGet($offset)
    {
        return isset($this->containers) ? $this->containers[$offset] : null;
    }
}