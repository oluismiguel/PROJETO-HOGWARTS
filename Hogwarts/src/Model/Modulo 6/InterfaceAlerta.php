<?php

namespace App\Modules\AlertSystem\Interfaces;

interface AlertInterface
{
    public function send(): bool;
    public function getMessage(): string;
}