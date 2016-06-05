<?php
namespace framework;

use framework\traits\ArrayAccess;
use framework\traits\FinalSingleton;
use framework\exceptions\AppExceptionHandler;

final class App implements \ArrayAccess
{
    use ArrayAccess, FinalSingleton;
    
	public static function run(array $config)
	{
        Config::init($config);
        
        AppExceptionHandler::register();
        
        $request = Request::getInstance();
        
        echo $request->method = 9;
	}
	

}