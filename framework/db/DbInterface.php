<?php

namespace framework\db;

interface DbInterface 
{
    public function createConnection(array $config);

    public function close();

    public function execute($sql, array $binds = []);

    public function fetchAllRows($sql, array $binds = []);
}
