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
        return 'topic';
    }
    
    public function getList($field = '*', array $where = array(), $order = 'id DESC', $limit = '')
    {
        return $this->select(static::tableName(), $field, $where, $order, $limit);
    }
}