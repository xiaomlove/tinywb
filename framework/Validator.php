<?php

namespace framework;

class Validator
{
    //整数或数字字符串
    public static function isNumeric($value)
    {
        return is_numeric($value);
    }
    
    //全数字
    public static function isPureNumber($value)
    {
        return ctype_digit(strval($value));
    }
    
    
    
}