<?php

namespace App\Modules\AlertSystem\Alerts;

use App\Modules\AlertSystem\Abstract\BaseAlert;

class EmailAlert extends BaseAlert
{
    public function send(): bool
    {
    
        echo "📧 Email enviado para {$this->recipient->getName()} ({$this->recipient->getEmail()})\n";
        echo "Mensagem: {$this->message}\n";
        echo "Prioridade: {$this->priority}\n\n";
        return true;
    }
}
