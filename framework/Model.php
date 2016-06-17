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
    
    /**
     * 插入数据
     * @param string $table 数据表名称 ，如test
     * @param array $fields 字段数组， 一维数组，如['name', 'age', 'sex']
     * @param array $datas  值数组，二维数组，如[['小明', 21, '男'], ['小红', 18, '女']]
     */
    public function insert($table, array $fieldData)
    {
        return $this->getDb()->insert($table, $fieldData);
    }
    
    public function lastSql()
    {
        return $this->getDb()->lastSql();
    }
    
    public function allSql()
    {
        return $this->getDb()->allSql();
    }
    
    
}