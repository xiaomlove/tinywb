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
use models\Tag;

class Stat extends StatModel implements Cli
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
        $key = "topic_pv_$topicId";
        $tableName = static::tableName();
        $sql = "INSERT INTO $tableName (id, meta_key, meta_value) VALUES (null, '$key', 1) ON DUPLICATE KEY UPDATE meta_value = meta_value + 1";
        return $this->execute($sql);
    }
    
    
    public static function getInstance()
    {
        return call_user_func([__CLASS__, 'model']);
    }
}