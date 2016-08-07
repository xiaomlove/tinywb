<?php
/**
* @desc url无法创建异常
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年8月7日    下午6:35:28
*/

namespace framework\exceptions;

use framework\View;
use framework\Response;

class UrlCanNotCreateException extends \Exception
{
    private $maps = [];//路由映射，含有不匹配原因
    
    private $controllerAction;
    
    private $params;
    
    protected  $message;
    
    public function __construct($message, $controllerAction, array $params = [], array $maps = [])
    {
        $this->message = $message;
        $this->controllerAction = $controllerAction;
        $this->params = $params;
        $this->maps = $maps;
    }
    
    
    public function output()
    {
        $html = View::getInstance()->render(__DIR__ . '/tpl_url_can_not_create.php', [
            'message' => $this->message,
            'params' => $this->params,
            'controllerAction' => $this->controllerAction,
            'maps' => $this->maps,
            'errfile' => $this->getFile(),
            'errline' => $this->getLine(),
            'errcode' => $this->getCode(),
            'errtype' => __CLASS__,
            'trace' => str_replace("\n", "<br/>", $this->getTraceAsString()),
        ]);
        $response = new Response($html);
        $response->send();
    }
    
}