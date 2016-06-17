<?php

namespace framework\db\drivers;

use framework\db\DbInterface;

class Mysql implements DbInterface
{
    private $pdo;
    
    private $lastSql;
    
    private $allSql = [];

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
        try {
            $this->pdo = new \PDO($dsn, $user, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_TIMEOUT => 1,
                \PDO::ATTR_CASE => \PDO::CASE_NATURAL,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
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
     * 执行任意SQL。
     * @param unknown $sql
     * @param array $binds
     */
    public function execute($sql, array $binds = [])
    {
        $this->collectSql($sql, $binds);
        $stat = $this->pdo->prepare($sql);
        $stat->execute($binds);
        return $stat->rowCount();
    }
    
    /**
     * 执行SELECT，获取一行
     * @param unknown $sql
     * @param array $binds
    */
    public function fetch($sql, array $binds = [], $fetchStyle = \PDO::FETCH_ASSOC)
    {
        $this->collectSql($sql, $binds);
        $stat = $this->pdo->prepare($sql);
        $stat->execute($binds);
        return $stat->fetch($fetchStyle);
    }
    
    /**
     * 执行SELECT，获取所有行
     * @param unknown $sql
     * @param array $binds
    */
    public function fetchAll($sql, array $binds = [], $fetchStyle = \PDO::FETCH_ASSOC)
    {
        $this->collectSql($sql, $binds);
        $stat = $this->pdo->prepare($sql);
        $stat->execute($binds);
        return $stat->fetchAll($fetchStyle);
    }
    
    /**
     * 执行SELECT，获取一列
     * @param unknown $sql
     * @param array $binds
    */
    public function fetchColumn($sql, array $binds = [])
    {
        $this->collectSql($sql, $binds);
        $stat = $this->pdo->prepare($sql);
        $stat->execute($binds);
        return $stat->fetchColumn();
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
        return $this->pdo->beginTransaction();
    }
    
    public function commit()
    {
        return $this->pdo->commit();
    }
    
    public function rollBack()
    {
        return $this->rollBack();
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