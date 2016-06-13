<?php
namespace framework;

use framework\db\drivers\Mysql;

class Model
{
    private $db;
    
    private $policy;
    
    public function getDb()
    {
        if (is_null($this->db)) {
            
        }
        return $this->db;
    }
    
    public function __construct($policy)
    {
        $this->policy = $policy;
    }
    
    
}