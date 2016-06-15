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
            $this->db = new Mysql($this->policy);
        }
        return $this->db;
    }
    
    public function __construct($policyId = 'default')
    {
        $policy = Config::get('db.' . $policyId);
        if (empty($policy)) {
            throw new \InvalidArgumentException("Invalid policyId: $policyId");
        }
        $this->policy = $policy;
    }
    
    
}