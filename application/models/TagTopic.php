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
    
    public function getTopicCountByTagId($tagId)
    {
        return $this->count(['tag_id' => $tagId]);
    }
    
}