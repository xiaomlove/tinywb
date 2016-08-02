<?php
namespace widgets;

use framework\Widget;

class SideHotTag extends Widget
{
    public function run()
    {
        return $this->render('sideHotTag.php');
    }
}