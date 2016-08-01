<?php
namespace widgets;

use framework\Widget;
use services\TagService;
use services\StatService;

class SideDataStat extends Widget
{
    public function run()
    {
        $tagTotal = TagService::getTotal();
        $topicTotal = StatService::getBykey('topic_total');
        $topicTotal = empty($topicTotal) ? 0 : $topicTotal['meta_value'];
        return $this->render('sideDataStat.php', ['tagTotal' => $tagTotal, 'topicTotal' => $topicTotal]);
    }
}