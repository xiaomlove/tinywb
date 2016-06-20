<?php
namespace models;

use framework\Model;

class Juan extends Model
{
    public function policyId()
    {
        return 'default';
    }
    
    public function tableName()
    {
        return 'juan';
    }

    public function test()
    {
    }
}