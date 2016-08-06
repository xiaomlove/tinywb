<?php
namespace providers;

use framework\Providers\Provider;

class GearmanClientProvider extends Provider
{
    private static $instance = null;
    
    public function register()
    {
        $self = $this;
        $this->app['gearmanClient'] = function() use ($self) {
            return $self->getInstance();
        };
    }
    
    private function getInstance()
    {
        if (self::$instance !== null)
        {
            return self::$instance;
        }
        if (!class_exists('\GearmanClient'))
        {
            throw new \RuntimeException('\GearmanClient is not defined');
        }
        $client = new \GearmanClient();
        $client->addServer('127.0.0.1', 4730);
    
        return self::$instance = $client;
    }
}