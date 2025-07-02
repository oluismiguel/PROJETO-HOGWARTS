<?php

namespace App\Modules\AlertSystem\Users;

class Student extends User
{
    private string $house;
    
    public function __construct(string $name, string $email, string $house)
    {
        parent::__construct($name, $email, 'student');
        $this->house = $house;
    }
    
    public function getHouse(): string
    {
        return $this->house;
    }
}