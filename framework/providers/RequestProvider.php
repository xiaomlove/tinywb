<?php
/**
* @desc 注入请求对象
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年6月8日    上午12:21:37
*/

namespace framework\providers;

use framework\Request;

class RequestProvider extends Provider
{
    public function register()
    {
        $this->app['request'] = function() {
            return Request::getInstance();  
        };
    }
}