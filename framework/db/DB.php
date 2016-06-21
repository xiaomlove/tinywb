<?php
namespace framework\db;

use framework\Config;
use framework\db\drivers\Mysql;

final class DB 
{
    //一个策略ID(就想象成数据库名)创建一个数据库连接对象，同一个库的表的ID是一样的。
    //以策略ID为键存储起来所有数据库连接
    private static $dbConnections = [];
    
    public static function allSql()
    {
        $result = [];
        foreach (self::$dbConnections as $policyId => $db) {
            $result[$policyId] = $db->allSql();
        }
        return $result;
    }
    
    private function __construct()
    {
        
    }
    
    private function __clone()
    {
        
    }
    
    public static function getConnection($policyId)
    {
        if (isset(self::$dbConnections[$policyId]))
        {
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
    
    public static function getAllConnections()
    {
        return self::$dbConnections;
    }
    
    
}