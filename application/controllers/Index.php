<?php
namespace controllers;

use framework\Controller;

use framework\View;
use framework\Model;

class Index extends Controller
{
    public $layout = 'main.php';
    
    public function index()
    {
        $res = "controller is: " . __CLASS__;
        $res .= " and action is: " . __FUNCTION__;
//         echo 'ssss';
        $model = new Model;
        dump($model->getDb()->getAttribute());

        return $this->display('index/index.php', ['info' => $res]);
    }
}