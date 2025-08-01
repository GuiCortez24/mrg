<?php
/**
 * Localização: /PHP_ACTION/summary.php
 *
 * Gera o resumo do mês, incluindo dados para os gráficos.
 * Retorna um JSON com o HTML e os dados dos gráficos.
 */

session_start();
include '../db.php';

// Proteção básica
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Acesso negado.']);
    exit();
}

$month = $_GET['month'] ?? date('m');
$year = $_GET['year'] ?? date('Y');

// Query principal para agrupar e somar os dados
// Excluímos apólices canceladas dos cálculos de produção
$sql = "SELECT
            seguradora,
            tipo_seguro,
            SUM(premio_liquido) AS total_premio,
            SUM(premio_liquido * (comissao / 100)) AS total_comissao,
            COUNT(id) AS total_clientes
        FROM
            clientes
        WHERE
            MONTH(inicio_vigencia) = ? AND YEAR(inicio_vigencia) = ?
            AND status != 'Cancelado'
        GROUP BY
            seguradora, tipo_seguro
        ORDER BY
            total_premio DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $month, $year);
$stmt->execute();
$result = $stmt->get_result();
$data_agrupada = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();


// --- Processamento dos Dados em PHP ---

// 1. Calcular totais gerais
$total_premio_mes = 0;
$total_comissao_mes = 0;
$total_clientes_mes = 0;
$seguradoras_unicas = [];
$tipos_seguro_unicos = [];

// Arrays para os dados dos gráficos
$clientes_por_seguradora = [];
$premio_por_seguradora = [];
$clientes_por_tipo = [];
$premio_por_tipo = [];

foreach ($data_agrupada as $row) {
    $total_premio_mes += $row['total_premio'];
    $total_comissao_mes += $row['total_comissao'];
    $total_clientes_mes += $row['total_clientes'];
    
    // Contagem para os resumos
    if (!in_array($row['seguradora'], $seguradoras_unicas)) $seguradoras_unicas[] = $row['seguradora'];
    if (!in_array($row['tipo_seguro'], $tipos_seguro_unicos)) $tipos_seguro_unicos[] = $row['tipo_seguro'];
    
    // Agrupamento para os gráficos
    $seguradora = $row['seguradora'];
    $tipo = $row['tipo_seguro'];

    @$clientes_por_seguradora[$seguradora] += $row['total_clientes'];
    @$premio_por_seguradora[$seguradora] += $row['total_premio'];
    @$clientes_por_tipo[$tipo] += $row['total_clientes'];
    @$premio_por_tipo[$tipo] += $row['total_premio'];
}

// Contagem de apólices emitidas e canceladas no mês
$status_count_sql = "SELECT status, COUNT(id) as total FROM clientes WHERE MONTH(inicio_vigencia) = ? AND YEAR(inicio_vigencia) = ? GROUP BY status";
$stmt_status = $conn->prepare($status_count_sql);
$stmt_status->bind_param("ss", $month, $year);
$stmt_status->execute();
$status_result = $stmt_status->get_result();
$total_canceladas = 0;
$total_emitidas = 0;
while($row = $status_result->fetch_assoc()){
    if($row['status'] == 'Cancelado') $total_canceladas = $row['total'];
    if($row['status'] == 'Emitida') $total_emitidas = $row['total'];
}
$stmt_status->close();


// --- Geração do HTML ---
ob_start(); // Inicia o buffer de saída para capturar o HTML
?>
<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="table-light">
            <tr>
                <th>Seguradora</th>
                <th>Tipo de Seguro</th>
                <th>Total Prêmio Líquido</th>
                <th>Total Comissão</th>
                <th>Total Clientes</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($data_agrupada) > 0): ?>
                <?php foreach ($data_agrupada as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['seguradora']); ?></td>
                        <td><?php echo htmlspecialchars($row['tipo_seguro']); ?></td>
                        <td>R$ <?php echo number_format($row['total_premio'], 2, ',', '.'); ?></td>
                        <td>R$ <?php echo number_format($row['total_comissao'], 2, ',', '.'); ?></td>
                        <td><?php echo $row['total_clientes']; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" class="text-center">Nenhum dado de produção encontrado para este mês.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<hr>

<div class="row mt-4">
    <div class="col-md-6">
        <h6><strong>Resumo Geral do Mês:</strong></h6>
        <ul class="list-group list-group-flush">
            <li class="list-group-item"><strong>Total Prêmio Líquido:</strong> R$ <?php echo number_format($total_premio_mes, 2, ',', '.'); ?></li>
            <li class="list-group-item"><strong>Total Comissão:</strong> R$ <?php echo number_format($total_comissao_mes, 2, ',', '.'); ?></li>
            <li class="list-group-item"><strong>Total de Seguradoras:</strong> <?php echo count($seguradoras_unicas); ?></li>
            <li class="list-group-item"><strong>Total de Tipos de Seguro:</strong> <?php echo count($tipos_seguro_unicos); ?></li>
            <li class="list-group-item"><strong>Apólices Emitidas:</strong> <?php echo $total_emitidas; ?></li>
            <li class="list-group-item"><strong>Apólices Canceladas:</strong> <?php echo $total_canceladas; ?></li>
        </ul>
    </div>
    <div class="col-md-6">
        </div>
</div>

<hr>

<h5 class="text-center mt-4 mb-3">Análise Gráfica</h5>
<div class="row">
    <div class="col-md-6"><canvas id="chartPremioPorSeguradora"></canvas></div>
    <div class="col-md-6"><canvas id="chartClientesPorSeguradora"></canvas></div>
</div>
<div class="row mt-4">
    <div class="col-md-6"><canvas id="chartPremioPorTipo"></canvas></div>
    <div class="col-md-6"><canvas id="chartClientesPorTipo"></canvas></div>
</div>

<?php
$html_content = ob_get_clean(); // Captura o HTML gerado


// --- Preparação dos Dados para JSON ---
$response = [
    'success' => true,
    'html' => $html_content,
    'chartData' => [
        'premioPorSeguradora' => [
            'labels' => array_keys($premio_por_seguradora),
            'data' => array_values($premio_por_seguradora)
        ],
        'clientesPorSeguradora' => [
            'labels' => array_keys($clientes_por_seguradora),
            'data' => array_values($clientes_por_seguradora)
        ],
        'premioPorTipo' => [
            'labels' => array_keys($premio_por_tipo),
            'data' => array_values($premio_por_tipo)
        ],
        'clientesPorTipo' => [
            'labels' => array_keys($clientes_por_tipo),
            'data' => array_values($clientes_por_tipo)
        ]
    ]
];

header('Content-Type: application/json');
echo json_encode($response);