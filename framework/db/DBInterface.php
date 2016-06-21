<?php

namespace framework\db;

interface DBInterface 
{
    
    public function exec($sql);
    
    /**
     * 执行任意sql语句
     * @param unknown $sql
     * @param array $binds
     */
    public function execute($sql, array $binds = []);
    
    
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
     * 最后执行的SQL语句
     */
    public function lastSql();
    
    /**
     * 所有执行过的SQL
     */
    public function allSql();
    
    
    public function beginTransaction();
    
    public function commit();
    
    public function rollBack();
    
}
