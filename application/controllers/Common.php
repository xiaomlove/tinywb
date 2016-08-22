<?php
namespace controllers;

use framework\Controller;

class Common extends Controller
{
    public function __construct()
    {
        parent::__construct();
        
        
    }
    
    public function showInvalidParam()
    {
        return $this->display('common/invalid-param.php');
    }
    
    public function show404NotFound()
    {
        return $this->display('common/404-not-found.php');
    }
    
    public function runCli($controllerAction, $data)
    {
        $command = 'sudo -u root php ' . APP_PATH . "/cli/main.php $controllerAction " . implode(' ', $data);
        passthru($command, $result);
        return $result;
    }
    
}