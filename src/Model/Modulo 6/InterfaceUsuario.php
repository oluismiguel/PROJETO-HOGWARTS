<?php

namespace App\Modules\AlertSystem\Interfaces;

interface UserInterface
{
    public function getName(): string;
    public function getEmail(): string;
}