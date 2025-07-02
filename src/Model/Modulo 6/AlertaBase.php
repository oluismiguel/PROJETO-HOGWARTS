<?php

namespace App\Modules\AlertSystem\Abstract;

use App\Modules\AlertSystem\Interfaces\AlertInterface;
use App\Modules\AlertSystem\Interfaces\UserInterface;

abstract class BaseAlert implements AlertInterface
{
    protected string $message;
    protected UserInterface $recipient;
    protected string $priority;
    
    public function __construct(string $message, UserInterface $recipient, string $priority = 'normal')
    {
        $this->message = $message;
        $this->recipient = $recipient;
        $this->priority = $priority;
    }
    
    public function getMessage(): string
    {
        return $this->message;
    }
    
    public function getPriority(): string
    {
        return $this->priority;
    }
    
    abstract public function send(): bool;
}