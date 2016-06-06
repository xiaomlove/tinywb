<?php
/**
* @desc 异常与错误处理类 
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年6月3日    上午2:07:17
*/

namespace framework\exceptions;

use framework\Config;
use framework\View;
use framework\Request;
use framework\Response;

class AppExceptionHandler
{
    private static $errLevels = [
        1 => 'E_ERROR',
        2 => 'E_WARNING',
        4 => 'E_PARSE',
        8 => 'E_NOTICE',
        16 => 'E_CORE_ERROR',
        32 => 'E_CORE_WARNING',
        64 => 'E_COMPILE_ERROR',
        128 => 'E_COMPILE_WARNING',
        256 => 'E_USER_ERROR',
        512 => 'E_USER_WARNING',
        1024 => 'E_USER_NOTICE',
        2048 => 'E_STRICT',
        4096 => 'E_RECOVERABLE_ERROR',
        8192 => 'E_DEPRECATED',
        16384 => 'E_USER_DEPRECATED',
        30719 => 'E_ALL'
    ];
    /**
     * 注册错误与异常处理机制
     */
    public static function register()
    {
        if (APP_DEGUB) {
            error_reporting(E_ALL);
        }
        
        $useFrameworkExceptionHandler = Config::get('use_framework_exception_handler');
        if ($useFrameworkExceptionHandler) {
            set_exception_handler([__CLASS__, 'defaultExceptionHandler']);//不能私有，必须公开
        }
        
        $useFrameworkErrorHandler = Config::get('use_framework_error_handler');
        if ($useFrameworkErrorHandler) {
            ini_set('display_errors', 0);
            set_error_handler([__CLASS__, 'errorHandler']);//不能私有，必须公开
            register_shutdown_function([__CLASS__, 'shutdownFunction']);//不能私有，必须公开
        }
        
        return true;
    }
    /**
     * 没有被try catch捕获的异常处理函数
     */
    public static function defaultExceptionHandler(\Exception $e)
    {
//         echo "<hr>Exception---" . get_class($e) . ": " . $e->getCode() . ", " . $e->getMessage() . "<br/>" .  str_replace("\n", "<br/>", $e->getTraceAsString()) . "</hr>";
//         die;
        self::output($e->getCode(), get_class($e), $e->getFile(), $e->getLine(), $e->getMessage(), $e->getTraceAsString());
    }
    
    /**
     * 错误处理函数
     */
    public static function errorHandler($errno, $errstr, $errfile, $errline, $errcontext)
    {
//         $outStr = self::$errLevels[$errno] . ": {$errstr}---in file: {$errfile}---at line: {$errline}";
//         echo "<hr/>Error---$outStr<hr/>";
//         die;
        self::output($errno, self::$errLevels[$errno], $errfile, $errline, $errstr);
    }
    
    /**
     * 脚本终止处理函数。
     */
    public static function shutdownFunction()
    {
       $errInfo = error_get_last();
       if (!empty($errInfo)) {
           //echo "<hr/>shutdownFunction: " . self::$errLevels[$errInfo['type']] . "---{$errInfo['message']}---in file: {$errInfo['file']}--- at line: {$errInfo['line']} </hr>";
           self::output($errInfo['type'], self::$errLevels[$errInfo['type']], $errInfo['file'], $errInfo['line'], $errInfo['message']);
       }
    }
    
    private static function output($errno, $type, $file, $line, $message, $stack = '')
    {
        $codeArr = file($file, FILE_IGNORE_NEW_LINES);
        $start = max(0, $line - 11);//前边10行
        $end = min(count($codeArr) - 1, $line + 10);//后边10行
        $codeArr = array_slice($codeArr, $start, $end - $start);
//         dump($codeArr);die;
        $html = View::render(__DIR__ . '/tpl_exception.php', [
            'errcode' => $errno,
            'errtype' => $type,
            'errfile' => $file,
            'errline' => $line,
            'errStartLine' => $start + 1,
            'errEndLine' => $end,
            'errMessage' => $message,
            'errStack' => str_replace("\n", '<br/>', $stack),
            'errSourceCode' => $codeArr,
            'constants' => get_defined_constants(true)['user'],
            'includedFiles' => get_included_files(),
            'server' => $_SERVER,
            'get' => $_GET,
            'post' => $_POST,
            'request' => $_REQUEST,
            'cookie' => $_COOKIE,
            'time' => microtime(true) - APP_START_TIME,
        ]);
        
        (new Response($html))->send();
    }
}

