<?php
/**
* @desc 视图类，用于渲染视图。
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年6月5日    上午1:38:04
*/

namespace framework;

use framework\traits\Singleton;

class View
{
    use Singleton;
    
    public $context;//调用者，环境等。控制器调用时会传递控制器对象过来
    
    public function render($view, array $data = [], $context = null)
    {
        if (!file_exists($view)) {
            throw new \Exception("view: $view not exists.");
        }
        $this->context = $context;
        unset($context);
        extract($data);
        //ob_clean();
        ob_start();
        ob_implicit_flush(0);
        require $view;
        return ob_get_clean();
    }
}