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
        $sql = "SELECT * FROM juan WHERE 1=:1";
        $sql = "SELECT count(*) FROM juan WHERE id>:id";
        todump($model->fetchColumn($sql, array(':id' => 0)));
        dump($model->lastSql());
        return $this->display('index/index.php', ['info' => $res]);
    }
}