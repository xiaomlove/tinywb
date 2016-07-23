<?php
namespace framework;

use framework\traits\Singleton;
use framework\traits\ReadonlyProperties;
use framework\exceptions\AppExceptionHandler;
use framework\exceptions\RouteNotMatchException;
use framework\providers\Provider;

final class App extends Container
{
    use Singleton, ReadonlyProperties;
    
    private $hadRun = false;
    
    private $controller;
    
    private $action;
    
    private $isDebug;
    
    /**
     * 注册提供者
     * @throws \RuntimeException
     * @throws \Exception
     */
    private function regiserProvider()
    {
        $defaultProviders = Config::get('default_providers');
        $providers = Config::get('providers');
        if (is_array($providers) && !empty($providers)) 
        {
            $defaultProviders = array_merge($defaultProviders, $providers);
        }
        foreach ($defaultProviders as $className) 
        {
            if (!class_exists($className)) 
            {
                throw new \RuntimeException("class: $className is not exists.");
            }
            $providerObj = new $className($this);
            if (!$providerObj instanceof Provider) 
            {
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
        $defaultNamespaces = Config::get('default_namespaces');
        $namespaces = Config::get('namespaces');
        if (is_array($namespaces) && (!empty($namespaces))) 
        {
            $defaultNamespaces = array_merge($defaultNamespaces, $namespaces);
        }
        $loader = Autoload::getInstance();
        foreach ($defaultNamespaces as $name => $path) 
        {
            $loader->addNamespace($name, $path);
        }
        return true;
    }
    
    private function registerRoute()
    {
        if (!file_exists(APP_PATH . '/route.php')) 
        {
            throw new \RuntimeException("Every application should have a 'route.php' file under the application root path.");
        }
        require APP_PATH . '/route.php';
        return true;
    }
    
    private function registerEvent()
    {
        $defaulEvents = Config::get('default_events');
        $events = Config::get('events');
        if (is_array($events) && (!empty($events))) 
        {
            $defaulEvents = array_merge($defaulEvents, $events);
        }
        $eventObj = Event::getInstance();
        $eventObj->create($defaulEvents);
        return true;
    }
    
    private function runController($matchedRoute)
    {
        $controller = $this->controller;
        $action = $this->action;
        $parameters = $this->getActionParameters($controller, $action);
        foreach ($parameters as $name => &$value) 
        {
            if (isset($matchedRoute['params'][$name])) 
            {
                $value = $matchedRoute['params'][$name];
            }
        }
        $controllerObj = new $controller;
        
        //在action执行之前，做些什么？
        $event = Event::getInstance();
        $event->trigger('before:action');
        $controllerResult = call_user_func_array([$controllerObj, $action], $parameters);
        
        //运行远action之后，做些什么？
        $event->trigger('after:action');
        return $controllerResult;
    }
    
    
    /*******************************************************************/
    
    public function getProviders()
    {
        return $this->containers;
    }
    
	public function init(array $config)
	{
	    if ($this->hadRun) 
	    {
	        return false;
	    }
	    $this->hadRun = true;
	    
	    $this->readonlyProperties = ['controller', 'action', 'isDebug'];
	    
        Config::init($config);
        
        $this->isDebug = (bool)Config::get('app_debug');
        
        AppExceptionHandler::register();
        
        $this->regiserProvider();

        $this->registerNamespace();
        
        $this->registerRoute();
        
        $this->registerEvent();
        
        return $this;
	}
	
	public function handle()
	{
	    if (!$this->hadRun)
	    {
	        return false;
	    }
	    $event = Event::getInstance();
	    $event->trigger('app:start');
	    
	    $request = Request::getInstance();
	    $route = Route::getInstance();
	    $event->trigger('before:route');
	    try 
	    {
	        $matchedRoute = $route->resolveUrl($request->getFullUrl());
	        $route->setMatchedRoute($matchedRoute);
	        $this->controller = $matchedRoute['map']['controller'];
	        $this->action = $matchedRoute['map']['action'];
	    } catch(RouteNotMatchException $e) {
	        $e->output();
	    }
	    $event->trigger('after:route');
	    todump('__clean');
	    //todump($matchedRoute);
	    
	    //解析完URL，才方便定义框架常量。这些常量主要是给框架使用者使用，框架本身不能依靠之，框架是提供这些常量
	    require(__DIR__ . '/Constants.php');
	     
	    //在实例化控制器之前，有个启动文件会引入，里边的代码都会被执行？
	    
	    $event->trigger('before:controller');
	    $result = $this->runController($matchedRoute);
	    $event->trigger('after:controller');
	    
	    $runningInfo = '';
	    if ($this->isDebug && Config::get('show_running_info')) 
	    {
	        $runningInfo = View::getInstance()->render(__DIR__ . '/exceptions/tpl_common_running_info.php');
	    }
	    if ($result instanceof Response) 
	    {
	        $result->appendContent($runningInfo);
	        $result->send();
	    } 
	    elseif (is_array($result)) 
	    {
	        $response = new Response($result);
	        $response->appendContent($runningInfo);
	        $response->send();
	    } 
	    elseif (is_scalar($result)) 
	    {
	        (new Response((string)$result . $runningInfo))->send();
	    } 
	    else 
	    {
	        throw new \UnexpectedValueException("Not support response type: " . gettype($result));
	    }
	}
	
	public function dispatch($controller, $action, array $parameters = [])
	{
	    return call_user_func_array([new $controller, $action], $parameters);
	}
	
	public function getActionParameters($className, $methodName)
	{
	    $reflectionMethod = new \ReflectionMethod($className, $methodName);
	    $parameters = $reflectionMethod->getParameters();
	    if (empty($parameters)) 
	    {
	        return [];
	    }
	    $out = [];
	    foreach ($parameters as $param) 
	    {
	        $paramName = $param->name;
	        $reflectionParameter = new \ReflectionParameter([$className, $methodName], $paramName);
	        $dependentClass = $reflectionParameter->getClass();
	        if (!empty($dependentClass)) 
	        {
	            throw new \UnexpectedValueException("Not support object inject in method parameter: {$dependentClass->name}");
	        }
	        if ($reflectionParameter->isDefaultValueAvailable()) 
	        {
	            $out[$paramName] = $reflectionParameter->getDefaultValue();
	        } 
	        else 
	        {
	            $out[$paramName] = '';
	        }
	    }
	    return $out;
	}
	

}