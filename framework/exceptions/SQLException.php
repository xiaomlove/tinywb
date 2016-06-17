<?php
namespace framework\exceptions;

class SQLException extends \PDOException
{
    private $sql;
    
    private $binds;
    
    public function __construct($code, $msg, $sql, array $binds = [])
    {
        $this->code = $code;
        $this->message = $msg;
        $this->sql = $sql;
        $this->binds = $binds;
    }
    
    public function getSql()
    {
        return $this->sql;
    }
    
    public function getBinds()
    {
        return $this->binds;
    }
}