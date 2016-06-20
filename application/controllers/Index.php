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
//         dump($_SESSION);
        
        $res = "controller is: " . __CLASS__;
        $res .= " and action is: " . __FUNCTION__;
        
        $model2 = Juan::model();
        $model2->getDb();
        $model3 = Test::model();
        $model3->getDb();
//         $model2->insert('juan', [
//             ['type' => 'A', 'value' => 1],
//             ['type' => 'B', 'value' => 2],
//         ]);
        
        dump($model3->selectOne());
        return $this->display('index/index.php', ['info' => $res]);
    }
}