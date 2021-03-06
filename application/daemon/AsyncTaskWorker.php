<?php
/**
* @desc 异步任务。执行特定的CLI模式方法
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年8月14日    下午2:30:47
*/
namespace daemon;

define('APP_PATH', dirname(__DIR__));

class AsyncTaskWorker
{
    private static $worker = null;
    
    public function __construct()
    {
        $worker = new \GearmanWorker();
        $worker->addServer('127.0.0.1', 4730);
        self::$worker = $worker;
        $this->addFunctions();
    }
    
    public function getWorker()
    {
        return self::$worker;
    }
    
    private static function getFunctions()
    {
        $functionsFile = APP_PATH . '/configs/asynctask-functions.php';
        if (!is_file($functionsFile))
        {
            echo "not file $functionsFile \n";
            return null;
        }
        return require $functionsFile;
    }
    
    private function addFunctions()
    {
        $functions = self::getFunctions();
        if (empty($functions) || !is_array($functions))
        {
            echo "no functions \n";
            return;
        }
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
        if (mb_substr_count($funcName, '@') === 1)
        {
            //类@方法
            $classMethodArr = explode('@', $funcName);
            $className = $classMethodArr[0];
            $methodName = $classMethodArr[1];
            $funcName = str_replace('\\', '\\\\', $funcName);//执行脚本时会被干掉一层，导致命名空间丢失
            $command = 'sudo -u root php ' . APP_PATH . "/cli/main.php $funcName " . implode(' ', $data);
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
while ($worker->getWorker()->work());