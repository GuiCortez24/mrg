<?php
/**
 * Localização: /PHP_ACTION/helpers/summary_functions.php
 * Funções de apoio para buscar e processar os dados do resumo mensal.
 */

function fetchProductionData($conn, $month, $year, $pode_ver_comissao) {
    $comissao_select = $pode_ver_comissao ? "SUM(premio_liquido * (comissao / 100)) AS total_comissao," : "0 AS total_comissao,";

    $sql = "SELECT
                seguradora,
                tipo_seguro,
                SUM(premio_liquido) AS total_premio,
                $comissao_select
                COUNT(id) AS total_clientes
            FROM clientes
            WHERE MONTH(inicio_vigencia) = ? AND YEAR(inicio_vigencia) = ? AND status != 'Cancelado'
            GROUP BY seguradora, tipo_seguro
            ORDER BY total_premio DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $month, $year);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $data;
}

function fetchStatusCounts($conn, $month, $year) {
    $sql = "SELECT status, COUNT(id) as total FROM clientes WHERE MONTH(inicio_vigencia) = ? AND YEAR(inicio_vigencia) = ? GROUP BY status";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $month, $year);
    $stmt->execute();
    $result = $stmt->get_result();
    $counts = ['canceladas' => 0, 'emitidas' => 0];
    while($row = $result->fetch_assoc()){
        if($row['status'] == 'Cancelado') $counts['canceladas'] = $row['total'];
        if($row['status'] == 'Emitida') $counts['emitidas'] = $row['total'];
    }
    $stmt->close();
    return $counts;
}

function processProductionData($data_agrupada) {
    $summary = [
        'total_premio_mes' => 0, 'total_comissao_mes' => 0, 'total_clientes_mes' => 0,
        'seguradoras_unicas' => [], 'tipos_seguro_unicos' => [],
        'clientes_por_seguradora' => [], 'premio_por_seguradora' => [],
        'clientes_por_tipo' => [], 'premio_por_tipo' => []
    ];

    foreach ($data_agrupada as $row) {
        $summary['total_premio_mes'] += $row['total_premio'];
        $summary['total_comissao_mes'] += $row['total_comissao'];
        
        $seguradora = $row['seguradora'];
        $tipo = $row['tipo_seguro'];

        if (!isset($summary['seguradoras_unicas'][$seguradora])) $summary['seguradoras_unicas'][$seguradora] = true;
        if (!isset($summary['tipos_seguro_unicos'][$tipo])) $summary['tipos_seguro_unicos'][$tipo] = true;
        
        @$summary['clientes_por_seguradora'][$seguradora] += $row['total_clientes'];
        @$summary['premio_por_seguradora'][$seguradora] += $row['total_premio'];
        @$summary['clientes_por_tipo'][$tipo] += $row['total_clientes'];
        @$summary['premio_por_tipo'][$tipo] += $row['total_premio'];
    }

    $summary['total_clientes_mes'] = array_sum($summary['clientes_por_seguradora']);
    return $summary;
}
?>