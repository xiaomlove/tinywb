<?php
namespace framework;

use framework\traits\Singleton;
use framework\exceptions\AppExceptionHandler;

final class App extends Container
{
    use Singleton;
    
	public static function run(array $config)
	{
        Config::init($config);
            
        AppExceptionHandler::register();
        
        
        
	}
	

}