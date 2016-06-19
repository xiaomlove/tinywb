<?php
namespace models;

use framework\Model;

class Juan extends Model
{
    protected  $tableName = 'juan';
    
    public function __construct($policyId = 'wordpress')
    {
        parent::__construct($policyId);
    }
}