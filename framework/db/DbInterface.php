<?php

namespace framework\db;

interface DbInterface 
{
    public function createConnection($dsn, $user = '', $password = '', array $options = []);

    public function execute($sql, array $binds = []);

    
}
