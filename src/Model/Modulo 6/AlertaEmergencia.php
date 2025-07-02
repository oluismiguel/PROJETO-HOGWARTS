<?php

namespace App\Modules\AlertSystem\Alerts;

use App\Modules\AlertSystem\Abstract\BaseAlert;
use App\Modules\AlertSystem\Interfaces\UserInterface;

class EmergencyAlert extends BaseAlert
{
    public function __construct(string $message, UserInterface $recipient)
    {
        parent::__construct($message, $recipient, 'urgent');
    }
    
    public function send(): bool
    {
    
        echo "🚨 ALERTA DE EMERGÊNCIA para {$this->recipient->getName()}\n";
        echo "⚠️  URGENTE: {$this->message}\n\n";
        return true;
    }
}
