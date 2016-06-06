<?php
/**
* @desc 单例模式。会将当前类的构造函数私有化。
*       如果当前类为父类，子类需要显式声明构造函数并将访问级别变弱（weaker）,也就是由private -> protected/public，才能实例化。
*       如果当前类为子类，其父类不能声明protected/public类的构造函数，要么不声明，要么声明privated。
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年6月3日    上午12:03:55
*/

namespace framework\traits;

trait Singleton
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