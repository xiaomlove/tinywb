<?php
namespace framework\providers;

use framework\Route;

class RouteProvider extends Provider
{
    public function register()
    {
        $this->app['route'] = function() {
            return Route::getInstance();
        };
    }
}