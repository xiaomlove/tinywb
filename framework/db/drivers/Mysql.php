<?php

namespace framework\db\drivers;

use framework\db\DbInterface;
use framework\Config;

class Mysql implements DbInterface
{
    private $pdo;
    
    private $stat;
    
    public function __construct($dsn, $user = '', $password = '')
    {
        if (!class_exists('\PDO')) {
            throw new \RuntimeException('class \PDO is not exists.');
        }
        $config = Config::get('db');
        if (empty($config['dsh'])) {
            throw new \RuntimeException('no set config: db.dsh.');
        }
        $dsn = $config['dsn'];
        $user = empty($config['user']) ? '' : $config['user'];
        $password = empty($config['password']) ? '' : $config['password'];
        try {
            $this->pdo = new \PDO($dsn, $user, $password, [
                \PDO::ATTR_ERRMODE=>\PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_TIMEOUT=>1,
            ]);
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }
    
    /**
     * 总的执行所有SQL。
     * @param unknown $sql
     * @param array $binds
     */
    private function execute($sql, array $binds = []);
    
    /**
     * 执行除SELECT外的语句，如UPDATE/DELETE/INSERT
     * @param unknown $sql
     * @param array $binds
    */
    public function exec($sql, array $binds = []);
    
    
    /**
     * 执行SELECT，获取一行
     * @param unknown $sql
     * @param array $binds
    */
    public function fetch($sql, array $binds = []);
    
    /**
     * 执行SELECT，获取所有行
     * @param unknown $sql
     * @param array $binds
    */
    public function fetchAll($sql, array $binds = []);
    
    /**
     * 执行SELECT，获取一列
     * @param unknown $sql
     * @param array $binds
    */
    public function fetchColumn($sql, array $binds = []);
    
    
    /**
     * 插入数据
     * @param unknown $table
     * @param array $arrSets
    */
    public function insert($table, array $arrSets);
    
    
    /**
     * 更新数据
     * @param unknown $table
     * @param array $arrSets
     * @param unknown $whereStr
    */
    public function update($table, array $arrSets, $whereStr);
    
    /**
     * 删除数据
     * @param unknown $table
     * @param unknown $whereStr
    */
    public function delete($table, $whereStr);
    
    /**
     * 返回最后插入的ID
    */
    public function lastInsertId();
    
    /**
     * 最后执行的SQL语句
    */
    public function lastSql();
    
    /**
     * 所有执行过的SQL
    */
    public function allSql();
    
    
    public function beginTransaction();
    
    public function commit();
    
    public function rollBask();
}