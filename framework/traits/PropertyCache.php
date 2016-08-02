<?php
namespace framework\traits;

trait PropertyCache
{
    private static $propertyCacheData = [];
    
    protected static function buildParamKey($params)
    {
        $key = '';
        if (is_array($params))
        {
            foreach ($params as $k => $v)
            {
                $key .= sprintf("%s_%s_", strval($k), self::buildParamKey($v));
            }
        }
        else
        {
            $key .= self::convertParamToString($params);
        }
        return $key;
    }
    
    protected static function convertParamToString($param)
    {
        if (is_array($param))
        {
            throw new \InvalidArgumentException("Invalid param: " . print_r($param));
        }
        $key = '';
        if ($param === true)
        {
            $key = 'true';
        }
        elseif ($param === false)
        {
            $key = 'false';
        }
        elseif (is_null($param))
        {
            $key = 'null';
        }
        elseif (is_scalar($param))
        {
            $key = strval($param);
        }
        elseif ($param instanceof \Closure)
        {
            throw new \InvalidArgumentException("Invalid param: " . print_r($param));
        }
        elseif (is_object($param))
        {
            $key = spl_object_hash($param);
        }
        else
        {
            throw new \InvalidArgumentException("Invalid param: " . print_r($param));
        }
        return $key;
    }
    
    public static function setPropertyCache($key, $data)
    {
        self::$propertyCacheData[$key] = $data;
    }
    
    public static function hasPropertyCache($key)
    {
        return isset(self::$propertyCacheData[$key]);
    }
    
    public static function getPropertyCache($key)
    {
        return self::$propertyCacheData[$key];
    }
}