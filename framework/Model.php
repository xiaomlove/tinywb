<?php
namespace framework;

use framework\db\drivers\Mysql;
use framework\exceptions\SQLException;

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
    
    public function execute($sql, array $binds = [])
    {
        return $this->getDb()->execute($sql, $binds);
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
     * @param array $fieldData 数据，单条一维索引数组，多条二维索引数组，如
     * [
     *    ['name' => '小明', 'age' => 18, 'sex' => '男'],
     *    ['name' => '小红', 'age' => 20, 'sex' => '女'],
     * ]
     */
    public function insert($table, array $fieldData)
    {
        if (count($fieldData) === count($fieldData, true)) {
            $fieldData = array($fieldData);
        }
        $fields = array_keys($fieldData[0]);
        $fieldsCount = count($fields);
        $fieldsStr = '(';
        foreach ($fields as $field)
        {
            $fieldsStr .= "`{$field}`,";
        }
        $fieldsStr = rtrim($fieldsStr, ',') . ')';
        unset($field);
        
        $sql = "INSERT INTO $table $fieldsStr VALUES ";
        $binds = [];
        foreach ($fieldData as $k => $row) {
            if (($currCount = count($row)) !== $fieldsCount) {
                throw new \InvalidArgumentException("row $k field number: $currCount not match row 1, it's number is $fieldsCount");
            }
            $sql .= '(';
            foreach ($fields as $field) {
                if (!isset($row[$field])) {
                    throw new \InvalidArgumentException("row $k do not have field: '$field', " . print_r($row, true));
                }
                $sql .= '?,';
                $binds[] = $row[$field];
            }
            $sql = rtrim($sql, ','). '),';
        }
        $sql = rtrim($sql, ',');
        try {
            return $this->getDb()->execute($sql, $binds);
        } catch (\PDOException $e) {
            throw new SQLException(
                $e->getCode(),
                $e->getMessage(),
                $sql,
                $binds
            );
        }
    }
    
    public function delete($table, array $where, array $binds = [])
    {
        if (empty($where)) {
            return false;
        }
        $sql = "DELETE FROM `$table` ". $this->formatWhere($where);
        return $this->getDb()->execute($sql, $binds);
    }
    
    public function lastSql()
    {
        return $this->getDb()->lastSql();
    }
    
    public function allSql()
    {
        return $this->getDb()->allSql();
    }
    
    /**
     * 格式化where，只支持AND相连。键是字段名，值若是字符串直接为值且连接符为=，若是数组，第一个是值，第二个是连接符
     * 
     * 要使用OR或者括号等复杂条件，手动拼接好直接execute.
     * 
     * @param array $where, 如['type' => 1, 'age' => [18, '>'], 'name' => ['小%', 'LIKE'], 'uid' => ['(1,2,4)', 'IN']]
     *                      最终拼成  type=1 AND age > 18 AND name LIKE '小%' AND uid IN (1,2,3)
     * @throws \InvalidArgumentException
     * @return string
     */
    private function formatWhere(array $where)
    {
        if (empty($where)) {
            return '';
        }
        $outStr = "WHERE";
        foreach ($where as $field => $option) {
            $option = (array)$option;
            $argsCount = count($option);
            switch ($argsCount) {
                case 1:
                    $outStr .= " `{$field}` = {$option[0]}";
                    break;
                case 2:
                    $outStr .= " `{$field}` {$option[1]} {$option[0]}";
                    break;
                default:
                    throw new \InvalidArgumentException("parameter format error: " . print_r($option, true));
            }
            $outStr .= " AND";
        }
        return rtrim($outStr, ' AND');
    }
    
}