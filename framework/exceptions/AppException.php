<?php
/**
* @desc 异常处理类 
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年6月3日    上午2:07:17
*/

namespace framework\exceptions;

class AppException extends \Exception
{
    /**
     * 没有被try catch捕获的异常处理函数
     */
    public function defaultExceptionHandler(\Exception $e)
    {
        var_dump($e->getTrace());
    }
    
    /**
     * 错误处理函数，不能被set_exception_handler、try catch捕获
     */
    public function errorExceptionHandler()
    {
        $error = error_get_last();
        var_dump($error);
    }
}

