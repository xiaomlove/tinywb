<?php
namespace cli;

use framework\db\DB;

class Test
{
    public function t()
    {
        $connection = DB::getConnection('default');
        var_dump($connection);
    }
}