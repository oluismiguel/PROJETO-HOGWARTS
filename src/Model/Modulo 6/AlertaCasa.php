<?php

namespace App\Modules\AlertSystem\Alerts;

use App\Modules\AlertSystem\Abstract\BaseAlert;
use App\Modules\AlertSystem\Interfaces\UserInterface;

class HouseAlert extends BaseAlert
{
    private string $house;
    
    public function __construct(string $message, UserInterface $recipient, string $house)
    {
        parent::__construct($message, $recipient, 'normal');
        $this->house = $house;
    }
    
    public function send(): bool
    {
        echo "🏰 Alerta da Casa {$this->house} para {$this->recipient->getName()}\n";
        echo "Mensagem: {$this->message}\n\n";
        return true;
    }
}