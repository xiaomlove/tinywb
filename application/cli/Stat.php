<?php
/**
* @desc 统计相关
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年8月14日    下午10:20:40
*/

namespace cli;

use models\Stat as StatModel;

class Stat extends StatModel
{
    public function updateTopicPv($topicId)
    {
        if (empty($topicId) || !ctype_digit($topicId))
        {
            throw new \InvalidArgumentException("Invalid param topicId: $topicId");
        }
        $key = "topic_pv_$topicId";
        $tableName = static::tableName();
        $sql = "INSERT INTO $tableName VALUES (null, $key, 1) ON DUPLICATE UPDATE meta_value = meta_value + 1";
        return $this->execute($sql);
    }
}