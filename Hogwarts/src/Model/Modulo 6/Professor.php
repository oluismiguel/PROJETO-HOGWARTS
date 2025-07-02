<?php

namespace App\Modules\AlertSystem\Users;

class Professor extends User
{
    private string $subject;
    
    public function __construct(string $name, string $email, string $subject)
    {
        parent::__construct($name, $email, 'professor');
        $this->subject = $subject;
    }
    
    public function getSubject(): string
    {
        return $this->subject;
    }
}