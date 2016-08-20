<?php
namespace models;

use framework\Model;

class Stat extends Model
{
    const META_KEY_TOPIC_TOTAL = 'topic_total';
    
    const META_KEY_TOPIC_PUBLISH = 'topic_publish';
    
    const META_KEY_TOPIC_DRAFT = 'topic_draft';
    
    const META_KEY_TOPIC_PRIVATE = 'topic_private';
    
    const KEY_TOPIC_PV_PREFIX = 'topic_pv';
    
    const KEY_TAG_HEAT_TOPIC_VIEW_PREFIX = 'tag_heat_topic_view';
    
    const KEY_TAG_HEAT_TAG_VIEW_PREFIX = 'tag_heat_tag_view';
    
    
    public function policyId()
    {
        return 'default';
    }
    
    public function tableName()
    {
        return 'stat';
    }
    
    public function getList($field = '*', array $where = array(), $orderby = '', $order = '', $limit = '')
    {
        return $this->select(static::tableName(), $field, $where, $orderby, $order, $limit);
    }
    
    public function getOne($field = '*', array $where = array(), $orderby = '', $order = '')
    {
        return $this->selectOne(static::tableName(), $field, $where, $orderby, $order);
    }
    
    public function getPv($num = 5, $order = 'meta_value DESC')
    {
        $tableName = static::tableName();
        $sql = "SELECT * FROM $tableName WHERE meta_key LIKE 'topic_pv_%' ORDER BY $order LIMIT $num";
        return $this->fetchAll($sql);     
    }
    
    /**
     * 增加文章PV
     * @param unknown $topicId
     * @return Ambigous <\framework\db\drivers\false, number>
     */
    public function increaseTopicPv($topicId)
    {
        $key = self::KEY_TOPIC_PV_PREFIX . "_$topicId";
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
        return $this->increaseTagHeat($tagIdArr, self::KEY_TAG_HEAT_TAG_VIEW_PREFIX, 10);
    }
    
    private function increaseTagHeat(array $tagIdArr, $type, $value)
    {
        $tableName = static::tableName();
        $sql = "INSERT INTO $tableName (id, meta_key, meta_value) VALUES ";
        foreach ($tagIdArr as $item)
        {
            if ($item && ctype_digit(strval($item)))
            {
                $sql .= "(null, '{$type}_{$item}', $value),";
            }
        }
        $sql = rtrim($sql, ',');
        $sql .= " ON DUPLICATE KEY UPDATE meta_value = meta_value + $value";
        return $this->execute($sql);
    }
}