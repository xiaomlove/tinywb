<?php
namespace controllers;

use framework\Controller;
use services\TopicService;
use framework\utils\Pagination2;
use framework\utils\Pagination;

class Index extends Controller
{
    public $layout = 'main.php';
    
    public function index()
    {
        $app = app();
        $event = $app['event'];
        $event->on('AFTER_CONTROLLER', function() {
            echo 'sss';
        });
        $page = $this->request->getParam('page');
        $page = empty($page) ? 1 : intval($page);
        $total = 31420000;
        $per = 10;
        $totalPage = ceil($total/$per);
        
        $pagination = Pagination2::create();
        
        dump($pagination);
        
        $list = TopicService::getHomeArticles($page, $per, 'publish_time', 'DESC');
        $newest = TopicService::getNewest(5);
        
        return $this->display('index/index.php', [
            'list' => $list, 
            'newest' => $newest,
        ]);
    }
}