<?php
/**
* @desc 统计服务
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年7月24日    上午12:00:48
*/
namespace services;

use models\Stat;
use framework\traits\PropertyCache;

class StatService
{
    use PropertyCache;
    
    public static function getById($id, $fields = '*')
    {
        if (empty($id) || !ctype_digit(strval($id)))
        {
            return [];
        }
        return Stat::model()->getOne($fields, ['id' => $id]);
    }
    
    public static function getBykey($key, $fields = '*')
    {
        if (empty($key))
        {
            return [];
        }
        $propertyCacheKey = self::buildParamKey([$key, $fields]);
        if (self::hasPropertyCache($propertyCacheKey))
        {
            return self::getPropertyCache($propertyCacheKey);
        }
        $result = Stat::model()->getOne($fields, ['meta_key' => "'$key'"]);
        self::setPropertyCache($propertyCacheKey, $result);
        return $result;
    }
    
    public static function getByIdList(array $idList, $fields = '*', $order = '')
    {
        if (empty($idList) || !is_array($idList))
        {
            return [];
        }
        $whereStr = sprintf("(%s)", implode(',', $idList));
        return Stat::model()->getList($fields, ['id' => [$whereStr, 'IN']], $order);
    }
}