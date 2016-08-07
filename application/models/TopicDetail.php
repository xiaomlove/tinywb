<?php
namespace models;

use framework\Model;

class TopicDetail extends Model
{
    public function policyId()
    {
        return 'default';
    }

    public function tableName()
    {
        return self::TABLE_NAME;
    }
    
    const TABLE_NAME = 'topic_detail';
    
    
    public function getList($field = '*', array $where = array(), $orderby = '', $order = '', $limit = '')
    {
        return $this->select(static::tableName(), $field, $where, $orderby, $order, $limit);
    }
    
    public function getOne($field = '*', array $where = [], $orderby = '', $order = '')
    {
        return $this->selectOne(static::tableName(), $field, $where, $orderby, $order);
    }
    
}