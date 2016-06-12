<?php

namespace framework\db;

interface DbInterface 
{
    public function connect(array $config);

    public function close();

    public function execute($sql, array $binds = []);

    
}
