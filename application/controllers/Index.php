<?php
namespace controllers;

use framework\Controller;
use services\TopicService;
use services\TagService;

class Index extends Controller
{
    public $layout = 'main.php';
    
    public function index()
    {
        $page = $this->request->getParam('page');
        $page = empty($page) ? 1 : intval($page);
        $total = 31420000;
        $per = 10;
        
        $list = TopicService::getHomeArticles($page, $per, 'publish_time', 'DESC');
        $newest = TopicService::getNewest(5);
        
        return $this->display('index/index.php', [
            'list' => $list, 
            'newest' => $newest,
            'pagination' => getPagination($total, $page),
        ]);
    }
    
    public function tag($tagName)
    {
        dump($tagName);
    }
}