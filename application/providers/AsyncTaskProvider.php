<?php
namespace providers;

use framework\Providers\Provider;

class AsyncTaskProvider extends Provider
{
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