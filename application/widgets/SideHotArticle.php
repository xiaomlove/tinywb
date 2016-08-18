<?php
namespace widgets;

use framework\Widget;
use services\TopicService;

class SideHotArticle extends Widget
{
    public function run()
    {
        $list = TopicService::getHotArticles(5);
        return $this->render('sideHotArticle.php', ['list' => $list]);
    }
}