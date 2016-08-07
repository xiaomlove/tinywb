<?php
namespace services;

use models\Topic;
use models\TagTopic;
use models\TopicDetail;

class TopicService
{
    public static function getHomeArticles($page, $pageSize, $orderby, $order)
    {
        return static::getList('*', ['status' => Topic::STATUS_PUBLIC], $orderby, $order, ($page - 1) * $pageSize . ",$pageSize");
    }
    
    public static function getNewest($numbers = 5)
    {
        return static::getList('*', ['status' => Topic::STATUS_PUBLIC], 'publish_time', 'DESC', $numbers);
    }
    
    public static function getById($id, $fields = '*')
    {
        if (empty($id) || !ctype_digit(strval($id)))
        {
            throw new \InvalidArgumentException("Invalid id, it should be an integer");
        }
        return Topic::model()->getOne($fields, ['id' => $id]);
    }
    
    public static function getByIdList(array $idList, $field = '*')
    {
        if (!is_array($idList))
        {
            throw new \InvalidArgumentException("Invalid idList, it should be an array");
        }
        elseif (empty($idList))
        {
            return [];
        }
        $whereStr = sprintf("(%s)", implode(',', $idList));
        return static::getList($field, ['id' => [$whereStr, 'IN']]);
    }
    
    public static function getListByTagId($tagId, $limit = 10)
    {
        if (empty($tagId) || !ctype_digit(strval($tagId)))
        {
            throw new \InvalidArgumentException("Invalid tagId: $tagId");
        }
        return Topic::model()->getListByTagId($tagId, $limit);
    }
    
    public static function getCountsByTagId($tagId)
    {
        if (empty($tagId) || !ctype_digit(strval($tagId)))
        {
            throw new \InvalidArgumentException("Invalid tagId: $tagId");
        }
        return TagTopic::model()->getTopicCountByTagId($tagId);
    }
    
    private static function getList($field = '*', array $where = array(), $orderby = '', $order = '', $limit = '')
    {
        return Topic::model()->getList($field, $where, $orderby, $order, $limit);
    }
    
    public static function getDetail($id)
    {
        if (empty($id) || !ctype_digit(strval($id)))
        {
            throw new \InvalidArgumentException("Invalid id, it should be an integer");
        }
        return TopicDetail::model()->getOne('*', ['topic_id' => $id]);
    }
    
    public static function getTags($id)
    {
        if (empty($id) || !ctype_digit(strval($id)))
        {
            throw new \InvalidArgumentException("Invalid id, it should be an integer");
        }
        $result = TagService::getByTopicIdList([$id]);
        if (empty($result))
        {
            return [];
        }
        else 
        {
            return $result[$id];
        }
    }
}