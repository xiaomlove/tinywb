<?php
namespace providers;

use framework\Providers\Provider;

class AsyncTaskProvider extends Provider
{
    //为使用方便，将 configs/async-functions.php 中的函数也在这里定义一遍。
    const TASK_INCREASE_TOPIC_PV = 'cli\\Stat@increaseTopicPv';
    const TASK_UPDATE_TAG_TOPIC_COUNTS= 'cli\\Tag@updateTopicCounts';
    const TASK_INCREASE_TAG_HEAT_BY_VIEW_TOPIC = 'cli\\Stat@increaseTagHeatByViewTopic';
    const TASK_INCREASE_TAG_HEAT_BY_VIEW_TAG = 'cli\\Stat@increaseTagHeatByViewTag';
    
    private static $gearmanClient = null;
    
    private static $functions = null;
    
    public function register()
    {
        $self = $this;
        $this->app['asyncTask'] = function() use ($self) {
            return $self;
        };
    }
    
    private static function getFunctions()
    {
        if (self::$functions !== null)
        {
            return self::$functions;
        }
        $functionsFile = APP_PATH . '/configs/asynctask-functions.php';
        if (!is_file($functionsFile))
        {
            throw new \RuntimeException("not file: $functionsFile \n");
        }
        $result = require $functionsFile;
        return self::$functions = (array)$result;
    }
    
    private static function isFunctionExists($funcName)
    {
        $functionsArr = self::getFunctions();
        return in_array($funcName, $functionsArr);
    }
    
    private function getGearmanClient()
    {
        if (self::$gearmanClient !== null)
        {
            return self::$gearmanClient;
        }
        return self::$gearmanClient = app('gearmanClient');
    }
    
    public function addTask($funcName, array $data, $priority = 'normal')
    {
        if (!self::isFunctionExists($funcName))
        {
            throw new \InvalidArgumentException("Invalid param funcName: $funcName");
        }
        if (ENV !== 'release')
        {
            return sprintf('当前环境为: %s, 不能添加异步任务', ENV);
        }
        $client = $this->getGearmanClient();
        switch ($priority)
        {
            case 'normal':
                $jobHandle = $client->doBackground($funcName, json_encode($data));
                break;
            case 'high':
                $jobHandle = $client->doHighBackground($funcName, json_encode($data));
                break;
            case 'low':
                $jobHandle = $client->doLowBackground($funcName, json_encode($data));
                break;
            default:
                throw new \InvalidArgumentException("Invalid param priority: $priority");
        }
        $returnCode = $client->returnCode();
        if ($returnCode !== GEARMAN_SUCCESS)
        {
            return $client->error() . PHP_EOL;
        }
        else
        {
            return __CLASS__ . '---' . __FUNCTION__ . " run with $funcName success, jobHandle: $jobHandle" . PHP_EOL;
        }
    }
}