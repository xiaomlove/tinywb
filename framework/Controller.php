<?php
/**
* @desc 控制器基类
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年6月10日    下午4:00:24
*/

namespace framework;

abstract class Controller
{
    public $request;
    
    public $route;
    
    public $autoload;
    
    public $view;//当前即将要渲染的视图文件，在视图中可以获取得到
    
    public $layoutPath;
    
    public $layout;
    
    public $pageTitle;
    
    public $viewPath;
    
    public function __construct()
    {
        $this->request = Request::getInstance();
        $this->route = Route::getInstance();
        $this->autoload = Autoload::getInstance();
    }
    
    public function fetch($view, array $data = [])
    {
        $this->view = $view;
        return View::getInstance()->render($view, $data, $this);
    }
    
    //带布局
    public function display($view, array $data = [], $statusCode = 200, array $headers = [])
    {
        $this->view = $view;
        $layoutFile = $this->getLayoutFile();
        $viewFile = $this->getViewFile();
        $html = View::getInstance()->render($viewFile, $data, $this);//视图部分
        if (!empty($layoutFile)) {
            $html = View::getInstance()->render($layoutFile, array_merge($data, ['__content' => $html]));
        }
        $response = new Response($html, $statusCode, $headers);
        return $response;
    }
    
    //不带布局
    public function displayPartial($view, array $data = [], $statusCode = 200, array $headers = [])
    {
        $this->view = $view;
        $viewFile = $this->getViewFile();
        
        $html = View::getInstance()->render($viewFile, $data, $this);
        
        $response = new Response($html, $statusCode, $headers);
        return $response;
    }
    
    /**
     * 获取视图文件夹
     * 1、如果在控制器属性$viewPath有值，则取这个值
     * 2、否则则取配置中的值。无法跟控制器走，因为控制器可以随便放的
     */
    public function getViewPath()
    {
        $viewPath = '';
        if (!empty($this->viewPath)) {
            $viewPath = rtrim($this->viewPath, '/\\');
        } else {
            $viewPath = Config::get('default_view_path');
        }
        if (!is_dir($viewPath)) {
            throw new \RuntimeException("viewPath: $viewPath is not exists.");
        }
        return $viewPath;
    }
    
    public function getViewFile()
    {
        $viewPath = $this->getViewPath();
        $viewFile = "{$viewPath}/" . $this->view;
        if (!is_file($viewFile)) {
            throw new \RuntimeException("view: $viewFile is not exists.");
        }
        return $viewFile;
    }
    
    public function getLayoutPath()
    {
        $layoutPath = '';
        if (!empty($this->layoutPath)) {
            $layoutPath = $this->layoutPath;
        } else {
            $layoutPath = Config::get('default_layout_path');
        }
        if (!is_dir($layoutPath)) {
            throw new \RuntimeException("layoutPath: $layoutPath is not exists.");
        }
        return $layoutPath;
    }
    
    /**
     * 获取布局文件。
     */
    public function getLayoutFile()
    {
        if (!empty($this->layout) && is_string($this->layout)) {
            //使用布局
            $layoutPath = $this->getLayoutPath();
            $layoutFile = rtrim($layoutPath, '/\\') . '/' . trim($this->layout, '/\\');
            if (!is_file($layoutFile)) {
                throw new \RuntimeException("layoutFile: $layoutFile is not exists.");
            }
            return $layoutFile;
        } else {
            //不使用
            return '';
        }
    }
    
    public function outJson($code = 0, $msg = 'OK', $data = [], $statusCode = 200, array $headers = [])
    {
        $out = [
            'code' => $code,
            'msg' => $msg,
            'data' => (array)$data,
            'timestamp' => $_SERVER['REQUEST_TIME'],
            'usetime' => microtime(true) - APP_START_TIME,
        ];
        var_dump($out);die;
        return new Response($out, $statusCode, $headers);
    }
    
    public function validate(array $data, array $rules, array $customMessage = [], array $customAttr = [], $bulk = false)
    {
        $validator = Validator::make($data, $rules, $customMessage, $customAttr, $bulk);
        if ($validator->hasError())
        {
            
        }
    }
}

