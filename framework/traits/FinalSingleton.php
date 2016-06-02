<?php
/**
* @desc 单例模式，且为最终类。
*       如果该为为父类，单例了其子类无法实例化，情况很少
*       如果该类为子类，更不能私有化其父类的构造函数。故只用于最终类的单例化
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年6月3日    上午12:03:55
*/

namespace framework\traits;

trait FinalSingleton
{
    private static $instance;
    
    private function __construct()
    {
        
    }
    
    private function __clone()
    {
        
    }
    
    public static function getInstance()
	{
		if (self::$instance !== null)
		{
			return self::$instance;
		}
		self::$instance = new self;
		return self::$instance;
	}
    
}