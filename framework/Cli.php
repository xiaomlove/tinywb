<?php
/**
* @desc CLI模式下运行的类接口。
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年8月17日    上午2:11:04
*/

namespace framework;

interface  Cli
{
    //提供一个获取自身对象的静态方法
    public static function getInstance();
}