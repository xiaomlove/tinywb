<?php
namespace cli;

use framework\Cli;
use services\TopService;
use services\TopicService;


class Top implements Cli
{
    public static function getInstance()
    {
        return new self;
    }
    
    public static function updateHotTags()
    {
        return TopService::updateHotTags();
    }
    
}