<?php
namespace cli;

use models\Tag as TagModel;
use framework\Cli;
use models\TagTopic;

class Tag extends TagModel implements Cli
{
    public static function getInstance()
    {
        return call_user_func([__CLASS__, 'model']);
    }
    
    /**
     * 更新一个标签的文章数量
     * @param unknown $tagId
     * @throws \InvalidArgumentException
     * @return Ambigous <boolean, \framework\db\drivers\false, number>
     */
    public function updateTopicCounts($tagId)
    {
        if (empty($tagId) || !ctype_digit(strval($tagId)))
        {
            throw new \InvalidArgumentException("Invalid param tagId: $tagId");
        }
        $count = TagTopic::model()->getTopicCountByTagId($tagId);
        return $this->update(static::tableName(), ['counts' => $count], ['id' => $tagId]);
    }
}