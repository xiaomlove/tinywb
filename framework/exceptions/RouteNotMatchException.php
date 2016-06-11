<?php
/**
* @desc 无法匹配路由异常
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年6月10日    上午3:37:59
*/

namespace framework\exceptions;

use framework\View;
use framework\Response;

class RouteNotMatchException extends \Exception
{
    private $maps = [];//路由映射，含有不匹配原因
    
    private $fullUrl;//完整URL
    
    private $toMatchPath;//需要匹配的path部分
    
    protected  $message;
    
    public function __construct($message, $fullUrl, $toMatchPath = '', $maps = [])
    {
        $this->message = $message;
        $this->fullUrl = $fullUrl;
        $this->toMatchPath = $toMatchPath;
        $this->maps = $maps;
    }
    
    
    public function output()
    {
        $statusCode = APP_DEBUG ? 200 : 404;
        $html = View::getInstance()->render(__DIR__ . '/tpl_route_not_match.php', [
            'message' => $this->message,
            'fullUrl' => $this->fullUrl,
            'toMatchPath' => $this->toMatchPath,
            'maps' => $this->maps,
            'statusCode' => $statusCode,
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'trace' => str_replace("\n", "<br/>", $this->getTraceAsString()),
        ]);
        $response = new Response($html, $statusCode);
        $response->send();
    }
    
}