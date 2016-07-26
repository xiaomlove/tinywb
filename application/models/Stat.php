<?php
namespace models;

use framework\Model;

class Stat extends Model
{
    const META_KEY_TOPIC_TOTAL = 'topic_total';
    
    const META_KEY_TOPIC_PUBLISH = 'topic_publish';
    
    const META_KEY_TOPIC_DRAFT = 'topic_draft';
    
    const META_KEY_TOPIC_PRIVATE = 'topic_private';
    
    
    public function policyId()
    {
        return 'default';
    }
    
    public function tableName()
    {
        return 'stat';
    }
    
    public function getList($field = '*', array $where = array(), $orderby = '', $order = '', $limit = '20')
    {
        return $this->select(static::tableName(), $field, $where, $orderby, $order, $limit);
    }
    
    public function getOne($field = '*', array $where = array(), $orderby = '', $order = '')
    {
        return $this->selectOne(static::tableName(), $field, $where, $orderby, $order);
    }
}