<?php
include '../db.php';

$month = isset($_GET['month']) ? $_GET['month'] : '';
$year = isset($_GET['year']) ? $_GET['year'] : '';
$compareYear = isset($_GET['compareYear']) ? $_GET['compareYear'] : '';

if ($month && $year && $compareYear) {
    // Consulta para o ano atual
    $stmt = $conn->prepare("SELECT seguradora, tipo_seguro, 
                                   SUM(premio_liquido) as total_premio_liquido, 
                                   SUM(premio_liquido * (comissao / 100)) as total_comissao, 
                                   COUNT(*) as total_clientes 
                            FROM clientes 
                            WHERE MONTH(inicio_vigencia) = ? AND YEAR(inicio_vigencia) = ? 
                            GROUP BY seguradora, tipo_seguro");
    $stmt->bind_param("ss", $month, $year);
    $stmt->execute();
    $resultCurrentYear = $stmt->get_result();

    // Consulta para o ano de comparação
    $stmt->bind_param("ss", $month, $compareYear);
    $stmt->execute();
    $resultCompareYear = $stmt->get_result();

    $comparisons = [];

    // Processa os resultados para o ano atual
    while ($row = $resultCurrentYear->fetch_assoc()) {
        $comparisons[$row['seguradora']][$row['tipo_seguro']] = [
            'currentYear' => $row,
            'compareYear' => null
        ];
    }

    // Processa os resultados para o ano de comparação
    while ($row = $resultCompareYear->fetch_assoc()) {
        if (isset($comparisons[$row['seguradora']][$row['tipo_seguro']])) {
            $comparisons[$row['seguradora']][$row['tipo_seguro']]['compareYear'] = $row;
        } else {
            $comparisons[$row['seguradora']][$row['tipo_seguro']] = [
                'currentYear' => null,
                'compareYear' => $row
            ];
        }
    }

    // Exibição dos resultados
    foreach ($comparisons as $seguradora => $tipos) {
        foreach ($tipos as $tipoSeguro => $data) {
            $current = $data['currentYear'];
            $compare = $data['compareYear'];

            $currentProduction = $current ? $current['total_premio_liquido'] : 0;
            $currentCommission = $current ? $current['total_comissao'] : 0;
            $compareProduction = $compare ? $compare['total_premio_liquido'] : 0;
            $compareCommission = $compare ? $compare['total_comissao'] : 0;
            $currentClients = $current ? $current['total_clientes'] : 0;
            $compareClients = $compare ? $compare['total_clientes'] : 0;

            $productionDifference = $currentProduction - $compareProduction;
            $commissionDifference = $currentCommission - $compareCommission;
            $clientsDifference = $currentClients - $compareClients;

            echo "<p>Seguradora: <strong>{$seguradora}</strong></p>";
            echo "<p>Tipo de Seguro: <strong>{$tipoSeguro}</strong></p>";
            echo "<p>Em {$year}, a produção foi de R$ " . number_format($currentProduction, 2, ',', '.') . ", a comissão foi de R$ " . number_format($currentCommission, 2, ',', '.') . ", e o número de clientes foi {$currentClients}.</p>";
            echo "<p>Em {$compareYear}, a produção foi de R$ " . number_format($compareProduction, 2, ',', '.') . ", a comissão foi de R$ " . number_format($compareCommission, 2, ',', '.') . ", e o número de clientes foi {$compareClients}.</p>";
            echo "<p>Variação na produção: R$ " . number_format($productionDifference, 2, ',', '.') . ".</p>";
            echo "<p>Variação na comissão: R$ " . number_format($commissionDifference, 2, ',', '.') . ".</p>";
            echo "<p>Variação no número de clientes: " . ($clientsDifference > 0 ? "+" : "") . "{$clientsDifference}.</p>";
            echo "<hr>";
        }
    }
} else {
    echo '<p>Parâmetros inválidos para comparação.</p>';
}
$conn->close();
?>
