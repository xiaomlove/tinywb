<?php
/**
* @desc 统计相关
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年8月14日    下午10:20:40
*/

namespace cli;

use framework\Cli;
use models\Stat as StatModel;

class Stat implements Cli
{
    /**
     * 文章PV加1
     * @param unknown $topicId
     * @throws \InvalidArgumentException
     * @return Ambigous <\framework\db\drivers\false, number>
     */
    public function increaseTopicPv($topicId)
    {
        if (empty($topicId) || !ctype_digit(strval($topicId)))
        {
            throw new \InvalidArgumentException("Invalid param topicId: $topicId");
        }
        return StatModel::model()->increaseTopicPv($topicId);
    }
    
    public function increaseTagHeatByViewTopic($tagId)
    {
        if (empty($tagId))
        {
            throw new \InvalidArgumentException("Invalid param tagId");
        }
        return StatModel::model()->increaseTagHeatByViewTopic((array)$tagId);        
    }
    
    public function increaseTagHeatByViewTag($tagId)
    {
        if (empty($tagId))
        {
            throw new \InvalidArgumentException("Invalid param tagId");
        }
        return StatModel::model()->increaseTagHeatByViewTag((array)$tagId);
    }
    
    
    public static function getInstance()
    {
        return new self;
    }
}