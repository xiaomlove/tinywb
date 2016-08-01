<?php
/**
* @desc widget小挂件
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年8月1日    下午9:30:19
*/

namespace framework;

abstract class Widget
{
    protected $request;
    
    private static $widgets = [];
    
    protected function __construct()
    {
        $this->request = Request::getInstance();
    }
    
    abstract public function run();
    
    private static function buildKey($param)
    {
        if ($param === true)
        {
            $key = 'true';
        }
        elseif ($param === false)
        {
            $key = 'false';
        }
        elseif (is_null($param))
        {
            $key = 'null';
        }
        elseif (is_scalar($param))
        {
            $key = strval($param);
        }
        elseif ($param instanceof \Closure)
        {
            return false;//不支持闭包
        }
        elseif (is_object($param))
        {
            $key = spl_object_hash($param);
        }
        elseif (is_array($param))
        {
            $key = serialize($param);
        }
        else 
        {
            return false;
        }
        return strtolower(get_called_class()) . '__' . $key;
    }
    
    /**
     * 获取视图目录，如若要特殊化请在子类覆盖之。或者子类定义viewPath属性。
     * 或者在启动文件中修改配置。或者绑定before_action等事件根据情况修改配置，总之方法很多
     * @throws \RuntimeException
     * @return string
     */
    protected function getViewPath()
    {
        $viewPath = '';
        if (!empty($this->viewPath)) {
            $viewPath = rtrim($this->viewPath, '/\\');
        } else {
            $viewPath = Config::get('default_widget_view_path');
        }
        if (!is_dir($viewPath)) {
            throw new \RuntimeException("viewPath: $viewPath is not exists.");
        }
        return $viewPath;
    }
    
    private function getViewFile($viewFile)
    {
        $viewPath = $this->getViewPath();
        $viewFile = "{$viewPath}/{$viewFile}";
        if (!is_file($viewFile)) {
            throw new \RuntimeException("view: $viewFile is not exists.");
        }
        return $viewFile;
    }
    
    protected function render($viewFile, array $data = [])
    {
        $viewFile = $this->getViewFile($viewFile);
        $result = View::getInstance()->render($viewFile, $data, $this);
        return $result;
    }
    
    public static function widget($param = null)
    {
        $key = self::buildKey($param);
        if ($key === false)
        {
            throw new \InvalidArgumentException("Invalid param: " . print_r($param));
        }
        if (isset(self::$widgets[$key]))
        {
            return self::$widgets[$key];
        }
        $instance = new static();
        ob_start();
        $result = call_user_func_array([$instance, 'run'], (array)$param);
        $content = ob_get_clean();
        if ($result === null)
        {
            //没有return，直接echo了。
            return self::$widgets[$key] = $content;
        }
        else 
        {
            return self::$widgets[$key] = $result;
        }
        
    }
}