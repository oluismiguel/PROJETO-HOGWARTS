<?php

namespace App\Modules\AlertSystem\System;

use App\Modules\AlertSystem\Interfaces\AlertInterface;
use App\Modules\AlertSystem\Interfaces\UserInterface;
use App\Modules\AlertSystem\Alerts\EmailAlert;
use App\Modules\AlertSystem\Alerts\HouseAlert;
use App\Modules\AlertSystem\Users\Student;

class AlertSystem
{
    private array $users = [];
    private array $alertHistory = [];
    
    public function addUser(UserInterface $user): void
    {
        $this->users[] = $user;
    }
    
    public function sendAlert(AlertInterface $alert): bool
    {
        $success = $alert->send();
        
        if ($success) {
            $this->alertHistory[] = [
                'alert' => $alert,
                'timestamp' => date('Y-m-d H:i:s'),
                'status' => 'sent'
            ];
        }
        
        return $success;
    }
    
    public function sendBulkAlert(string $message, string $priority = 'normal'): void
    {
        echo "📢 Enviando alerta em massa...\n";
        echo "═══════════════════════════════════\n";
        
        foreach ($this->users as $user) {
            $alert = new EmailAlert($message, $user, $priority);
            $this->sendAlert($alert);
        }
    }
    
    public function sendHouseAlert(string $house, string $message): void
    {
        echo "🏠 Enviando alerta para a Casa {$house}...\n";
        echo "═══════════════════════════════════\n";
        
        foreach ($this->users as $user) {
            if ($user instanceof Student && $user->getHouse() === $house) {
                $alert = new HouseAlert($message, $user, $house);
                $this->sendAlert($alert);
            }
        }
    }
    
    public function getAlertHistory(): array
    {
        return $this->alertHistory;
    }
}