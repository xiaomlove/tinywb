<?php
/**
* @desc 测试控制器
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年7月27日    上午1:43:51
*/

namespace controllers;

use framework\Controller;

class Test extends Controller
{
    public function index()
    {
        $sphinx = app('sphinx');
        $sphinx->setServer('120.24.175.25', 9312);
        $sphinx->query('this', 'test1');
        
        dump($sphinx);
    }
}