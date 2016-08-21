<?php
/**
* @desc 各种排行服务 
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年8月21日    下午5:34:59
*/
namespace services;

use models\Top;
use models\Stat;

class TopService
{
    public static function updateHotTags()
    {
        $num = 50;
        $tagByHeatOfViewTopic = TagService::getByHeatOfViewTopic($num);
        $tagByHeatOfViewTag = TagService::getByHeatOfViewTag($num);
        $tagByTopicCounts = TagService::getByHeatOfTopicCounts($num);
        $data = [];
        foreach ($tagByHeatOfViewTopic as $tagId => $value)
        {
            if (isset($data[$tagId]))
            {
                $data[$tagId] += $value['weigh'] * 2;
            }
            else 
            {
                $data[$tagId] = $value['weigh'];
            }
        }
        foreach ($tagByHeatOfViewTag as $tagId => $value)
        {
            if (isset($data[$tagId]))
            {
                $data[$tagId] += $value['weigh'] * 4;
            }
            else
            {
                $data[$tagId] = $value['weigh'];
            }
        }
        foreach ($tagByTopicCounts as $tagId => $value)
        {
            if (isset($data[$tagId]))
            {
                $data[$tagId] += $value['weigh'];
            }
            else
            {
                $data[$tagId] = $value['weigh'];
            }
        }
        arsort($data);
        $insert = [];
        $num = 0;
        $type = Top::TYPE_HOT_TAG;
        foreach ($data as $tagId => $weigh)
        {
           if ($num >=50)
           {
               break;
           }
           $num++;
           $insert[] = ['type' => $type, 'target' => $tagId, 'weigh' => $weigh, 'dateline' => $_SERVER['REQUEST_TIME']];
        }
        $topModel = Top::model();
        $topModel->beginTransaction();
        try
        {
            $topModel->delete(['type' => $type]);
            $topModel->insert($insert);
            $topModel->commit();
            return true;
        }
        catch (\PDOException $e)
        {
            $topModel->rollBack();
            echo $e->getMessage();
            return false;
        }
    }
}