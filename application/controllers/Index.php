<?php
namespace controllers;

use framework\Controller;
use services\TopicService;
use services\StatService;
use services\TagService;

class Index extends Controller
{
    public $layout = 'main.php';
    
    public function index()
    {
        $page = $this->request->getParam('page');
        $page = empty($page) ? 1 : intval($page);
        $total = StatService::getBykey('topic_total');
        $total = empty($total) ? 0 : $total['meta_value'];
        $per = 10;
        
        $list = TopicService::getHomeArticles($page, $per, 'publish_time', 'DESC');
        $newest = TopicService::getNewest(5);
        
        return $this->display('index/index.php', [
            'list' => $list, 
            'newest' => $newest,
            'tagTotal' => TagService::getTotal(),
            'topicTotal' => $total,
            'pagination' => getPagination($total, $page),
        ]);
    }
    
    public function tag($tagName)
    {
        dump($tagName);
    }
}