<?php
namespace services;

use models\Topic;
use models\TagTopic;

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
    
    public static function getByIdList(array $idList, $field = '*')
    {
        if (empty($idList) || !is_array($idList))
        {
            return [];
        }
        $whereStr = sprintf("(%s)", implode(',', $idList));
        return static::getList($field, ['id' => [$whereStr, 'IN']]);
    }
    
    public static function getByTagName($tagName, $orderby = '', $order = '', $limit = 10)
    {
        if (empty($tagName))
        {
            return [];
        }
        $tag = TagService::getByName($tagName, 'id');
        if (empty($tag))
        {
            return [];
        }
        $tagId = $tag['id'];
        
        //从关联表取topicId
        $tagTopic = TagTopic::model()->getList('topic_id', ['tag_id' => $tagId], 'topic_id', 'DESC', $limit);
        if (empty($tagTopic))
        {
            return [];
        }
        $topicIdList = array_column($tagTopic, 'topic_id');
        
        //还有状态，没法取的感觉！！！
        
        $where = [
            'status' => Topic::STATUS_PUBLIC,
            
        ];
        return static::getList('*', ['status' => Topic::STATUS_PUBLIC], $orderby, $order, $limit);
    }
    
    private static function getList($field = '*', array $where = array(), $orderby = '', $order = '', $limit = '')
    {
        return Topic::model()->getList($field, $where, $orderby, $order, $limit);
    }
}