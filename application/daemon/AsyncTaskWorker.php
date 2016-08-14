<?php
/**
* @desc 异步任务。执行特定的CLI模式方法
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年8月14日    下午2:30:47
*/

class AsyncTaskWorker
{
    private static $worker = null;
    
    public function __construct()
    {
        $worker = new \GearmanWorker();
        $worker->addServer('127.0.0.1', 4370);
        self::$worker = $worker;
        $this->addFunctions();
    }
    
    private function addFunctions()
    {
        $functions = array_keys(self::$taskText);
        foreach ($functions as $func)
        {
            $addResult = self::$worker->addFunction($func, function(\GearmanJob $job, $context) {
                $funcName = $job->functionName();
                $data = $job->workload();
                $result = $context->runTask($funcName, json_decode($data, true));
                $timeString = date('Y-m-d H:i:s', time());
                echo "$timeString $funcName run with data: $data success, result is: $result \n";
            }, $this, 10);
            if ($addResult === false)
            {
                throw new \RuntimeException(__CLASS__ . '---' . __METHOD__ . " error \n");
            }
            else 
            {
                echo "add function $func success \n";
            }
        }
    }
    
    private function runTask($funcName, array $data)
    {
        if (empty($funcName))
        {
            return "error, empty funcName";
        }
        if (mb_substr_count($funcName, '@') !== 1)
        {
            //类@方法
            $classMethodArr = explode('@', $funcName);
            $className = $classMethodArr[0];
            $methodName = $classMethodArr[1];
            
            $command = dirname(__DIR__) . "/cli/main.php " . implode(' ', $data);
            passthru($command, $result);
            return $result;
        }
        else 
        {
            return "error, funcName: $funcName is invalid";
        }
    }
}


$worker = new AsyncTaskWorker();
while ($worker->work());