<?php
namespace models;

use framework\Model;

class Topic extends Model
{
    public function policyId()
    {
        return 'default';
    }

    public function tableName()
    {
        return self::TABLE_NAME;
    }
    
    const TABLE_NAME = 'topic';
    
    const STATUS_PUBLIC = 1;
    
    const STATUS_DRAFT = 2;
    
    const STATUS_DELETED = -1;
    
    const STATUS_PRIVATE = 3;
    
    private static $statusText = [
        self::STATUS_DELETED => '已删除',
        self::STATUS_DRAFT => '草稿',
        self::STATUS_PRIVATE => '私有',
        self::STATUS_PUBLIC => '公开',
    ];
    
    public function getStatusText($status = null)
    {
        if (is_null($status)) {
            return self::$statusText;
        }
        return isset(self::$statusText[$status]) ? self::$statusText[$status] : '未知';
    }
    
    
    public function getListByTagId($tagId, $limit = '')
    {
        $topicTableName = self::TABLE_NAME;
        $mapTableName = TagTopic::model()->tableName();
        $topicStatus = self::STATUS_PUBLIC;
        $sql = "SELECT topic.* 
                FROM $topicTableName topic 
                LEFT JOIN $mapTableName map 
                ON topic.id = map.topic_id 
                WHERE map.tag_id = $tagId 
                ORDER BY map.id DESC";
        if (!empty($limit))
        {
            $sql .= " LIMIT " . strval($limit);
        }
        return $this->fetchAll($sql);
    }
    
}