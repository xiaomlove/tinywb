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
    
    public function swoole()
    {
        $swooleClient = app('swooleClient');
        print_r($swooleClient);
        if (!$swooleClient->isConnected())
        {
            die('swoole client is not connected');
        }
        $result = $swooleClient->send(json_encode([
            'class' => __CLASS__,
            'method' => __METHOD__,
            'time' => $_SERVER['REQUEST_TIME'],
            'data' => 'send a test task',
        ]));
        var_dump($result);
        if ($result)
        {
            die('添加异步任务成功');
        }
        else 
        {
            die('添加异步任务失败');
        }
        $swooleClient->close();
    }
}