<?php
/**
 * Localização: /PHP_ACTION/comparison.php
 * Busca dados de produção para um mês em dois anos diferentes e retorna uma tabela HTML comparativa.
 */

session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo "Acesso negado.";
    exit();
}

// Valida os parâmetros recebidos
$month = $_GET['month'] ?? null;
$year1 = $_GET['year1'] ?? null;
$year2 = $_GET['year2'] ?? null;

if (!$month || !$year1 || !$year2) {
    http_response_code(400);
    echo "Parâmetros inválidos.";
    exit();
}

// Função auxiliar para buscar os dados de um ano específico
function getMonthSummary($conn, $month, $year) {
    $sql = "SELECT 
                COUNT(id) as total_clientes, 
                SUM(premio_liquido) as total_premio,
                SUM(premio_liquido * (comissao / 100)) as total_comissao
            FROM clientes
            WHERE MONTH(inicio_vigencia) = ? AND YEAR(inicio_vigencia) = ? AND status != 'Cancelado'";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $month, $year);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    // Garante que os valores não sejam nulos
    return [
        'clientes' => $result['total_clientes'] ?? 0,
        'premio' => $result['total_premio'] ?? 0,
        'comissao' => $result['total_comissao'] ?? 0,
    ];
}

// Busca os dados para os dois anos
$data_year1 = getMonthSummary($conn, $month, $year1);
$data_year2 = getMonthSummary($conn, $month, $year2);

// Função auxiliar para calcular a porcentagem de mudança
function calculateChange($current, $previous) {
    if ($previous == 0) {
        return ($current > 0) ? '<span style="color: green;">(Novo)</span>' : '<span>( - )</span>';
    }
    $change = (($current - $previous) / $previous) * 100;
    $color = $change >= 0 ? 'green' : 'red';
    $icon = $change >= 0 ? '▲' : '▼';
    return sprintf('<span style="color: %s;">%s %.2f%%</span>', $color, $icon, $change);
}

// Mapeia o número do mês para o nome
$months_map = [
    '01'=>'Janeiro', '02'=>'Fevereiro', '03'=>'Março', '04'=>'Abril', '05'=>'Maio', '06'=>'Junho',
    '07'=>'Julho', '08'=>'Agosto', '09'=>'Setembro', '10'=>'Outubro', '11'=>'Novembro', '12'=>'Dezembro'
];
$month_name = $months_map[$month];

?>

<h4 class="text-center mb-4">Comparativo de Produção: <?php echo $month_name; ?></h4>
<table class="table table-bordered table-hover">
    <thead class="table-light">
        <tr>
            <th>Métrica</th>
            <th class="text-center"><?php echo $year2; ?></th>
            <th class="text-center"><?php echo $year1; ?></th>
            <th class="text-center">Variação %</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><strong><i class="bi bi-people-fill text-muted me-2"></i> Total de Apólices</strong></td>
            <td class="text-center"><?php echo $data_year2['clientes']; ?></td>
            <td class="text-center"><?php echo $data_year1['clientes']; ?></td>
            <td class="text-center"><?php echo calculateChange($data_year1['clientes'], $data_year2['clientes']); ?></td>
        </tr>
        <tr>
            <td><strong><i class="bi bi-cash-stack text-muted me-2"></i> Total Prêmio Líquido</strong></td>
            <td class="text-center">R$ <?php echo number_format($data_year2['premio'], 2, ',', '.'); ?></td>
            <td class="text-center">R$ <?php echo number_format($data_year1['premio'], 2, ',', '.'); ?></td>
            <td class="text-center"><?php echo calculateChange($data_year1['premio'], $data_year2['premio']); ?></td>
        </tr>
        <tr>
            <td><strong><i class="bi bi-currency-dollar text-muted me-2"></i> Total Comissão</strong></td>
            <td class="text-center">R$ <?php echo number_format($data_year2['comissao'], 2, ',', '.'); ?></td>
            <td class="text-center">R$ <?php echo number_format($data_year1['comissao'], 2, ',', '.'); ?></td>
            <td class="text-center"><?php echo calculateChange($data_year1['comissao'], $data_year2['comissao']); ?></td>
        </tr>
    </tbody>
</table>