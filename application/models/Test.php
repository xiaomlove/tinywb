<?php
namespace models;

use framework\Model;

class Test extends Model
{
    public function pocilyId()
    {
        return 'wordpress';
    }
    
    public function tableName()
    {
        return 'wp_users';
    }
}