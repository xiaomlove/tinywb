<?php
/**
* @desc 模型基类
*       一般来说，一个数据表对应一个模型。
* @author xiaomlove
* @link http://xiaomlove.com
* @time 2016年6月19日    下午5:41:20
*/

namespace framework;

use framework\db\drivers\Mysql;

abstract class Model
{
    //一个策略ID(就想象成数据库名)创建一个数据库连接对象，同一个库的表的ID是一样的。
    //以策略ID为键存储起来所有数据库连接
    private static $dbConnections = [];
    
    //以模型完整类名存储起来所有模型对象，模型对象是单例，不能直接new，通过静态方法model()获得对象
    private static $models = [];
    
    public function getDb()
    {
        $policyId = static::policyId();
        if (empty($policyId) || !is_string($policyId)) {
            throw new \RuntimeException("policyId() function don't return a valid policyId in " . get_called_class());
        }
        if (isset(self::$dbConnections[$policyId])) {
            return self::$dbConnections[$policyId];
        }
        $policy = Config::get('db.' . $policyId);
        if (empty($policy)) {
            throw new \InvalidArgumentException("Invalid policyId: $policyId");
        }
        $mysql = new Mysql($policy);
        self::$dbConnections[$policyId] = $mysql;
        return $mysql;
    }
    
    public function getDbConnections()
    {
        return self::$dbConnections;
    }
    
    public function getModels()
    {
        return self::$models;
    }
    
    final private function __construct()
    {
        
    }
    
    final private function __clone()
    {
        
    }
    
    final public static function model()
    {
        $className = get_called_class();
        if (isset(self::$models[$className])) {
            return self::$models[$className];
        }
        $model = new $className();
        self::$models[$className] = $model;
        return $model;
    }
    
    abstract function policyId();
    
    abstract function tableName();
    
    private function getTableName($tableName)
    {
        if (!empty($tableName)) {
            return $tableName;
        } else {
            $tableName = static::tableName();
            if (!empty($tableName) && is_string($tableName)) {
                return $tableName;
            } else {
                throw new \RuntimeException("no pass parameter tableName or function tableName() no return valid string in " . get_called_class());
            }
        }
    }
    
    public function execute($sql, array $binds = [])
    {
        return $this->getDb()->execute($sql, $binds);
    }
    
    public function exec($sql)
    {
        return $this->getDb()->exec($sql);
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
        
        $sql = "INSERT INTO `$table` $fieldsStr VALUES ";
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
        return $this->getDb()->execute($sql, $binds);
    }
    
    public function lastInsertId()
    {
        return $this->getDb()->lastInsertId();
    }
    
    public function delete($table, array $where, array $binds = [])
    {
        if (empty($where)) {
            return false;
        }
        $sql = "DELETE FROM `$table`". $this->formatWhere($where);
        return $this->getDb()->execute($sql, $binds);
    }
    
    public function update($table, array $fieldData, array $where, array $binds = [])
    {
        if (empty($fieldData) || empty($where)) {
            return false;
        }
        $sql = "UPDATE `$table` SET ";
        foreach ($fieldData as $field => $value) {
            $sql .= "`$field` = $value, ";
        }
        $sql = rtrim($sql, ', ');
        $sql .= $this->formatWhere($where);
        return $this->getDb()->execute($sql, $binds);
    }
    
    /**
     * 简单查询，需要GROUP BY等复杂查询直接接语句execute.
     * @param string $table
     * @param array $where, 见formatWher()
     * @param string|array $fields
     * @param string $order
     * @param string $limit
     * @param array $binds
     * @return array|false
     */
    public function select($table, $fields = '*', array $where = [],  $order = '', $limit = '', array $binds = [])
    {
        $fields = is_array($fields) ? implode(',', $fields) : $fields;
        $sql = "SELECT $fields FROM `$table`";
        if (!empty($where)) {
            $sql .= $this->formatWhere($where);
        }
        if (!empty($order)) {
            $sql .= " ORDER BY $order";
        }
        if (!empty($limit)) {
            $sql .= " LIMIT $limit";
        }
        
        return $this->getDb()->fetchAll($sql, $binds);
    }
    
    public function selectOne($table = '', $fields = '*', array $where = [], $order = '', array $binds = [])
    {
        $table = $this->getTableName($table);
        $fields = is_array($fields) ? implode(',', $fields) : $fields;
        $sql = "SELECT $fields FROM `$table`";
        if (!empty($where)) {
            $sql .= $this->formatWhere($where);
        }
        return $this->getDb()->fetch($sql, $binds);
    }
    
    public function count($table, array $where, $field = '*', array $binds = [])
    {
        $sql = "SELECT count($field) as counts FROM `$table`" . $this->formatWhere($where) . " LIMIT 1";
        $result = $this->getDb()->fetch($sql, $binds);
        return empty($result) ? 0 : $result['counts'];
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
        $outStr = " WHERE";
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
    
    public function beginTransaction()
    {
        return $this->getDb()->beginTransaction();
    }
    
    public function commit()
    {
        return $this->getDb()->commit();
    }
    
    public function rollBack()
    {
        return $this->getDb()->rollBack();
    }
}