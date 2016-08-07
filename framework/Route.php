<?php

namespace framework;

use framework\traits\Singleton;
use framework\exceptions\RouteNotMatchException;
use framework\exceptions\UrlCanNotCreateException;
use framework\traits\PropertyCache;

class Route
{
    use Singleton, PropertyCache;
    
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
        if (empty($urlPath)) 
        {
            throw new RouteNotMatchException("Invalid URL, " . print_r($urlPath, true), $url);
        }
        $request = Request::getInstance();
        $urlAndLevel = self::getUrlAndLevel($urlPath);
        $urlPath = $urlAndLevel['url'];
        $level = $urlAndLevel['level'];
        $method = $request->getMethod();
        if (empty(self::$routeMaps[$method][$level])) 
        {
            throw new RouteNotMatchException("No route match, method: '$method', level: $level", $url);
        }
        $maps = self::$routeMaps[$method][$level];
        
        $matched = ['map' => '', 'method' => $method, 'params' => [], 'index' => 0, 'total' => count($maps)];
        foreach ($maps as $k => &$map) 
        {
            if ($map['url'] === $urlPath) 
            {
                //直接等了，就是它了
                $matched['map'] = $map;
                $matched['index'] = $k;
                break;
            }
            $pattern = '/{(.+)}/U';//找参数，全部换为[.+];
            $mapUrl = $map['url'];
            $paramsNum = preg_match_all($pattern, $mapUrl, $matches, PREG_SET_ORDER);
            todump($matches);
            if (empty($paramsNum)) 
            {
                //又不直接等，又没有参数，不可能，跳过
                $map['reason'] = "not equal no param, '$mapUrl'   not match   '$pattern'";
                continue;
            }
            $params = [];//参数数组
            foreach ($matches as $paramInfo) 
            {
                $params[] = $paramInfo[1];
                $mapUrl = str_replace($paramInfo[0], '(.+)', $mapUrl);//如将{tagName}换为(.+)
            }
            $mapUrl = "|$mapUrl|";
            todump($mapUrl);
            $resultNum = preg_match($mapUrl, $urlPath, $resultMatches);
//             dump($resultMatches);
            if ($resultNum === 0) 
            {
                //没戏
                $map['reason'] = "'$urlPath' not match '$mapUrl'";
                continue;
            }
            $paramsValue = array_slice($resultMatches, 1);
            //再逐个检查是否符合options中要要求
            $keyValues = array_combine($params, $paramsValue);//组合url中的键和值，如id => 12, age =>23
            foreach ($keyValues as $key => $value)
            {
                if (isset($map['options'][$key]))
                {
                    if (!preg_match($map['options'][$key], $value))
                    {
                        //没戏
                        $map['reason'] = "key: {$key}'s value: $value not match '{$map['options'][$key]}'";
                        continue 2;
                    }
                }
            }
            
            $matched['map'] = $map;
            $matched['index'] = $k;
            $matched['params'] = $keyValues;
            break;
        }
        if (empty($matched['map'])) 
        {
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
    
    public function createUrl($controllerAction, array $params = [], $anchor = '')
    {
        try
        {
            $url = self::createUrlFromRouteMapsFlip($controllerAction, $params, $anchor);
        }
        catch (UrlCanNotCreateException $e)
        {
            $e->output();
        }
        return $url;
    }
    
    private static function createUrlFromRouteMapsFlip($controllerAction, array $params = [], $anchor = '')
    {
        if (!isset(self::$routeMapsFlip[$controllerAction]))
        {
            throw new \InvalidArgumentException("Invalid controllerAction: $controllerAction, not register yet.");
        }
        $routeMapsFlip = self::$routeMapsFlip[$controllerAction];
        $url = '';
        foreach ($routeMapsFlip as &$route)
        {
            if (strpos($route['url'], '{') === false)
            {
                //无参数，就是它了
                $url = $route['url'];
                break;
            }
            //否则，找参数
            $findRouteParamsPattern = '|{([\w]+)}|';
            $routeParamsNum = preg_match_all($findRouteParamsPattern, $route['url'], $routeParamsMatches);
            todump($routeParamsNum, $routeParamsMatches);
        
            if (empty($routeParamsNum))
            {
                //有参数，却又无法匹配{}之类
                $route['reason'] = "have params, but {$route['url']} not match '$findRouteParamsPattern'";
                continue;
            }
            $routeParams = $routeParamsMatches[1];//得到url中的参数
        
            if ($routeParamsNum !== count($params))
            {
                //有参数，也找到了，但个数不一致
                $route['reason'] = "have params, but num: $routeParamsNum != " . count($params);
                continue;
            }
            $replace = [];//进行替换的值，为了保持顺序
            foreach ($routeParams as $routeParam)
            {
                if (!isset($params[$routeParam]))
                {
                    $route['reason'] = "params not have the key: $routeParam";
                    continue 2;//所需要参数不全，跳过
                }
                if (isset($route['options'][$routeParam]))
                {
                    //对参数有要求
                    $optionPattern = $route['options'][$routeParam];
                    if (!preg_match($optionPattern, $params[$routeParam]))
                    {
                        $route['reason'] = "{$routeParam}'s value '{$params[$routeParam]}' not match options pattern: $optionPattern";
                        continue 2;//传递过来的数值通不过验证
                    }
                }
                $replace[] = $params[$routeParam];
            }
            //成功得到
            $url = str_replace($routeParamsMatches[0], $replace, $route['url']);
        }
        if (empty($url))
        {
            throw new UrlCanNotCreateException("url can not create !", $controllerAction, $params, $routeMapsFlip);
        }
        else
        {
            $baseUrl = Request::getInstance()->getBaseUrl();
            $url = "$baseUrl/$url";
            return empty($anchor) ? $url : $url . '#' . $anchor;
        }
    }
}