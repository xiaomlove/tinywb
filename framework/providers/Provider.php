<?php
/**
* @desc 服务提供者基类，继承此类的都可以通过全局对象$app['xxx']得到注入的结果。
*       子类实现register方法，将依赖注入到全局对象$app
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年6月8日    上午12:01:51
*/

namespace framework\providers;

abstract class Provider
{
    protected $app;//全局对象$app
    
    public function __construct($app)
    {
        $this->app = $app;
    }
    
    abstract function register();
}