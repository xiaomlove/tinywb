<?php
namespace controllers;

use framework\Controller;

use models\Test;
use models\Juan;
use framework\Model;

class Index extends Controller
{
    public $layout = 'main.php';
    
    public function index()
    {
        $res = "controller is: " . __CLASS__;
        $res .= " and action is: " . __FUNCTION__;
        $model = new Test;
        $model->getDb();
        $model2 = new Juan;
        $model3 = new Juan;
        $model2->getDb();
//         $model->insert('juan', [
//             ['type' => 'A', 'value' => 1],
//             ['type' => 'B', 'value' => 2],
//         ]);
        
        dump($model->getModels(), $model2->getDbConnections());
        return $this->display('index/index.php', ['info' => $res]);
    }
}