<?php
namespace framework\providers;

use framework\Autoload;

class AutoloadProvider extends Provider
{
    public function register()
    {
        $this->app['autoload'] = function() {
            return Autoload::getInstance();
        };
    }
}