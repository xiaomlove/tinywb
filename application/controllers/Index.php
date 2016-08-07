<?php
namespace controllers;

use services\TopicService;
use services\StatService;
use services\TagService;

class Index extends Common
{
    public $layout = 'main.php';
    
    public function index()
    {
        $page = $this->request->getParam('page');
        $page = empty($page) || !ctype_digit($page) ? 1 : intval($page);
        if ($page > 100)
        {
            $url = $this->request->getBaseUrl();
            header("Location: $url/search");
            die;
        }
        $total = StatService::getBykey('topic_total');
        $total = empty($total) ? 0 : $total['meta_value'];
        $per = 10;
        
        $list = TopicService::getHomeArticles($page, $per, 'publish_time', 'DESC');
        if (!empty($list))
        {
            //取标签
            $topicIdList = array_column($list, 'id');
            $tagList = TagService::getByTopicIdList(array_unique($topicIdList));
            foreach ($list as &$value)
            {
                if (isset($tagList[$value['id']]))
                {
                    $value['tagList'] = $tagList[$value['id']];
                }
                else 
                {
                    $value['tagList'] = [];
                }
            }
        }
        
//         $newest = TopicService::getNewest(5);
        return $this->display('index/index.php', [
            'list' => $list, 
//             'newest' => $newest,
//             'tagTotal' => TagService::getTotal(),
//             'topicTotal' => $total,
            'pagination' => getPagination($total, $page),
        ]);
    }
    
    public function search()
    {
        $page = $this->request->getParam('page');
        $page = empty($page) || !ctype_digit($page) ? 1 : $page;
        $size = 10;
        $offset = ($page - 1) * $size;
        $keyword = trim($this->request->getParam('keyword', ''));
        $orderby = $this->request->getParam('orderby');
        $order = $this->request->getParam('order');
        $total = 0;
        $costTime = 0;
        if (!in_array($orderby, ['1', '2', '3']))
        {
            $orderby = '1';
        }
        if (in_array($order, ['1', '2']))
        {
            $orderText = $order === '1' ? 'DESC' : 'ASC';
        }
        else 
        {
            $order = '1';
            $orderText = 'DESC';
        }
        if (empty($keyword))
        {
            $data = '请输入关键字来搜索';
            goto A;
        }
//         dump($orderby, $orderText, $keyword, $offset, $page);
        $sphinx = app('sphinx');
        $sphinx->setConnectTimeout(3);
        $sphinx->setServer('120.24.175.25', 9312);
//         $sphinx->setMatchMode(SPH_MATCH_PHRASE);//整个查询看作一个词组
        $sphinx->setMaxQueryTime(5000);//最大查询时间，单位微秒
        switch ($orderby)
        {
            case '1':
                //相关性
                $sphinx->setSortMode(SPH_SORT_EXTENDED, "@weight $orderText");
                break;
            case '2':
                //发表时间
                $sphinx->setSortMode(SPH_SORT_EXTENDED, "publish_time $orderText");
                break;
            case '3':
                //更新时间
                $sphinx->setSortMode(SPH_SORT_EXTENDED, "update_time $orderText");
                break;
            default:
                //相关性
                $sphinx->setSortMode(SPH_SORT_EXTENDED, "@weight $orderText");
        }
        $sphinx->setLimits($offset, $size, 50000000);
//         $sphinx->setFilter('status', [1]);
        $result = $sphinx->query($keyword, 'test1');
        if ($result === false || !empty($result['error']) || !empty($result['warning']))
        {
            //出错
            $total = 0;
            $data = sprintf("error: %s, warning: %s", $sphinx->getLastError(), $sphinx->getLastWarning());
        }
        else 
        {
            $total = $result['total'];
            $costTime = $result['time'];
            if ($total == 0)
            {
                $data = "关键字：'$keyword' 没有结果，换一个试试吧。";
            }
            else
            {
                $idList = array_keys($result['matches']);
                $list = TopicService::getByIdList($idList);
                if (empty($list))
                {
                    $total = 0;
                    $data = "从Mysql搜索结果为空！" . print_r($idList);
                }
                else
                {
                    $list = array_column($list, null, 'id');
                    $data = [];
                    foreach ($idList as $id)
                    {
                        $item = $list[$id];
                        $item['title'] = str_replace($keyword, "<span style=\"color: red;font-weight: bold\">$keyword</span>", $item['title']);
                        $data[] = $item;//保持排序
                    }
                }
            }
        }
//         dump($result);
        A:
        $all = StatService::getBykey('topic_total');
        return $this->display('index/search.php', [
            'keyword' => $keyword,
            'total' => $total,
            'costTime' => $costTime,
            'all' => $all['meta_value'],
            'order' => $order,
            'orderby' => $orderby,
            'data' => $data,
            'pagination' => getPagination($total, $page),
        ]);   
    }
    
    public function tag($tagName)
    {
        $tagInfo = TagService::getByName(urldecode($tagName));
        
        if (empty($tagInfo))
        {
            $data = "标签：{$tagName}不存在";
        }
        else 
        {
            $tagId = $tagInfo['id'];
            $page = $this->request->getParam('page');
            $page = empty($page) || !ctype_digit($page) ? 1 : intval($page);
            $size = 10;
            $offset = ($page - 1) * $size;
            $total = TopicService::getCountsByTagId($tagId);
            if ($total == 0)
            {
                $data = "没有结果";
            }
            else 
            {
                $list = TopicService::getListByTagId($tagId, "$offset, $size");
                if (is_array($list))
                {
                    //取标签
                    $topicIdList = array_column($list, 'id');
                    $tagList = TagService::getByTopicIdList(array_unique($topicIdList));
                    foreach ($list as &$value)
                    {
                        if (isset($tagList[$value['id']]))
                        {
                            $value['tagList'] = $tagList[$value['id']];
                        }
                        else
                        {
                            $value['tagList'] = [];
                        }
                    }
                    $data = $list;
                }
                else
                {
                    $data = "获取出错";
                }
            }
        }
        return $this->display('index/archive-tag.php', [
            'tagInfo' => $tagInfo,
            'total' => $total,
            'list' => $data,
            'pagination' => getPagination($total, $page, $size),
        ]);
    }
    
    public function detail($id)
    {
        if (empty($id) || !ctype_digit($id))
        {
            return $this->showInvalidParam();
        }
        
        $topicInfo = TopicService::getById($id);
        if (empty($topicInfo))
        {
            return $this->show404NotFound();
        }
        
        $topicDetail = TopicService::getDetail($id);
        if (empty($topicDetail))
        {
            $topicInfo['content'] = '还没有内容哟~.~';
        }
        else
        {
            $topicInfo['content'] = $topicDetail['content'];
        }
        
        $topicTags = TopicService::getTags($id);
        $topicInfo['tagList'] = $topicTags;
        
        return $this->display('index/detail.php', [
            'article' => $topicInfo,
        ]);
    }
}