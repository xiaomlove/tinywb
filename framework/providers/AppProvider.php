<?php
namespace framework\providers;

use framework\App;

class AppProvider extends Provider
{
    public function register()
    {
        $this->app['app'] = function() {
            return App::getInstance();
        };
    }
}