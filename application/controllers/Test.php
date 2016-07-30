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
//         $sphinx->setMatchMode(SPH_MATCH_PHRASE);
//         $sphinx->setArrayResult(true);
        $sphinx->setSortMode(SPH_SORT_EXTENDED, 'update_time DESC');
        $sphinx->setLimits(0, 2, 50000000);
        
        $result = $sphinx->query('好', 'test1');
        todump($sphinx->getLastError());
        dump($result);
    }
}