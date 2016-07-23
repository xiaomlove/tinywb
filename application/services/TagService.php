<?php
/**
* @desc 标签服务
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年7月24日    上午12:00:48
*/
namespace services;

use models\Tag;

class TagService
{
    public static function getById($id, $fields = '*')
    {
        if (empty($id) || !ctype_digit(strval($id)))
        {
            return [];
        }
        return Tag::model()->getOne($fields, ['id' => $id]);
    }
    
    public static function getByName($name, $fields = '*')
    {
        if (empty($name))
        {
            return [];
        }
        return Tag::model()->getOne($fields, ['name' => $name]);
    }
    
    public static function getByIdList(array $idList, $fields = '*', $orderby = '', $order = '')
    {
        if (empty($idList) || !is_array($idList))
        {
            return [];
        }
        $whereStr = sprintf("(%s)", implode(',', $idList));
        return Tag::model()->getList($fields, ['id' => [$whereStr, 'IN']], $orderby, $order);
    }
}