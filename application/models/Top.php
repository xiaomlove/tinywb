<?php
namespace models;

use framework\Model;

class Top extends Model
{
    const TYPE_HOT_TAG = 1;//热门标签
    
    public function policyId()
    {
        return 'default';
    }
    
    public function tableName()
    {
        return 'top';
    }
    
}