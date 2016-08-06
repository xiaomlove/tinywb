<?php
  
class SwooleServer  
{  
    private $serv;  
  
    public function __construct()  
    {  
        $this->serv = new \Swoole\Server("0.0.0.0", 9501);
        $this->serv->set(array(  
            'worker_num' => 1, //一般设置为服务器CPU数的1-4倍  
            'daemonize' => 0, //以守护进程执行  
            'max_request' => 10000,  
            'dispatch_mode' => 2,  
            'task_worker_num' => 8, //task进程的数量  
            "task_ipc_mode " => 3, //使用消息队列通信，并设置为争抢模式  
            "log_file" => "/tmp/SwooleServer.log" ,//日志  
        ));  
        $this->serv->on('Receive', array($this, 'onReceive'));  
        // bind callback  
        $this->serv->on('Task', array($this, 'onTask'));  
        $this->serv->on('Finish', array($this, 'onFinish'));  
        $this->serv->start();  
    }  
  
    public function onReceive(\Swoole\Server $serv, $fd, $from_id, $data)  
    {  
        echo __FUNCTION__ . PHP_EOL;
        echo "Get Message From Client {$fd}:{$data}\n";  
        echo "from_id: $from_id \n";
        //send a task to task worker.  
        $serv->task($data);  
    }  
  
    public function onTask(\Swoole\Server $serv, $task_id, $from_id, $data)  
    {  
        echo __FUNCTION__ . PHP_EOL;
        echo "task_id: $task_id, from_id: $from_id, data: \n";
        print_r($data);
    }  
  
    public function onFinish(\Swoole\Server $serv, $task_id, $data)  
    {  
        echo __FUNCTION__ . PHP_EOL;
        echo "task_id: {$task_id} finish\n";  
        echo "Result: {$data}\n";  
    }  
  
    protected function httpGet($url, $data)  
    {  
        if ($data) {  
            $url .= '?' . http_build_query($data);  
        }  
        $curlObj = curl_init(); //初始化curl，  
        curl_setopt($curlObj, CURLOPT_URL, $url); //设置网址  
        curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1); //将curl_exec的结果返回  
        curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, FALSE);  
        curl_setopt($curlObj, CURLOPT_SSL_VERIFYHOST, FALSE);  
        curl_setopt($curlObj, CURLOPT_HEADER, 0); //是否输出返回头信息  
        $response = curl_exec($curlObj); //执行  
        curl_close($curlObj); //关闭会话  
        return $response;  
    }  
  
}  
  
$server = new SwooleServer();  