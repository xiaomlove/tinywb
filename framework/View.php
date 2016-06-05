<?php
/**
* @desc 视图类，用于渲染视图。纯静态
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年6月5日    上午1:38:04
*/

namespace framework;

class View
{
    public static function render($view, array $data = [])
    {
        if (!file_exists($view)) {
            throw new \Exception("view: $view not exists.");
        }
        extract($data);
        ob_start();
        ob_implicit_flush(0);
        require $view;
        return ob_get_clean();
    }
}