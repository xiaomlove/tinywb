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
        // $sql = "SELECT count(*) FROM test1 WHERE id>:id";
        // todump($model->fetchColumn($sql, array(':id' => 0)));
        //dump($model->fetch($sql, array(':1' => 1)));

        $field = [
            ['type' => 'A', 'value' => 123],
            ['type' => 'B', 'values' => '123'],
        ];
        //$r = $model->insert('juan', $field);
        $r = $model->delete('juan', ['id' => [0, '>']]);
        dump($r, $model->lastSql());
        return $this->display('index/index.php', ['info' => $res]);
    }
}