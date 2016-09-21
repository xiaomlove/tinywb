<?php

namespace framework;

class Validator
{
    private static $defaultMessage = [
        'required' => ':attr为必填项',
        'number' => ':attr必须是数字',
        'positive_integer' => ':attr必须为正整数',
        'email' => ':attr不是合法的邮箱格式',
        'ip' => ':attr必须是合法的IP格式',
        'equal' => ':attr的值必须为:target',
        'equal_to' => ':attr的值必须跟:target的值相等',
        'regular' => ':attr值非法',
        'url' => ':attr不是合理的URL格式',
        'phone' => ':attr不是合理的手机格式',
        'in' => ':attr的值只能是:target中的某个',
        'max' => ':attr的最大值是:target',
        'min' => ':attr的最小值是:target',
        'max_length' => ':attr最大长度不能超过:target',
        'min_length' => ':attr最小长度不能小于:target',
        'max_counts' => ':attr个数不能超过:target',
        'min_counts' => ':attr个数不能少于:target',
    ];

    const VALIDATE_TYPE_ONE_ERROR = 1;//任何一个字段出现错误即停止

    const VALIDATE_TYPE_ONE_ATTR = 2;//每个字段只获取一个错误

    const VALIDATE_TYPE_ALL_ERROR = 3;//验证完所有字段获得所有错误
    
    //需要目标值的规则
    private static $needTargetRule = ['max', 'min', 'max_length', 'min_length', 'max_counts', 'min_counts', 'equal', 'equal_to', 'regular'];
    
    private $data = [];
    
    private $rules = [];
    
    private $customMessage = [];
    
    private $customAttr = [];
    
    private $errors = [];
    
    public function __construct(array $data, array $rules, array $customMessage = [], array $customAttr = [], $type = self::VALIDATE_TYPE_ONE_ERROR)
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
                switch ($type)
                {
                    case self::VALIDATE_TYPE_ONE_ERROR:
                        if ($this->hasError())
                        {
                            return;
                        }
                        break;
                    case self::VALIDATE_TYPE_ONE_ATTR:
                        if ($this->hasError($attr))
                        {
                            continue 2;
                        }
                        break;
                    case self::VALIDATE_TYPE_ALL_ERROR:
                        break;
                    default:
                        throw new \InvalidArgumentException("Invalid type: $type");
                }
                foreach ($ruleArr as $rule)
                {
                    switch ($type)
                    {
                        case self::VALIDATE_TYPE_ONE_ERROR:
                            if ($this->hasError())
                            {
                                return;
                            }
                            break;
                        case self::VALIDATE_TYPE_ONE_ATTR:
                            if ($this->hasError($attr))
                            {
                                continue 2;
                            }
                            break;
                        case self::VALIDATE_TYPE_ALL_ERROR:
                            break;
                        default:
                            throw new \InvalidArgumentException("Invalid type: $type");
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
        
        $data = $this->getData($attr);
            
        if (is_null($data))
        {
            //没有值，直接不通过，也不分场景之类的了
            $validateResult = false;
        }
        else
        {
            $validateResult = call_user_func([$this, $method], $data, $target);
        }
        
        if (!$validateResult)
        {
            $message = $this->getErrorMessage($attr, $rule, $target);
            if (!empty($message))
            {
                $this->setError($attr, $message);
            }
            else
            {
                throw new \RuntimeException("can't get errorMessage, attr: $attr, rule: $rule, target: $target");
            }
        }
        else
        {
            return true;
        }
    }
    
    public static function make(array $data, array $rules, array $customMessage = [], array $customAttr = [], $type = self::VALIDATE_TYPE_ONE_ERROR)
    {
        return new static($data, $rules, $customMessage, $customAttr, $type);
    }
    
    private function getData($attr)
    {
        $attrKeyArr = explode('.', $attr);
        $data = null;
        foreach ($attrKeyArr as $attrKey)
        {
            if (is_null($data))
            {
                if (isset($this->data[$attrKey]))
                {
                    $data = $this->data[$attrKey];
                }
                else 
                {
                    return null;
                }
            }
            else 
            {
                if (isset($data[$attrKey]))
                {
                    $data = $data[$attrKey];
                }
                else
                {
                    return null;
                }
            }
        }
        return $data;
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
    private function validate_required($value)
    {
        if (is_string($value))
        {
            $value = trim($value);
        }
        return $value !== '' && !is_null($value);
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
        return is_numeric($target) && mb_strlen($value, 'UTF-8') <= $target;
    }
    
    //最小长度
    private function validate_min_length($value, $target)
    {
        return is_numeric($target) && mb_strlen($value, 'UTF-8') >= $target;
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

    //手机
    private function validate_phone($value)
    {
        return preg_match('/^(13|15|18|14|17)[0-9]{9}$/', $value) > 0;
    }

    //equal
    private function validate_equal($value, $target)
    {
        return $value == $target;
    }

    //equal_to
    private function validate_equal_to($value, $target)
    {
        $targetValue = $this->getData($target);
        return $value == $targetValue;
    }

    //regular
    private function validate_regular($value, $target)
    {
        $isRegularValid = @preg_match($target, null) !== false;//正则是否合法
        if (!$isRegularValid)
        {
            throw new \InvalidArgumentException("Invalid regular: $target");
        }
        return preg_match($target, $value) > 0;
    }

    //in
    private function validate_in($value, $target)
    {
        $evalResult = @eval('$targetArr = ' . $target . ';');
        if ($evalResult === false)
        {
            throw new \InvalidArgumentException("rule 'in' target invalid, target: $target");
        }
        if (!isset($targetArr) || !is_array($targetArr))
        {
            throw new \InvalidArgumentException("rule 'in' target invalid, not array, target: $target");
        }
        return in_array($value, $targetArr);
    }
}