<?php
namespace controllers;

use framework\Controller;

use models\Topic;

class Index extends Controller
{
    public $layout = 'main.php';
    
    public function index()
    {
        $topicModel = Topic::model();
        $list = $topicModel->getList('*', );
        return $this->display('index/index.php');
    }
}