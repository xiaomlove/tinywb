<?php
namespace widgets;

use framework\Widget;

class SideHotArticle extends Widget
{
    public function run()
    {
        return $this->render('sideHotArticle.php');
    }
}