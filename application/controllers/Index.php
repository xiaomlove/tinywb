<?php
namespace controllers;

use services\TopicService;
use services\StatService;
use services\TagService;
use providers\AsyncTaskProvider;
use framework\Validator;
use framework\db\DB;

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
        
        $list = TopicService::getHomeArticles($page, $per, 'publish_time DESC');
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
        $data = [];
        $total = 0;
        $page = 1;
        $size = 10;
        
        $tagInfo = TagService::getByName(urldecode($tagName));
        if (empty($tagInfo))
        {
            $data = "标签：{$tagName}不存在";
            goto A;
        }
        //添加异步任务--增加标签热度
        app('asyncTask')->addTask(AsyncTaskProvider::TASK_INCREASE_TAG_HEAT_BY_VIEW_TAG, [$tagInfo['id']]);
        
        
        $tagId = $tagInfo['id'];
        $page = $this->request->getParam('page');
        $page = empty($page) || !ctype_digit($page) ? 1 : intval($page);
       
        $offset = ($page - 1) * $size;
        
        //处理数量
        if ($tagInfo['counts'] == 0)
        {
            //获取真实数据
            $total = TopicService::getCountsByTagId($tagId);
            if (empty($total))
            {
                $data = "没有结果";
            }
            //没数量时，赶紧统计一下
            app('asyncTask')->addTask(AsyncTaskProvider::TASK_UPDATE_TAG_TOPIC_COUNTS, [$tagId], 'low');
        }
        else 
        {
            $total = $tagInfo['counts'];
            //有数据时，一定概率统计一下
            if (mt_rand(1, 10) === 5)
            {
                app('asyncTask')->addTask(AsyncTaskProvider::TASK_UPDATE_TAG_TOPIC_COUNTS, [$tagId], 'low');
            }
        }
        
        //处理列表
        if ($total)
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
        
        A:
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
        $editLink = '<a href="' . url('controllers\Index@edit', ['id' => $id]) . '">[编辑]</a>';
        if (empty($topicDetail))
        {
            $topicInfo['content'] = '还没有内容哟~.~' . $editLink;
        }
        else
        {
            $topicInfo['content'] = $topicDetail . $editLink;
        }
        
        $topicTags = TopicService::getTags($id);
        $topicInfo['tagList'] = $topicTags;
        
        $pv = TopicService::getPv($id);
        $topicInfo['pv'] = $pv;
        
        //添加异步任务--统计PV
        $asyncTask = app('asyncTask');
        $addTaskResult = $asyncTask->addTask(AsyncTaskProvider::TASK_INCREASE_TOPIC_PV, [$id]);
        
        //添加异步任务--增加标签热度
        if (!empty($topicTags))
        {
            $tagIdArr = array_column($topicTags, 'id');
            $addTaskResult = $asyncTask->addTask(AsyncTaskProvider::TASK_INCREASE_TAG_HEAT_BY_VIEW_TOPIC, $tagIdArr);
        }
        
        
        return $this->display('index/detail.php', [
            'article' => $topicInfo,
            'addTaskResult' => $addTaskResult,
        ]);
    }
    
    public function console()
    {
        return $this->display('index/console.php');
    }
    
    public function consoleSubmit()
    {
        $code = $this->request->getPost('code');
        $code = preg_replace('/<\?php|\?>|[\r\n\t]+/', '', $code);
        $errSet = 'error_reporting(E_ALL);ini_set(\'display_errors\', 1);';
        $code = $errSet . 'try{' . $code . '}catch(\Exception $e){die($e->getMessage() . \'ss\');}';
        $code .= 'finally{$err=error_get_last();if(!empty($err)){print_r($err);}}'; 
//         var_dump($code);die;
        ob_start();
        $result = eval($code);
        $content = ob_get_clean();
        if (empty($result))
        {
            $out = $content;
        }
        else 
        {
            $out = $result;
        }
        var_dump($out);die;
    }
    
    public function edit($id)
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
        
        $topicTags = TopicService::getTags($id);
        
        $topicInfo['detail'] = $topicDetail;
        $topicInfo['tags'] = $topicTags;
        
        return $this->display('index/edit.php', [
            'info' => $topicInfo, 
        ]);
    }
    
    public function editSubmit($id)
    {
        if (empty($id) || !ctype_digit($id))
        {
            return $this->showInvalidParam();
        }
        $data = $this->request->getParam();
        $validator = Validator::make($data, [
            'title' => 'required|max_length:20|min_length:5',
            'detail' => 'required|max_length:100|min_length:10',
            'title' => 'regular:/^[\d]+$/',
        ],[
            'required' => ':attr不能少的哟亲',
            'max_length' => '太长了，:attr最多只能是:target个字符',
            'min_length' => '太短了，:attr最少都得:target个字符',
        ], [
            'title' => '标题',
            'detail' => '内容'
        ], 2);
        
        var_dump($validator->getError());

        die;
        list($err, $result) = TopicService::update($id, $data);
        if (!is_null($err))
        {
            dump($err);
        }
        else 
        {
            dump('ok');
        }
    }
}