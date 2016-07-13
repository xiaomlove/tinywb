<?php
/**
* @desc 分页类，类似WordPress。
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年7月12日    下午10:34:34
*/

namespace framework\utils;

class Pagination2
{
    //默认参数
    private $defaults = [
        'baseUrl' => '',//基础URL，不含query_string
        'pageParam' => 'page',//页码形参
        'total' => 2,//总页数
        'current' => 1,//当前页数
        'sideSize' => 1,//第一和最后一页旁边页数
        'centerSize' => 1,//当前页两边页数
        'type' => 'html',//返回格式，有html的array
        'parameters' => [],//额外参数键值
        'fragment' => '',//锚点
        'before' => '',//在第一个页码前边插入一段字符串
        'after' => '',//在最后一个页码后插入一段字符串
        'addDot' => true,//是否添加...
        'mainClass' => 'pagination',//页码容器(如ul)的类名，
        'itemClass' => 'page-item',//页码项目(如li)的类史
        'linkClass' => 'page-link',//链接上(a标签)的类名
        'disabledClass' => 'disabled',//不可用页码上的类名
        'activeClass' => 'active',//当前大页码的类名
        'nextText' => '下一页',//下一页的文字
        'prevText' => '上一页',//上一页的文字
        'firstText' => '第一页',//第一页的文字
        'lastText' => '最末页',//最后一页的文字
    ];
    
    private $options = [];
    
    private $centerBegin;
    
    private $centerEnd;
    
    public function __construct(array $parameters = [])
    {
        $options = array_merge($this->defaults, $parameters);
        if ($options['current'] > $options['total'])
        {
            $options['current'] = $this->defaults['current'];
        }
        $this->options = $options;
    }
    
    private function getBaseUrl()
    {
        $parameters = $this->options['parameters'];
        $requestString = $_SERVER['QUERY_STRING'];
        parse_str($requestString, $requestArr);
        $args = array_merge($requestArr, $parameters);
        unset($args[$this->options['pageParam']]);
        $result = http_build_query($args);
        if (empty($result))
        {
            return sprintf(
                "%s?%s=", 
                $this->options['baseUrl'],
                $this->options['pageParam']
            );
        }
        else
        {
            return sprintf(
                "%s?%s&%s=", 
                $this->options['baseUrl'],
                $result,
                $this->options['pageParam']
            );
        }
    }
    
    public static function create(array $parameters = [])
    {
        return (new self($parameters))->make();
    }
    
    private function createPageItem()
    {
        return [
            'text' => '',
            'url' => '',
            'page' => '',
            'isActive' => false,
            'isDisabled' => false,
        ];
    }
    
    public function getPageItemFirst()
    {
        $page = $this->createPageItem();
        if ($this->options['current'] == 1)
        {
            $page['isActive'] = true;
        }
        $page['page'] = 1;
        $page['text'] = $this->options['firstText'];
        return $page;
    }
    
    public function getPageItemPrev()
    {
        $page = $this->createPageItem();
        if ($this->options['current'] == 1)
        {
            $page['isDisabled'] = true;
        }
        else
        {
            $page['page'] = intval($this->options['current']) - 1;
        }
        $page['text'] = $this->options['nextText'];
        return $page;
    }
    
    public function getPageItemLast()
    {
        $page = $this->createPageItem();
        if ($this->options['current'] == $this->options['total'])
        {
            $page['isActive'] = true;
        }
        $page['page'] = intval($this->options['total']);
        $page['text'] = $this->options['lastText'];
        return $page;
    }
    
    
    public function getPageItemNext()
    {
        $page = $this->createPageItem();
        if ($this->options['current'] == $this->options['total'])
        {
            $page['isDisabled'] = true;
        }
        else
        {
            $page['page'] = intval($this->options['current']) + 1;
        }
        $page['text'] = $this->options['nextText'];
        return $page;
    }
    
    public function getPageItemCenter()
    {
        $begin = $this->options['current'] - $this->options['centerSize'];
        $end = $this->options['current'] + $this->options['centerSize'];
        $begin = $this->centerBegin = max(2, $begin);
        $end = $this->centerEnd = min($this->options['total'] - 1, $end);
        $result = [];
        for ($i = $begin; $i <= $end; $i++)
        {
            $page = $this->createPageItem();
            if ($this->options['current'] == $i)
            {
                $page['isActive'] = true;
            }
            $page['page'] = $i;
            $page['text'] = $i;
            $result[] = $page;
        }
        return $result;
    }
    
    public function getPageItemSideLeft()
    {
        $begin = 2;
        $end = 2 + $this->options['sideSize'];
        $end = min($end, $this->centerBegin);
        $result = [];
        for ($i = $begin; $i <= $end; $i++)
        {
            $page = $this->createPageItem();
            if ($this->options['current'] == $i)
            {
                $page['isActive'] = true;
            }
            $page['page']= $i;
            $page['text'] = $i;
            $result[] = $page;
        }
        return $result;
    }
    
    public function getPageItemSideRight()
    {
        $begin = $this->options['total'] - $this->options['sideSize'];
        $begin = max($begin, $this->centerEnd);
        $end = $this->options['total'] - 1;
        $result = [];
        for ($i = $begin; $i <= $end; $i++)
        {
            $page = $this->createPageItem();
            if ($this->options['current'] == $i)
            {
                $page['isActive'] = true;
            }
            $page['page']= $i;
            $page['text'] = $i;
            $result[] = $page;
        }
        return $result;
    }
    
    private function buildPageItems()
    {
        if ($this->options['total'] < 2)
        {
            return array();
        }
        $baseUrl = $this->getBaseUrl();
        
    }
    
    private function formatPageItems(array $pageItems)
    {
        
    }
    
    public function make()
    {
        $pageItems = $this->buildPageItems();
        switch ($this->options['type'])
        {
            case 'html':
                
        }
    }
    
}