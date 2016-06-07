<?php
namespace framework\providers;

use framework\Config;

class ConfigProvider extends Provider
{
    public function register()
    {
        $this->app['config'] = function() {
            return Config::getInstance();  
        };
    }
}

