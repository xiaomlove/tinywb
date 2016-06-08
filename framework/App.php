<?php
namespace framework;

use framework\traits\Singleton;
use framework\exceptions\AppExceptionHandler;
use framework\providers\Provider;

final class App extends Container
{
    use Singleton;
    
    private $hadRun = false;
    
    public function regiserProvider($providerClassName)
    {
        foreach ((array)$providerClassName as $className) {
            if (!class_exists($className)) {
                throw new \RuntimeException("class: $className is not exists.");
            }
            $providerObj = new $className($this);
            if (!$providerObj instanceof Provider) {
                throw new \Exception("class: $className is not an instanceof framework\providers\Provider");
            }
            $providerObj->register();
        }
    }
    
	public function run(array $config)
	{
	    if ($this->hadRun) {
	        return false;
	    }
	    $this->hadRun = true;
	    
        Config::init($config);
            
        AppExceptionHandler::register();
        
        $defaultProviders = Config::get('default_providers');
        if (!empty($defaultProviders)) {
            $this->regiserProvider($defaultProviders);
        }
        
        dump(app('autoload'));
        
	}
	

}