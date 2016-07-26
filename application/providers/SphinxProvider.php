<?php
namespace providers;

use framework\Providers\Provider;

class SphinxProvider extends Provider
{
    private static $instance = null;
        
    public function register()
    {
        $self = $this;
        $this->app['sphinx'] = function() use ($self) {
            return $self->getInstance();
        };
    }
    
    private function getInstance()
    {
        if (self::$instance !== null)
        {
            return self::$instance;
        }
        if (!class_exists('\SphinxClient'))
        {
            throw new \RuntimeException("Sphinx is not loaded");
        }
        $sphinx = new \SphinxClient();
        
        return self::$instance = $sphinx;
    }
}