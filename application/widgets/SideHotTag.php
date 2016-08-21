<?php
namespace widgets;

use framework\Widget;
use services\TopService;

class SideHotTag extends Widget
{
    public function run()
    {
        $list = TopService::getHotTags(20);
        return $this->render('sideHotTag.php', ['list' => $list]);
    }
}