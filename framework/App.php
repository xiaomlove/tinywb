<?php
namespace framework;

use framework\traits\ArrayAccess;
use framework\traits\FinalSingleton;
use framework\exceptions\AppException;

final class App implements \ArrayAccess
{
    use ArrayAccess, FinalSingleton;
    
    private static function initExceptionHandler()
    {
        if (defined('APP_DEBUG') && APP_DEBUG === true ) {
            set_exception_handler(array(AppException::class, 'default_exception_handler'));
            register_shutdown_function(array(AppException::class, 'error_exception_handler'));
        }
    }
    
	public static function run(array $config)
	{
        
	}

}