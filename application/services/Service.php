<?php
namespace services;

abstract class Service
{
    /**
     * 调用服务成功
     * @param mixed $data
     * @return array
     */
    public static function success($data)
    {
        return [null, $data];        
    }
    
    /**
     * 调用服务失败
     * @param string $msg
     * @param mixed $data
     * @return array
     */
    public static function fail($msg, $data = '')
    {
        $obj = new \stdClass();
        $obj->msg = $msg;
        return [$obj, $data];
    }
}