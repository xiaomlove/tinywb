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
    
    
}