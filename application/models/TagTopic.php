<?php
namespace models;

use framework\Model;

class TagTopic extends Model
{
    public function policyId()
    {
        return 'default';
    }
    
    public function tableName()
    {
        return 'topic_tag_map';
    }
    
    public function getList($field = '*', array $where = array(), $orderby = '', $order = '', $limit = '')
    {
        return $this->select(static::tableName(), $field, $where, $orderby, $order, $limit);
    }
    
    public function getOne($field = '*', array $where = array(), $orderby = '', $order = '')
    {
        return $this->selectOne(static::tableName(), $field, $where, $orderby, $order);
    }
}