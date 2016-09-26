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

    const VALIDATE_ERROR_ONE = 1;//任何一个字段出现出现一个错误即停止

    const VALIDATE_ERROR_ONE_ATTR = 2;//每个字段只获取一个错误

    const VALIDATE_ERROR_ALL_ATTR = 3;//验证完所有字段获得所有错误
    
    
    const VALIDATE_ATTR_WITH_VALUE = 1;//有值才验证，值为空直接通过
    
    const VALIDATE_ATTR_WHETHER_VALUE = 2;//无论是否有值均验证，无值直接不通过
    
    //需要目标值的规则
    private static $needTargetRule = ['max', 'min', 'max_length', 'min_length', 'max_counts', 'min_counts', 'equal', 'equal_to', 'regular', 'in'];
    
    private $data = [];
    
    private $rules = [];
    
    private $customMessage = [];
    
    private $customAttr = [];
    
    private $errors = [];
    
    public function __construct(array $data, array $rules, array $customMessage = [], array $customAttr = [], $errorType = self::VALIDATE_ERROR_ONE_ATTR, $attrType = self::VALIDATE_ATTR_WITH_VALUE)
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
                switch ($errorType)
                {
                    case self::VALIDATE_ERROR_ONE:
                        if ($this->hasError())
                        {
                            return;
                        }
                        break;
                    case self::VALIDATE_ERROR_ONE_ATTR:
                        if ($this->hasError($attr))
                        {
                            continue 2;
                        }
                        break;
                    case self::VALIDATE_ERROR_ALL_ATTR:
                        break;
                    default:
                        throw new \InvalidArgumentException("Invalid errorType: $errorType");
                }
                foreach ($ruleArr as $rule)
                {
                    switch ($errorType)
                    {
                        case self::VALIDATE_ERROR_ONE:
                            if ($this->hasError())
                            {
                                return;
                            }
                            break;
                        case self::VALIDATE_ERROR_ONE_ATTR:
                            if ($this->hasError($attr))
                            {
                                continue 2;
                            }
                            break;
                        case self::VALIDATE_ERROR_ALL_ATTR:
                            break;
                        default:
                            throw new \InvalidArgumentException("Invalid errorType: $errorType");
                    }
                    $this->doValidate($attr, $rule, $attrType);
                }
            }
        }
    }
    
    /**
     * 便捷实例化方法
     * @param array $data 待验证的数据
     * @param array $rules 验证规则
     * @param array $customMessage 自定义错误信息
     * @param array $customAttr 自定义字段名
     * @param integer $type 验证方式，有3种
     * @return \framework\Validator 返回一个validator对象
     */
    public static function make(array $data, array $rules, array $customMessage = [], array $customAttr = [], $errorType = self::VALIDATE_ERROR_ONE_ATTR, $attrType = self::VALIDATE_ATTR_WITH_VALUE)
    {
        return new static($data, $rules, $customMessage, $customAttr, $errorType, $attrType);
    }
    
    private function doValidate($attr, $rule, $attrType)
    {
        $target = '';
        foreach (self::$needTargetRule as $_rule)
        {
            if (stripos($rule, $_rule) === 0)
            {
                //需要target
                $ruleAndTarget = explode(':', $rule);
                if (count($ruleAndTarget, true) !== 2)
                {
                    throw new \InvalidArgumentException("invalid rule: $rule, need target and only one ':'");
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
        
        if (is_null($data) || $data === '')
        {
            //没有值(空字符串当无值处理)。不考虑场景之类
            if ($attrType == self::VALIDATE_ATTR_WITH_VALUE)
            {
                //有值才验证。没有值，规则是必须，直接不通过。否则通过
                if ($rule === 'required')
                {
                    $validateResult = false;
                }
                else 
                {
                    $validateResult = true;
                }
            }
            elseif ($attrType == self::VALIDATE_ATTR_WHETHER_VALUE)
            {
                //无值也验证。无值，那当然不通过了
                $validateResult = false;
            }
            else 
            {
                throw new \InvalidArgumentException("invalid attrType: $attrType");
            }
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
        if (isset($this->customMessage["{$attr}.{$rule}"]))
        {
            $message = $this->customMessage["{$attr}.{$rule}"];
        }
        elseif (isset($this->customMessage[$rule]))
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
   
    /**
     * 设置一个错误
     * @param string $attr 字段名
     * @param string $message 错误信息
     */
    public function setError($attr, $message)
    {
        if (!isset($this->errors[$attr]))
        {
            $this->errors[$attr] = [];
        }
        $this->errors[$attr][] = $message;
        
    }
    
    /**
     * 判断是否有错误，不传递$attr判断所有字段
     * @param string $attr 字段名，有则判断这个字段是否有错误
     * @return boolean
     */
    public function hasError($attr = null)
    {
        if (is_null($attr))
        {
            return count($this->errors) > 0;
        }
        return isset($this->errors[$attr]);
    }
    
    /**
     * 获取错误，不传递$attr获取所有字段 
     * @param string $attr 字段名，有则取这个字段的所有错误
     * @return string|array 有错误为数组，无错误为空字符串
     */
    public function getError($attr = null)
    {
        if (is_null($attr))
        {
            return $this->errors;
        }
        return isset($this->errors[$attr]) ? $this->errors[$attr] : '';
    }
    
    /**
     * 获取第一个错误的字段名
     * @return string
     */
    public function firstAttr()
    {
        if ($this->hasError())
        {
            $keys = array_keys($this->errors);
            return $keys[0];
        }
        else
        {
            return '';
        }
    }
    
    /**
     * 获取第一条错误信息
     * @return string
     */
    public function firstMessage()
    {
        if ($this->hasError())
        {
            reset($this->errors);
            $first = current($this->errors);
            return $first[0];
        }
        else
        {
            return '';
        }
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
            throw new \InvalidArgumentException("rule 'in' target invalid, should be array, such as [1,2], target: $target");
        }
        $targetArr = array_map('strval', $targetArr);
        return in_array($value, $targetArr, true);
    }
}