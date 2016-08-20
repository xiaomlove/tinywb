<?php
/**
* @desc 测试控制器
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年7月27日    上午1:43:51
*/

namespace controllers;

use framework\Controller;
use providers\AsyncTaskProvider;
use services\TopicService;
use cli\Tag as cliTag;

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
        $start = microtime(true);
        $swooleClient = app('swooleClient');
        print_r($swooleClient);
        if (!$swooleClient->isConnected())
        {
            die('swoole client is not connected');
        }
        for ($i = 0; $i < 10;$i++)
        {
            $result = $swooleClient->send(json_encode([
                'time' => $_SERVER['REQUEST_TIME'],
                'data' => 'send a test task---' . $i,
            ]));
        }
        if ($result)
        {
            echo '添加10个异步任务成功';
        }
        else
        {
            echo '添加异步任务失败';
        }
        echo "<hr/>时间：" . (microtime(true) - $start);
        $swooleClient->close();
        die("<br/>结束");
    }
    
    
    public function gearman()
    {
        $start = microtime(true);
        $client = app('gearmanClient');
        print_r($client);
        echo '<br/>';
        for ($i = 0; $i < 10; $i++)
        {
            $client->doBackground('test', json_encode([
                'time' => $_SERVER['REQUEST_TIME'],
                'data' => 'send a test task',
            ]));
            $resultCode = $client->returnCode();
            if ($resultCode != GEARMAN_SUCCESS)
            {
                echo "failed at $i, code: $resultCode <br/>";
            }
            else
            {
                echo "success at $i, code: $resultCode </br>";
            }
        }
        die("done, cost time: " . (microtime(true) - $start));
    }
    
    public function url()
    {
        $url = url('controllers\User@profile', [ 'age' => 23,'id' => '34'], 'comment');
        dump($url);
    }
    
    public function topicpv()
    {
        $r = cliTag::getInstance()->updateTopicCounts(1);
        var_dump($r);
    }
}