<?php
/**
* @desc widget小挂件
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年8月1日    下午9:30:19
*/

namespace framework;

use framework\traits\PropertyCache;

abstract class Widget
{
    use PropertyCache;
    
    protected $request;
    
    protected function __construct()
    {
        $this->request = Request::getInstance();
    }
    
    abstract public function run();
    
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
        $key = self::buildParamKey($param);
        $key = strtolower(get_called_class() . '_' . $key);
        if (self::hasPropertyCache($key))
        {
            return self::getPropertyCache($key);
        }
        $instance = new static();
        ob_start();
        $result = call_user_func_array([$instance, 'run'], (array)$param);
        $content = ob_get_clean();
        if ($result === null)
        {
            //没有return，直接echo了。
            self::setPropertyCache($key, $content);
            return $content;
        }
        else 
        {
            self::setPropertyCache($key, $result);
            return $result;
        }
        
    }
}