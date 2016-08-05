<?php
namespace providers;

use framework\Providers\Provider;

class SwooleClientProvider extends Provider
{
    private static $instance = null;
    
    public function register()
    {
        $self = $this;
        $this->app['swooleClient'] = function() use ($self) {
            return $self->getInstance();
        };
    }
    
    private function getInstance()
    {
        if (self::$instance !== null)
        {
            return self::$instance;
        }
        if (!class_exists('swoole_client'))
        {
            throw new \RuntimeException('class swoole_client is not defined');
        }
        $client = new swoole_client();
        $client->connect('127.0.0.1', 9501, 1);
    
        return self::$instance = $client;
    }
}