<?php
namespace services;

use models\Topic;

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
    
    private static function getList($field = '*', array $where = array(), $orderby = 'id', $order = 'DESC', $limit = '20')
    {
        return Topic::model()->getList($field, $where, $orderby, $order, $limit);
    }
}