<?php
/**
* @desc 标签服务
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年7月24日    上午12:00:48
*/
namespace services;

use models\Tag;
use models\TagTopic;

class TagService
{
    public static function getById($id, $fields = '*')
    {
        if (empty($id) || !ctype_digit(strval($id)))
        {
            return [];
        }
        return Tag::model()->getOne($fields, ['id' => $id]);
    }
    
    public static function getByName($name, $fields = '*')
    {
        if (empty($name))
        {
            return [];
        }
        return Tag::model()->getOne($fields, ['name' => "'$name'"]);
    }
    /**
     * 通过标签ID数组查找
     * @param array $idList
     * @param string $fields
     * @param string $orderby
     * @param string $order
     * @return multitype:
     */
    public static function getByIdList(array $idList, $fields = '*', $orderby = '', $order = '')
    {
        if (empty($idList) || !is_array($idList))
        {
            return [];
        }
        $whereStr = sprintf("(%s)", implode(',', $idList));
        return Tag::model()->getList($fields, ['id' => [$whereStr, 'IN']], $orderby, $order);
    }
    
    /**
     * 通过文章ID数组查找
     * @param array $topicIdList
     */
    public static function getByTopicIdList(array $topicIdList)
    {
        if (empty($topicIdList) || !is_array($topicIdList))
        {
            return [];
        }
        $whereStr = sprintf("(%s)", implode(',', $topicIdList));
        //关联结果
        $tagTopicMapList = TagTopic::model()->getList('*', ['topic_id' => [$whereStr, 'IN']]);
        if (empty($tagTopicMapList))
        {
            return [];
        }
        //标签ID数组
        $tagIdList = array_column($tagTopicMapList, 'tag_id');
        //标签数组
        $tagList = self::getByIdList(array_unique($tagIdList));
        if (empty($tagList))
        {
            return [];
        }
        //整理数据
        $result = [];
        $tagList = array_column($tagList, null, 'id');
//         dump($tagTopicMapList, $tagList);
        foreach ($tagTopicMapList as $item)
        {
            if (isset($tagList[$item['tag_id']]))
            {
                $result[$item['topic_id']][] = $tagList[$item['tag_id']];
            }
        }
        return $result;
    }
    
    public static function getTotal()
    {
        return Tag::model()->getTotal();
    }
}