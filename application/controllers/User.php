<?php
namespace controllers;

class User
{
    
    public function profile($id, $age = 10)
    {
        return "$id----$age";
    }
}