<?php
namespace widgets;

use framework\Widget;
use services\TopicService;

class NewestArticle extends Widget
{
    public function run()
    {
        $newest = TopicService::getNewest(5);
        return $this->render('newestArticle.php', ['newest' => $newest]);
    }
}