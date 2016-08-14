<?php
namespace providers;

use framework\Providers\Provider;

class AsyncTaskProvider extends Provider
{
    private static $gearmanClient = null;
    
    const TASK_INCREASE_TOPIC_PV = 'cli\\Stat@updateTopicPv';
    
    private static $taskText = [
        self::TASK_INCREASE_TOPIC_PV => '增加文章PV',
    ];
    
    public function register()
    {
        $self = $this;
        $this->app['asyncTask'] = function() use ($self) {
            return $self;
        };
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
        if (!isset(self::$taskText[$funcName]))
        {
            throw new \InvalidArgumentException("Invalid param funcName: $funcName");
        }
        switch ($priority)
        {
            case 'normal':
                $jobHandle = $this->getGearmanClient()->doBackground($funcName, json_encode($data));
                break;
            case 'high':
                $jobHandle = $this->getGearmanClient()->doHighBackground($funcName, json_encode($data));
                break;
            case 'low':
                $jobHandle = $this->getGearmanClient()->doLowBackground($funcName, json_encode($data));
                break;
            default:
                throw new \InvalidArgumentException("Invalid param priority: $priority");
        }
        return $jobHandle;
    }
}