<?php
namespace models;

use framework\Model;

class Stat extends Model
{
    const META_KEY_TOPIC_TOTAL = 'topic_total';
    
    const META_KEY_TOPIC_PUBLISH = 'topic_publish';
    
    const META_KEY_TOPIC_DRAFT = 'topic_draft';
    
    const META_KEY_TOPIC_PRIVATE = 'topic_private';
    
    const KEY_TOPIC_PV_PREFIX = 'topic_pv_';
    
    const KEY_TAG_HEAT_TOPIC_VIEW_PREFIX = 'tag_heat_topic_view_';
    
    const KEY_TAG_HEAT_TAG_VIEW_PREFIX = 'tag_heat_tag_view_';
    
    
    public function policyId()
    {
        return 'default';
    }
    
    public function tableName()
    {
        return 'stat';
    }
    
    public function getPv($num = 5, $order = 'meta_value DESC')
    {
        $tableName = static::tableName();
        $prefix = self::KEY_TOPIC_PV_PREFIX;
        $sql = "SELECT * FROM $tableName WHERE meta_key LIKE '{$prefix}%' ORDER BY $order LIMIT $num";
        return $this->fetchAll($sql);     
    }
    
    /**
     * 增加文章PV
     * @param unknown $topicId
     * @return Ambigous <\framework\db\drivers\false, number>
     */
    public function increaseTopicPv($topicId)
    {
        $key = self::KEY_TOPIC_PV_PREFIX . "$topicId";
        $tableName = static::tableName();
        $sql = "INSERT INTO $tableName (id, meta_key, meta_value) VALUES (null, '$key', 1) ON DUPLICATE KEY UPDATE meta_value = meta_value + 1";
        return $this->execute($sql);
    }
    
    /**
     * 浏览文章增加标签热度
     * @param unknown $tagId
     */
    public function increaseTagHeatByViewTopic(array $tagIdArr)
    {
        return $this->increaseTagHeat($tagIdArr, self::KEY_TAG_HEAT_TOPIC_VIEW_PREFIX, 1);
    }
    
    /**
     * 点击标签增加标签热度
     * @param unknown $tagId
     */
    public function increaseTagHeatByViewTag(array $tagIdArr)
    {
        return $this->increaseTagHeat($tagIdArr, self::KEY_TAG_HEAT_TAG_VIEW_PREFIX, 1);
    }
    
    private function increaseTagHeat(array $tagIdArr, $type, $value)
    {
        $tableName = static::tableName();
        $sql = "INSERT INTO $tableName (id, meta_key, meta_value) VALUES ";
        foreach ($tagIdArr as $item)
        {
            if ($item && ctype_digit(strval($item)))
            {
                $sql .= "(null, '{$type}{$item}', $value),";
            }
        }
        $sql = rtrim($sql, ',');
        $sql .= " ON DUPLICATE KEY UPDATE meta_value = meta_value + $value";
        return $this->execute($sql);
    }
    
    /**
     * 通过标签热度——查看文章得来的，获取标签
     * @param unknown $num
     * @return Ambigous <multitype:, multitype:mixed >
     */
    public function getTagByHeatOfViewTopic($num)
    {
        return $this->getTagByHeat(self::KEY_TAG_HEAT_TOPIC_VIEW_PREFIX, $num);
    }
    
    /**
     * 通过标签热度——查看标签得来的，获取文章
     * @param unknown $num
     * @return Ambigous <multitype:, multitype:mixed >
     */
    public function getTagByHeatOfViewTag($num)
    {
        return $this->getTagByHeat(self::KEY_TAG_HEAT_TAG_VIEW_PREFIX, $num);
    }
    
    private function getTagByHeat($prefix, $num)
    {
        $result = $this->getList('*', ['meta_key' => ["'{$prefix}%'", 'LIKE']], 'meta_value DESC', $num);
        if (empty($result))
        {
            return [];
        }
        $out = [];
        foreach ($result as $item)
        {
            $tagId = str_replace($prefix, '', $item['meta_key']);
            $out[$tagId] = ['tag_id' => $tagId, 'weigh' => $item['meta_value']];
        }
        return $out;
    }
}