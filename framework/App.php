<?php
namespace framework;

use framework\traits\Singleton;
use framework\exceptions\AppExceptionHandler;
use framework\providers\Provider;

final class App extends Container
{
    use Singleton;
    
    private $hadRun = false;
    
    /**
     * 注册提供者
     * @throws \RuntimeException
     * @throws \Exception
     */
    private function regiserProvider()
    {
        $defaultProviders = Config::get('default_providers');
        $providers = Config::get('providers');
        if (is_array($providers) && !empty($providers)) {
            $defaultProviders = array_merge($defaultProviders, $providers);
        }
        foreach ($defaultProviders as $className) {
            if (!class_exists($className)) {
                throw new \RuntimeException("class: $className is not exists.");
            }
            $providerObj = new $className($this);
            if (!$providerObj instanceof Provider) {
                throw new \Exception("class: $className is not an instanceof framework\providers\Provider");
            }
            $providerObj->register();
        }
        return true;
    }
    
    /**
     * 注册命名空间
     */
    private function registerNamespace()
    {
        $defauleNamespaces = Config::get('default_namespaces');
        $namespaces = Config::get('namespaces');
        if (is_array($namespaces) && (!empty($namespaces))) {
            $defauleNamespaces = array_merge($defauleNamespaces, $namespaces);
        }
        $loader = Autoload::getInstance();
        foreach ($defauleNamespaces as $name => $path) {
            $loader->addNamespace($name, $path);
        }
        return true;
    }
    
    private function registerRoute()
    {
        if (!file_exists(APP_PATH . '/route.php')) {
            throw new \RuntimeException("Every application should have a 'route.php' file under the application root path.");
        }
        require APP_PATH . '/route.php';
        return true;
    }
    
    
    /*******************************************************************/
    
    public function getProviders()
    {
        return $this->containers;
    }
    
    
	public function run(array $config)
	{
	    if ($this->hadRun) {
	        return false;
	    }
	    $this->hadRun = true;
	    
        Config::init($config);
            
        AppExceptionHandler::register();
        
        $this->regiserProvider();

        $this->registerNamespace();
        
        $this->registerRoute();
        
        $request = app('request');
        $route = app('route');
        $routeMaps = $route->getRouteMaps();
        
        $url = $request->getFullUrl();
        
        //todump($routeMaps);
        
        $route->resolveUrl($url);
	}
	

}