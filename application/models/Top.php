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
    
    public function getHotTags($num = 50)
    {
        $topTableName = static::tableName();
        $tagTableName = Tag::model()->tableName();
        $type = self::TYPE_HOT_TAG;
        $sql = "SELECT tag.* FROM $topTableName as top LEFT JOIN $tagTableName as tag 
                ON top.target = tag.id WHERE top.type = $type 
                ORDER BY top.weigh DESC";
        if (!empty($num))
        {
            $sql .= " LIMIT $num";
        }
        return $this->fetchAll($sql);
    }
    
}