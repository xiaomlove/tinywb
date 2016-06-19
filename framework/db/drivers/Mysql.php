<?php

namespace framework\db\drivers;

use framework\db\DbInterface;
use framework\exceptions\SQLException;

class Mysql implements DbInterface
{
    private $pdo;
    
    private $lastSql;
    
    private $allSql = [];
    
    private $transactions = 0;

    private static $attributes = [
        "AUTOCOMMIT", "ERRMODE", "CASE", "CLIENT_VERSION", "CONNECTION_STATUS",
        "ORACLE_NULLS", "PERSISTENT", "SERVER_INFO","SERVER_VERSION"
    ];
    
    public function __construct($pocily)
    {
        if (!class_exists('\PDO')) {
            throw new \RuntimeException('class \PDO is not exists.');
        }
        if (empty($pocily['dsn'])) {
            throw new \RuntimeException('no set pocily.dsn: ' . print_r($pocily, true));
        }
        $dsn = $pocily['dsn'];
        $user = empty($pocily['user']) ? '' : $pocily['user'];
        $password = empty($pocily['password']) ? '' : $pocily['password'];
        $this->pdo = new \PDO($dsn, $user, $password, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_TIMEOUT => 1,
            \PDO::ATTR_CASE => \PDO::CASE_NATURAL,
            \PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    public function getAttribute($attr = null)
    {
        if (is_null($attr)) {
            $attributes = [];
            foreach (self::$attributes as $key => $value) {
                $attributes[$value] = $this->pdo->getAttribute(constant("\PDO::ATTR_{$value}"));
            }
            return $attributes;
        } elseif (in_array($attr, self::$attributes)) {
            return $this->pdo->getAttribute(constant("\PDO::ATTR_{$attr}"));
        } else {
            return '';
        }
    }
    
    /**
     * 执行任意语句，一般是不需要prepare的修改设置类，如set names 'GBK'，set autocommit 1.
     * @param unknown $sql
     * @return number 返回受影响记录条数，失败则是0
     */
    public function exec($sql)
    {
        return $this->pdo->exec($sql);
    }
    
    /**
     * 执行任意需要prepare的非查询语句，如增删改类。
     * @param unknown $sql
     * @param array $binds
     * @return false|int 成功返回受影响记录条数，失败返回false
     */
    public function execute($sql, array $binds = [])
    {
        $this->collectSql($sql, $binds);
        try {
            $stat = $this->pdo->prepare($sql);
            if ($stat === false) {
                //如果是ERR_SILENT，prepare不成功返回false，直接返回。
                return false;
            }
            $result = $stat->execute($binds);
            return $result === false ? false : $stat->rowCount();
        } catch (\PDOException $e) {
            throw new SQLException($e->getCode(), $e->getMessage(), $sql, $binds);
        }
    }
    
    /**
     * 执行SELECT，获取一行
     * @param unknown $sql
     * @param array $binds
     * @return array|false 成功返回记录一维数组，失败返回false
    */
    public function fetch($sql, array $binds = [], $fetchStyle = \PDO::FETCH_ASSOC)
    {
        $this->collectSql($sql, $binds);
        try {
            $stat = $this->pdo->prepare($sql);
            if ($stat === false) {
                return false;
            }
            $result = $stat->execute($binds);
            return $result === false ? false : $stat->fetch($fetchStyle);
        } catch (\PDOException $e) {
            throw new SQLException($e->getCode(), $e->getMessage(), $sql, $binds);
        }
        
    }
    
    /**
     * 执行SELECT，获取所有行
     * @param unknown $sql
     * @param array $binds
    */
    public function fetchAll($sql, array $binds = [], $fetchStyle = \PDO::FETCH_ASSOC)
    {
        $this->collectSql($sql, $binds);
        try {
            $stat = $this->pdo->prepare($sql);
            if ($stat === false) {
                return false;
            }
            $result = $stat->execute($binds);
            return $result === false ? false :$stat->fetchAll($fetchStyle);
        } catch (\PDOException $e) {
            throw new SQLException($e->getCode(), $e->getMessage(), $sql, $binds);
        }
        
    }
    
    /**
     * 执行SELECT，获取一列
     * @param unknown $sql
     * @param array $binds
    */
    public function fetchColumn($sql, array $binds = [])
    {
        $this->collectSql($sql, $binds);
        try {
            $stat = $this->pdo->prepare($sql);
            if ($stat === false) {
                return false;
            }
            $result = $stat->execute($binds);
            return $result === false ? false : $stat->fetchColumn();
        } catch (\PDOException $e) {
            throw new SQLException($e->getCode(), $e->getMessage(), $sql, $binds);
        }
       
    }
    
    
    /**
     * 返回最后插入的ID
    */
    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }
    
    /**
     * 最后执行的SQL语句
    */
    public function lastSql()
    {
        return $this->lastSql;
    }
    
    /**
     * 所有执行过的SQL
    */
    public function allSql()
    {
        return $this->allSql;
    }
    
    
    public function beginTransaction()
    {
        ++$this->transactions;
        if ($this->transactions == 1) {
            //只有第一层才真正开启事务
            return $this->pdo->beginTransaction();
        }
        return true;
    }
    
    public function commit()
    {
        if ($this->transactions == 1) {
            $this->transactions = 0;
            return $this->pdo->commit();
        } else {
            --$this->transactions;
            return true;
        }
    }
    
    public function rollBack()
    {
        if ($this->transactions == 1) {
            $this->transactions = 0;
            return $this->pdo->rollBack();
        } else {
            --$this->transactions;
            return true;
        }
    }
    
    private function collectSql($sql, array $binds)
    {
        $sql .= ' ---binds: ';
        foreach ($binds as $name => $value) {
            $sql .= "$name => $value, ";
        }
        $sql = rtrim($sql, ', ');
        
        $this->lastSql = $sql;
        $this->allSql[] = $sql;
    }

}