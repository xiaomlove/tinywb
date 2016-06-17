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
        $sql = "SELECT * FROM test1 WHERE 1=:1";
        // $sql = "SELECT count(*) FROM test1 WHERE id>:id";
        // todump($model->fetchColumn($sql, array(':id' => 0)));
        //dump($model->fetch($sql, array(':1' => 1)));

        $field = [
            ['name' => '小a', 'title' => 'title a', 'sex' => '男'],
            ['name' => '小b', 'title' => 'title b', 'sex' => '女'],
        ];
        $r = $model->insert('test1', $field);
        dump($r);
        return $this->display('index/index.php', ['info' => $res]);
    }
}