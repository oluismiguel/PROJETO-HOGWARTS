<?php
require_once __DIR__ . '/vendor/autoload.php';

use Src\Model\MenuPrincipal;

try {
    $menu = new MenuPrincipal();
    $menu->executar();
} catch (Exception $e) {
    echo "Erro fatal: " . $e->getMessage() . "\n";
    exit(1);
}