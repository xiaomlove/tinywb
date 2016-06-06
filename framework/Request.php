<?php
/**
* @desc 请求类，单例
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年6月5日    下午6:19:49
*/

namespace framework;

use framework\traits\ReadonlyProperties;
use framework\traits\Singleton;

class Request
{
	use Singleton, ReadonlyProperties;

	private $server;//$_SERVER
	
	private $get;//$_GET
	
	private $post;//$_POST
	
	private $param;//$_REQUEST
	
	private $header;
	
	private $method;

	private $isGet;

	private $isPost;

	private $isPut;

	private $isDelete;
	
	private $isAjax;

	public function __construct()
	{
		$this->server = &$_SERVER;
		$this->get = &$_GET;
		$this->post = &$_POST;
		$this->param = &$_REQUEST;
		$this->initHeaders();
		
        $method = $this->method = $this->getMethod();
        $this->isGet = $method === 'GET' ? true : false;
        $this->isPost = $method === 'POST' ? true : false;
        $this->isPut = $method === 'PUT' ? true : false;
        $this->isDelete = $method === 'DELETE' ? true : false;
        $this->isAjax = $this->isAjax();
        
        $this->readonlyProperties = ['method', 'isGet', 'isPost', 'isPut', 'isDelete', 'isAjax'];
	}

	public function getMethod()
	{
		$methodParameter = Config::get('method_parameter');
		$method = $this->getParam($methodParameter);
		if (!empty($method)) {
		    return strtoupper($method);
		} elseif (!empty($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
		    return strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
		} elseif (!empty($_SERVER['REQUEST_METHOD'])) {
		    return strtoupper($_SERVER['REQUEST_METHOD']);
		} else {
		    return '';
		}
	}
	
	public function getParam($name = null)
	{
	    if (is_null($name)) {
	        return $this->param;
	    } elseif (!is_string($name)) {
	        throw new \InvalidArgumentException("Invalid name, must be a string.");
	    }
	    return isset($this->param[$name]) ? $this->param[$name] : '';
	}
	
    public function getGet($name = null)
	{
	    if (is_null($name)) {
	        return $this->get;
	    } elseif (!is_string($name)) {
	        throw new \InvalidArgumentException("Invalid name, must be a string.");
	    }
	    return isset($this->get[$name]) ? $this->get[$name] : '';
	}
	
    public function getPost($name = null)
	{
	    if (is_null($name)) {
	        return $this->post;
	    } elseif (!is_string($name)) {
	        throw new \InvalidArgumentException("Invalid name, must be a string.");
	    }
	    return isset($this->post[$name]) ? $this->post[$name] : '';
	}
	
	public function getServer($name = null)
	{
	    if (is_null($name)) {
	        return $this->server;
	    } elseif (!is_string($name)) {
	        throw new \InvalidArgumentException("Invalid name, must be a string.");
	    }
	    return isset($this->server[$name]) ? $this->server[$name] : '';
	}
	
	public function getHeader($name = null)
	{
	    if (is_null($name)) {
	        return $this->header;
	    } elseif (!is_string($name)) {
	        throw new \InvalidArgumentException("Invalid name, must be a string.");
	    }
	    return isset($this->header[$name]) ? $this->header[$name] : '';
	}
	
	private function initHeaders()
	{
	    $headers = [];
	    if (function_exists('getallheaders')) {
	        $headers = getallheaders();
	    } elseif (function_exists('http_get_request_headers')) {
	        $headers = http_get_request_headers();
	    } else {
	        foreach ($_SERVER as $name => $value) {
	            if (strncmp($name, 'HTTP_', 5) === 0) {
	                $headerName = substr($name, 5);
	                $headerName = str_replace('_', '-', $headerName);
	                $headerName = strtolower($headerName);
	                $headerName = ucfirst($headerName);
	                $headers[$headerName] = $value;
	            }
	        }
	    }
	    $this->header = $headers;
	}
	
	public function isAjax()
	{
	    $ajaxId = $this->getServer('HTTP_X_REQUESTED_WITH');//jQuery会添加，自己的如果不添加就不准了
	    return !empty($ajaxId) && $ajaxId === 'XMLHttpRequest';
	}
	
	public function getClientIp()
	{
	    $ip = '';
	    if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown'))
	    {
	        $ip = getenv('HTTP_CLIENT_IP');
	    }
	    elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown'))
	    {
	        $ip = getenv('HTTP_X_FORWARDED_FOR');
	    }
	    elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown'))
	    {
	        $ip = $_SERVER['REMOTE_ADDR'];
	    }
	    return $ip;
	}
	
	public function getServerIp()
	{
	    if (!empty($_SERVER['SERVER_ADDR']))
	    {
	        return $_SERVER['SERVER_ADDR'];
	    } else {
	        return '';
	    }
	}
}