<?php
namespace models;

use framework\Model;

class Tag extends Model
{
    public function policyId()
    {
        return 'default';
    }
    
    public function tableName()
    {
        return 'tag';
    }
    
    public function getList($field = '*', array $where = [], $orderby = '', $order = '', $limit = '20')
    {
        return $this->select(static::tableName(), $field, $where, $orderby, $order, $limit);
    }
    
    public function getOne($field = '*', array $where = [], $orderby = '', $order = '')
    {
        return $this->selectOne(static::tableName(), $field, $where, $orderby, $order);
    }
    
    public function getTotal($field = '*', array $where = [])
    {
        return $this->count(static::tableName(), $where, $field);
    }
}