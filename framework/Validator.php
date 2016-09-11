<?php

namespace framework;

class Validator
{
    private static $defaultMessage = [
        'required' => ':attr为必填项',
        'number' => ':attr必须是数字',
        'positive_integer' => ':attr必须为正整数',
        'max' => ':attr的最大值是:target',
        'min' => ':attr的最小值是:target',
        'max_length' => ':attr最大长度不能超过:target',
        'min_length' => ':attr最小长度不能小于:target',
        'email' => ':attr必须是合法的邮箱格式',
        'ip' => ':attr必须是合法的IP格式',
        'max_counts' => ':attr个数不能超过:target',
        'min_counts' => ':attr个数不能少于:target',
    ];
    
    //需要目标值的规则
    private static $needTargetRule = ['max', 'min', 'max_length', 'min_length', 'max_counts', 'min_counts'];
    
    private $data = [];
    
    private $rules = [];
    
    private $customMessage = [];
    
    private $customAttr = [];
    
    private $errors = [];
    
    public function __construct(array $data, array $rules, array $customMessage = [], array $customAttr = [], $bulk = true)
    {
        if (empty($rules))
        {
            return;
        }
        $this->data = $data;
        $this->rules = $rules;
        $this->customMessage = $customMessage;
        $this->customAttr = $customAttr;        
        foreach ($rules as $attrStr => $ruleStr)
        {
            $attrStr = trim(preg_replace('/[\s]+/', '', $attrStr));
            $ruleStr = trim(preg_replace('/[\s]+/', '', $ruleStr));
            $attrArr = explode('|', $attrStr);//多个字段
            $ruleArr = explode('|', $ruleStr);//多个规则
            foreach ($attrArr as $attr)
            {
                if (!$bulk && $this->hasError($attr))
                {
                    continue;
                }
                foreach ($ruleArr as $rule)
                {
                    if (!$bulk && $this->hasError($attr))
                    {
                        continue;
                    }
                    $this->doValidate($attr, $rule);
                }
            }
        }
    }
    
    private function doValidate($attr, $rule)
    {
        $target = '';
        foreach (self::$needTargetRule as $_rule)
        {
            if (stripos($rule, $_rule) !== false)
            {
                //需要target
                $ruleAndTarget = explode(':', $rule);
                if (count($ruleAndTarget, true) !== 2)
                {
                    throw new \InvalidArgumentException("invalid rule: $rule, need target");
                }
                $rule = $ruleAndTarget[0];
                $target = $ruleAndTarget[1];
                break;
            }
        }
        $method = "validate_$rule";
        if (!method_exists($this, $method))
        {
            throw new \InvalidArgumentException("invalid rule: $rule, method: $method is not exist");
        }
        
        if ($rule === 'required')
        {
            $validateResult = call_user_func([$this, $method], $attr, $this->data);
        }
        elseif (!isset($this->data[$attr]))
        {
            //没有值，直接不通过，也不分场景之类的了
            $validateResult = false;
        }
        else 
        {
            $validateResult = call_user_func([$this, $method], $this->data[$attr], $target);
        }
        
        if (!$validateResult)
        {
            $message = $this->getErrorMessage($attr, $rule, $target);
            $this->setError($attr, $message);
            return false;
        }
        else
        {
            return true;
        }
    }
    
    public static function make(array $data, array $rules, array $customMessage = [], array $customAttr = [], $bulk = true)
    {
        return new static($data, $rules, $customMessage, $customAttr, $bulk);
    }
    
    private function getErrorMessage($attr, $rule, $target = '')
    {
        if (isset($this->customMessage[$rule]))
        {
            $message = $this->customMessage[$rule];
        }
        elseif (isset(self::$defaultMessage[$rule]))
        {
            $message = self::$defaultMessage[$rule];
        }
        else
        {
            return false;
        }            
        if (isset($this->customAttr[$attr]))
        {
            $attr = $this->customAttr[$attr];
        }
        return str_replace([':attr', ':target'], [$attr, $target], $message);
    }
   
    
    public function setError($attr, $message)
    {
        if (!isset($this->errors[$attr]))
        {
            $this->errors[$attr] = [];
        }
        $this->errors[$attr][] = $message;
        
    }
    
    public function hasError($attr = null)
    {
        if (is_null($attr))
        {
            return count($this->errors) > 0;
        }
        return isset($this->errors[$attr]);
    }
    
    public function getError($attr = null)
    {
        if (is_null($attr))
        {
            return $this->errors;
        }
        return isset($this->errors[$attr]) ? $this->errors[$attr] : '';
    }
    
    
    
    //必须
    private function validate_required($attr, array $data)
    {
        $attr = trim($attr);
        if (!isset($data[$attr]))
        {
            return false;
        }
        $value = $data[$attr];
        if (is_string($value))
        {
            $value = trim($value);
        }
        return $value !== '';
    }
    
    //整数或数字字符串
    private function validate_number($value)
    {
        return is_numeric($value);
    }
    
    //正整数
    private function validate_positive_integer($value)
    {
        return ctype_digit(strval($value));
    }
    
    //最大值
    private function validate_max($value, $target)
    {
        return is_numeric($value) && is_numeric($target) && $value <= $target;
    }
    
    //最小值
    private function validate_min($value, $target)
    {
        return is_numeric($value) && is_numeric($target) && $value >= $target;
    }
    
    //最大长度
    private function validate_max_length($value, $target)
    {
        return is_numeric($target) && mb_strlen($value) <= $target;
    }
    
    //最小长度
    private function validate_min_length($value, $target)
    {
        return is_numeric($target) && mb_strlen($value) >= $target;
    }
    
    //最大个数，针对数组
    private function validate_max_counts($value, $target)
    {
        return is_array($value) && is_int($target) && count($value) <= $target;
    }
    
    //最小个数，针对数组
    private function validate_min_counts($value, $target)
    {
        return is_array($value) && is_int($target) && count($value) >= $target;
    }
    
    //邮箱
    private function validate_email($value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    //url
    private function validate_url($value)
    {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }
    
    //ip
    private function validate_ip($value)
    {
        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }
}