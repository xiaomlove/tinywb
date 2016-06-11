<?php
namespace controllers;

use framework\Controller;

use framework\View;

class Index extends Controller
{
    public $layout = 'main.php';
    
    public function index()
    {
        $res = "controller is: " . __CLASS__;
        $res .= " and action is: " . __FUNCTION__;
//         echo 'ssss';
        return $this->display('index/index.php', ['info' => $res]);
    }
}