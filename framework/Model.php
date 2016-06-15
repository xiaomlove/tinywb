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
    
    public function fetch($sql, array $binds = [], $fetcyStyle = \PDO::FETCH_ASSOC)
    {
        return $this->getDb()->fetch($sql, $binds, $fetcyStyle);
    }
    
    public function fetchAll($sql, array $binds = [], $fetcyStyle = \PDO::FETCH_ASSOC)
    {
        return $this->getDb()->fetchAll($sql, $binds, $fetcyStyle);
    }
    
    public function fetchColumn($sql, array $binds = [])
    {
        return $this->getDb()->fetchColumn($sql, $binds);
    }
    
    public function lastSql()
    {
        return $this->getDb()->lastSql();
    }
    
    
}