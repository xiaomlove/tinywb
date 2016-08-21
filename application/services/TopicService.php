<?php
namespace services;

use models\Topic;
use models\TagTopic;
use models\TopicDetail;
use models\Stat;

class TopicService
{
    public static function getHomeArticles($page, $pageSize, $order)
    {
        return static::getList('*', ['status' => Topic::STATUS_PUBLIC], $order, ($page - 1) * $pageSize . ",$pageSize");
    }
    
    public static function getNewest($numbers = 5)
    {
        return static::getList('*', ['status' => Topic::STATUS_PUBLIC], 'publish_time DESC', $numbers);
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
    
    //获取某个标签下的文章数量。这是真实的数量
    public static function getCountsByTagId($tagId)
    {
        if (empty($tagId) || !ctype_digit(strval($tagId)))
        {
            throw new \InvalidArgumentException("Invalid tagId: $tagId");
        }
        return TagTopic::model()->getTopicCountByTagId($tagId);
    }
    
    private static function getList($field = '*', array $where = array(), $order = '', $limit = '')
    {
        return Topic::model()->getList($field, $where, $order, $limit);
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
    
    public static function getPv($id)
    {
        if (empty($id) || !ctype_digit(strval($id)))
        {
            throw new \InvalidArgumentException("Invalid id, it should be an integer");
        }
        $key = "topic_pv_$id";
        $result = StatService::getBykey($key);
        if (empty($result))
        {
            return 1;
        }
        else 
        {
            return $result['meta_value'] + 1;
        }
    }
    
    public static function getHotArticles($num = 5)
    {
        $pvList = Stat::model()->getPv($num);
        if (empty($pvList))
        {
            return [];
        }
        $idArr = [];
        foreach ($pvList as $item)
        {
            $pos = mb_strrpos($item['meta_key'], '_');
            $idArr[] = mb_substr($item['meta_key'], $pos + 1);
        }
        $articleList = self::getByIdList($idArr);
        if (empty($articleList))
        {
            return [];
        }
        $articleList = array_column($articleList, null, 'id');
        $out = [];
        foreach ($idArr as $id)
        {
            if (isset($articleList[$id]))
            {
                $out[] = $articleList[$id];
            }
        }
        return $out;
    }
}