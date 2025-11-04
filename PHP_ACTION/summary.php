<?php
/**
 * Localização: /PHP_ACTION/summary.php
 *
 * Gera o resumo do mês. Atua como um controlador que:
 * 1. Verifica permissões.
 * 2. Chama helpers para buscar e processar dados.
 * 3. Renderiza um template para gerar o HTML.
 * 4. Retorna uma resposta JSON completa.
 */

session_start();
include '../db.php';
require_once __DIR__ . '/../INCLUDES/functions.php';
require_once __DIR__ . '/helpers/summary_functions.php';

// 1. VERIFICAÇÃO DE ACESSO E PERMISSÃO
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Acesso negado.']);
    exit();
}
$pode_ver_comissao = hasPermission('pode_ver_comissao_total');

// 2. BUSCA E PROCESSAMENTO DE DADOS
$month = $_GET['month'] ?? date('m');
$year = $_GET['year'] ?? date('Y');

$data_agrupada = fetchProductionData($conn, $month, $year, $pode_ver_comissao);
$status_counts = fetchStatusCounts($conn, $month, $year);
$summary_totals = processProductionData($data_agrupada);

// 3. RENDERIZAÇÃO DO HTML
ob_start();
require __DIR__ . '/templates/summary_template.php';
$html_content = ob_get_clean();

// 4. PREPARAÇÃO DA RESPOSTA JSON
$response = [
    'success' => true,
    'html' => $html_content,
    'chartData' => [
        'premioPorSeguradora' => [
            'labels' => array_keys($summary_totals['premio_por_seguradora']),
            'data' => array_values($summary_totals['premio_por_seguradora'])
        ],
        'clientesPorSeguradora' => [
            'labels' => array_keys($summary_totals['clientes_por_seguradora']),
            'data' => array_values($summary_totals['clientes_por_seguradora'])
        ],
        'premioPorTipo' => [
            'labels' => array_keys($summary_totals['premio_por_tipo']),
            'data' => array_values($summary_totals['premio_por_tipo'])
        ],
        'clientesPorTipo' => [
            'labels' => array_keys($summary_totals['clientes_por_tipo']),
            'data' => array_values($summary_totals['clientes_por_tipo'])
        ]
    ]
];

header('Content-Type: application/json');
echo json_encode($response);
?>