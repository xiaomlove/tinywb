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
    
    public function getTotal($field = '*', array $where = [])
    {
        return $this->count($where, $field);
    }
}