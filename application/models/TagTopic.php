<?php
namespace models;

use framework\Model;

class TagTopic extends Model
{
    const TABLE_NAME = 'topic_tag_map';
    
    public function policyId()
    {
        return 'default';
    }
    
    public function tableName()
    {
        return self::TABLE_NAME;
    }
    
    public function getList($field = '*', array $where = array(), $orderby = '', $order = '', $limit = '')
    {
        return $this->select(static::tableName(), $field, $where, $orderby, $order, $limit);
    }
    
    public function getOne($field = '*', array $where = array(), $orderby = '', $order = '')
    {
        return $this->selectOne(static::tableName(), $field, $where, $orderby, $order);
    }
    
    public function getTopicCountByTagId($tagId)
    {
        return $this->count(static::tableName(), ['tag_id' => $tagId]);
    }
}