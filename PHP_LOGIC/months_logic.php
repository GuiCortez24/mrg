<?php
/**
 * Localização: /PHP_LOGIC/months_logic.php
 * Prepara os dados e verifica as permissões para a página de Produção Mensal.
 */

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../INCLUDES/functions.php';

// Array de meses para o grid
$months = [
    '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril',
    '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto',
    '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
];

// Anos para os seletores
$currentYear = date('Y');
$years = [];
for ($i = $currentYear; $i >= $currentYear - 10; $i--) {
    $years[] = $i;
}

// --- AJUSTE DE PERMISSÃO ---
// Verifica se o usuário pode ver dados de comissão e prepara para o JavaScript
$user_can_see_commission = hasPermission('pode_ver_comissao_total');

?>