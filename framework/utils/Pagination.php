<?php
/**
* @desc 分页类
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年7月7日    上午12:00:19
*/

namespace framework\utils;

class Pagination
{
    public $mainClass = 'pagination';//ul的class
     
    public $itemClass = '';//li的class
     
    public $prevText = '上一页';//上一页的文字
     
    public $prevClass = '';//上一页的class
     
    public $nextText = '下一页';//下一页的文字
     
    public $nextClass = '';//下一页的class
     
    public $activeClass = 'active';//当前页面class
     
    public $disabledClass = 'disabled';//不能点页面class
     
    public $url = '';//页面 基本的url
     
    public $p = 'page';//分页参数名
     
    public $showPageCount = 10;//最多显示10页，超过显示...
     
    public $prepend = '';//最左边插入的内容，是一个li
     
    public $append = '';//最右边插入的内容，是一个li
     
    public $addDot = TRUE;//是否添加...
     
    private $page;//当前页
     
    private $total;//总页数
     
    private $params = array();//传递的参数
     
    public function __construct($page, $total, array $params = array())
    {
        $this->page = $page;
        $this->total = $total;
        $this->params = empty($params) ? $_GET : $params;
    }
     
    /**
     * 获得基本的URL，包含要传递的参数，去掉分页参数
     */
    private function getBaseURL()
    {
        $URL = $this->url;
        $params = $this->params;
        unset($params[$this->p]);
        $queryString = http_build_query($params);
        if (empty($queryString))
        {
            return $URL.'?'.$this->p.'=';
        }
        else
        {
            return $URL.'?'.$queryString.'&'.$this->p.'=';
        }
    }
     
    public function show()
    {
        $HTML = '<ul class="'.$this->mainClass.'">';
        $hasPend = FALSE;
        if (!empty($this->prepend))
        {
            $hasPend = TRUE;
            $HTML .= $this->prepend;
        }
         
        if ($this->total <= 1)
        {
            if (!empty($this->append))
            {
                $hasPend = TRUE;
                $HTML .= $this->append;
            }
            if ($hasPend)
            {
                return $HTML.'</ul>';
            }
            else
            {
                return '';
            }
        }
        $startDot = $endDot = '';
        $max = $this->showPageCount;
        $total = $this->total;
        $d = floor(($max - 2)/2);//中位数
        $page = $this->page;//当前页
        $dot = $this->addDot ? '...' : '';
        $url = $this->getBaseUrl();
        $isOdd = $max%2 == 1 ? TRUE : FALSE;//是否奇数，偶数数两端有...时得减小某端的一个页码，这里取左边
        //拼凑上一页与第一页。上一页、第一页、最末页、下一页是固定的。没有...之类
        if ($page == 1)
        {
            $HTML .= '<li class="'.$this->disabledClass.' '.$this->prevClass.'"><a><span aria-hidden="true">'.$this->prevText.'</span></a></li>';
            $HTML .= '<li class="'.$this->activeClass.' '.$this->itemClass.'"><a>1</a></li>';
        }
        else
        {
            $HTML .= '<li class="'.$this->prevClass.'"><a href="'.$url.($page-1).'"><span aria-hidden="true">'.$this->prevText.'</span></a></li>';
            $HTML .= '<li class="'.$this->itemClass.'"><a href="'.$url.'1">1</a></li>';
        }
        //准备循环起始点
        if ($total <= $max)
        {
            $start = 2;
            $end = $total - 1;
        }
        else
        {
            if ($page - $d <= 2)
            {
                $start = 2;
                $end = $max - 1;
                $endDot = $dot;
            }
            else
            {
                if ($page + $d >= $total - 1)
                {
                    $start = $total - $max + 2;
                    $end = $total - 1;
                    $startDot = $dot;
                }
                else
                {
                    $start = $isOdd ? ($page - $d) : ($page - $d + 1);
                    $end = $page + $d;
                    $startDot = $dot;
                    $endDot = $dot;
                }
            }
        }
         
        for ($i = $start; $i <= $end; $i++)
        {
            $active = '';
            if ($i == $page)
            {
                $active = $this->activeClass;
            }
            $showText = $i;
             
            if ($i == $start)
            {
                $showText = $startDot.$showText;
            }
            if ($i == $end)
            {
                $showText = $showText.$endDot;
            }
             
            $HTML .= '<li class="'.trim($this->itemClass.' '.$active).'"><a href="'.$url.$i.'">'.$showText.'</a></li>';
        }
         
        //拼凑最末页和下一页
        if ($page == $total)
        {
            $HTML .= '<li class="'.$this->itemClass.' '.$this->activeClass.'"><a>'.$total.'</a></li>';
            $HTML .= '<li class="'.$this->nextClass.' '.$this->disabledClass.'"><a><span aria-hidden="true">'.$this->nextText.'</span></a></li>';
        }
        else
        {
            $HTML .= '<li class="'.$this->itemClass.'"><a href="'.$url.$total.'">'.$total.'</a></li>';
            $HTML .= '<li class="'.$this->nextClass.'"><a href="'.$url.($page + 1).'"><span aria-hidden="true">'.$this->nextText.'</span></a></li>';
        }
        if (!empty($this->append))
        {
            $HTML .= $this->append;
        }
        $HTML .= '</ul>';
        return $HTML;
    }
}