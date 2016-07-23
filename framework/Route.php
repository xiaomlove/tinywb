<?php

namespace framework;

use framework\traits\Singleton;
use framework\exceptions\RouteNotMatchException;

class Route
{
    use Singleton;
    
    private static $routeMaps = [];//用于解析URL
    
    private static $routeMapsFlip = [];//用于生成URL
    
    private static $methods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'];
    
    private $matchedRoute;
    
   /**
    * options中是对$url中参数的正则验证规则
    * @param unknown $url
    * @param unknown $controllerAction
    * @param unknown $options
    */
    public static function get($url, $controllerAction, array $options = [])
    {
        return self::add('GET', $url, $controllerAction, $options);
    }
    
    public static function post($url, $controllerAction, array $options = [])
    {
        return self::add('POST', $url, $controllerAction, $options);
    }
    
    public static function put($url, $controllerAction, array $options = [])
    {
        return self::add('PUT', $url, $controllerAction, $options);
    }
    
    private static function add($method, $url, $controllerAction, array $options = [])
    {
        $method = strtoupper($method);
        if (!in_array($method, self::$methods)) {
            throw new \InvalidArgumentException("Invalid method: $method");
        }
        $controllerAction = trim($controllerAction, '\\');
        $caArr = explode('@', $controllerAction);
        if (count($caArr) !== 2) {
            throw new \InvalidArgumentException("Invalid controllerAction: $controllerAction, it should be like controller@action.");
        }
        $urlAndLevel = self::getUrlAndLevel($url);
        
        $url = $urlAndLevel['url'];
        $level = $urlAndLevel['level'];
        self::$routeMaps[$method][$level][] = ['url' => $url, 'controller' => $caArr[0], 'action' => $caArr[1], 'options' => $options];
        self::$routeMapsFlip[$controllerAction][] = ['url' => $url, 'options' => $options];
        return true;
    }
    
    private static function getUrlAndLevel($url)
    {
        $url = trim($url);
        if (empty($url)) {
            throw new \InvalidArgumentException("Invalid url: $url, it must be a not empty string.");
        }
        if ($url === '/') {
            $level = 0;
        } else {
            $url = trim($url, '/');
            $level = substr_count($url, '/') + 1;
        }
        return ['url' => $url, 'level' => $level];
    }
    
    private static function injectParams(array $params = [])
    {
        //URL上的参数，肯定是注入GET
        foreach ($params as $name => $value) {
            $_GET[$name] = $value;
            if (!isset($_REQUEST[$name])) {
                //REQUST是以POST为准
                $_REQUEST[$name] = $value;
            }
        }
        return true;    
    }
    
    public function getRouteMaps()
    {
        return self::$routeMaps;
    }
    
    public function getRouteMapsFlip()
    {
        return self::$routeMapsFlip;
    }
    
    
    
    /**
     * 解析一个URL，返回路由表中匹配的项
     * @param unknown $url
     * @throws RouteNotMatchException
     * @return multitype:string multitype: number unknown NULL
     */
    public function resolveUrl($url)
    {
        $urlPath = parse_url($url, PHP_URL_PATH);
        if (empty($urlPath)) {
            throw new RouteNotMatchException("Invalid URL, " . print_r($urlPath, true), $url);
        }
        $request = Request::getInstance();
        $urlAndLevel = self::getUrlAndLevel($urlPath);
        $urlPath = $urlAndLevel['url'];
        $level = $urlAndLevel['level'];
        $method = $request->getMethod();
        if (empty(self::$routeMaps[$method][$level])) {
            throw new RouteNotMatchException("No route match, method: '$method', level: $level", $url);
        }
        $maps = self::$routeMaps[$method][$level];
        todump($maps);
        todump($urlPath);
        //dump();
        
        $matched = ['map' => '', 'method' => $method, 'params' => [], 'index' => 0, 'total' => count($maps)];
        foreach ($maps as $k => &$map) {
            if ($map['url'] === $urlPath) {
                //直接等了，就是它了
                $matched['map'] = $map;
                $matched['index'] = $k;
                break;
            }
            $pattern = '/{(.+)}/U';//找参数，将之换为设定的验证与此正则，如果没有，换为[.+];
            $mapUrl = $map['url'];
            $paramsNum = preg_match_all($pattern, $mapUrl, $matches, PREG_SET_ORDER);
            //todump($matches);
            if (empty($paramsNum)) {
                //又不直接等，又没有参数，不可能，跳过
                $map['reason'] = "not equal no param, '$mapUrl'   not match   '$pattern'";
                continue;
            }
            $params = [];//参数数组
            foreach ($matches as $paramInfo) {
                $params[] = $paramInfo[1];
                if (!empty($map['options'][$paramInfo[1]])) {
                    //该参数有正则验证规则
                    $mapUrl = str_replace($paramInfo[0], '(' . $map['options'][$paramInfo[1]] . ')', $mapUrl);
                } else {
                    //没有设置正则验证
                    $mapUrl = str_replace($paramInfo[0], '(.+)', $mapUrl);
                }
            }
            $mapUrl = "|$mapUrl|";
            todump($mapUrl);
            $resultNum = preg_match($mapUrl, $urlPath, $resultMatches);
            if ($resultNum === 0) {
                //没戏
                $map['reason'] = "'$urlPath' not match '$mapUrl'";
                continue;
            }
            $matched['map'] = $map;
            $matched['index'] = $k;
            $paramsValue = array_slice($resultMatches, 1);
            $matched['params'] = array_combine($params, $paramsValue);
            break;
        }
        if (empty($matched['map'])) {
            throw new RouteNotMatchException("can not match the URL from registered routes", $url, $urlPath, $maps);
        }
        $this->injectParams($matched['params']);
        return $matched;
    }
    
    /**
     * 返回当前请求匹配的路由
     */
    public function getMatchedRoute()
    {
        return $this->matchedRoute;
    }
    
    public function setMatchedRoute($route)
    {
        if (empty($this->matchedRoute)) {
            $this->matchedRoute = $route;
            return true;
        } else {
            return false;
        }
    }
    
}