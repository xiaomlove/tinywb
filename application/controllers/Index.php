<?php
namespace controllers;

use framework\Controller;
use services\TopicService;

class Index extends Controller
{
    public $layout = 'main.php';
    
    public function index()
    {
        $list = TopicService::getHomeArticles(1, 10, 'publish_time', 'DESC');
        $newest = TopicService::getNewest(5);
        
        return $this->display('index/index.php', ['list' => $list, 'newest' => $newest]);
    }
}