<?php
namespace framework\providers;

use framework\Event;

class EventProvider extends Provider
{
    public function register()
    {
        $this->app['event'] = function() {
            return Event::getInstance();
        };
    }
}