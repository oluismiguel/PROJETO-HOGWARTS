<?php

require_once '../Interfaces/AlertInterface.php';
require_once '../Interfaces/UserInterface.php';
require_once '../Abstract/BaseAlert.php';
require_once '../Users/User.php';
require_once '../Users/Student.php';
require_once '../Users/Professor.php';
require_once '../Alerts/EmailAlert.php';
require_once '../Alerts/EmergencyAlert.php';
require_once '../Alerts/HouseAlert.php';
require_once '../System/AlertSystem.php';

use App\Modules\AlertSystem\Users\Student;
use App\Modules\AlertSystem\Users\Professor;
use App\Modules\AlertSystem\System\AlertSystem;
use App\Modules\AlertSystem\Alerts\EmailAlert;
use App\Modules\AlertSystem\Alerts\EmergencyAlert;


$harry = new Student("Harry Potter", "harry@hogwarts.edu", "Grifinória");
$hermione = new Student("Hermione Granger", "hermione@hogwarts.edu", "Grifinória");
$draco = new Student("Draco Malfoy", "draco@hogwarts.edu", "Sonserina");
$snape = new Professor("Severus Snape", "snape@hogwarts.edu", "Poções");
$mcgonagall = new Professor("Minerva McGonagall", "mcgonagall@hogwarts.edu", "Transfiguração");


$alertSystem = new AlertSystem();

$alertSystem->addUser($harry);
$alertSystem->addUser($hermione);
$alertSystem->addUser($draco);
$alertSystem->addUser($snape);
$alertSystem->addUser($mcgonagall);

echo "🎓 SISTEMA DE ALERTAS DE HOGWARTS\n";
echo "═════════════════════════════════════════════════════════════\n\n";

echo "1️⃣  ALERTA INDIVIDUAL:\n";
echo "─────────────────────────\n";
$individualAlert = new EmailAlert("Sua aula de Poções foi remarcada para amanhã às 14h", $harry);
$alertSystem->sendAlert($individualAlert);

echo "2️⃣  ALERTA DE EMERGÊNCIA:\n";
echo "─────────────────────────\n";
$emergencyAlert = new EmergencyAlert("Troll solto no castelo! Todos para os dormitórios!", $hermione);
$alertSystem->sendAlert($emergencyAlert);

echo "3️⃣  ALERTA PARA CASA GRIFINÓRIA:\n";
echo "─────────────────────────────────\n";
$alertSystem->sendHouseAlert("Grifinória", "Reunião da casa hoje às 19h no Salão Comunal");

echo "4️⃣  ALERTA EM MASSA:\n";
$alertSystem->sendBulkAlert("Lembrete: Festa de Halloween amanhã no Grande Salão!", "normal");

echo "📊 Total de alertas enviados: " . count($alertSystem->getAlertHistory()) . "\n";
echo "\n✅ Sistema de Alertas funcionando corretamente!\n";
