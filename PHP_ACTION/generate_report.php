<?php
// Iniciar buffer de saída para evitar que qualquer saída interfira na geração do PDF
ob_start();

require_once('../db.php');
require_once('vendor/tecnickcom/tcpdf/tcpdf.php');

// Verificar se os parâmetros foram passados
if (!isset($_GET['reportType']) || !isset($_GET['startDate']) || !isset($_GET['endDate'])) {
    die("Todos os parâmetros são necessários.");
}

$reportType = $_GET['reportType'];
$startDate = date('d/m/Y', strtotime($_GET['startDate']));
$endDate = date('d/m/Y', strtotime($_GET['endDate']));

// Consulta com inclusão de final_vigencia
$stmt = $conn->prepare("SELECT nome, seguradora, inicio_vigencia, final_vigencia, numero, email, tipo_seguro, premio_liquido 
                        FROM clientes 
                        WHERE inicio_vigencia BETWEEN ? AND ? 
                        ORDER BY inicio_vigencia ASC, seguradora ASC");
$stmt->bind_param("ss", $_GET['startDate'], $_GET['endDate']);
$stmt->execute();
$result = $stmt->get_result();

// Calcular totais
$totalPremioLiquido = 0;
$totalClientes = $result->num_rows;

// Criar o PDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetAutoPageBreak(true, 10);
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage('P'); // vertical
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, "Relatório de $reportType - Período: $startDate a $endDate", 0, 1, 'C');
$pdf->Ln(10);
$pdf->SetFont('helvetica', '', 12);

// Cartões por cliente
while ($row = $result->fetch_assoc()) {
    $totalPremioLiquido += $row['premio_liquido'];

    $inicioVigencia = date('d/m/Y', strtotime($row['inicio_vigencia']));
    $finalVigencia = date('d/m/Y', strtotime($row['final_vigencia']));

    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 8, "Cliente: " . $row['nome'], 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 11);
    $pdf->MultiCell(0, 6, 
        "Seguradora: " . $row['seguradora'] . "\n" .
        "Início Vigência: " . $inicioVigencia . "\n" .
        "Final Vigência: " . $finalVigencia . "\n" .
        "Telefone: " . $row['numero'] . "\n" .
        "Email: " . $row['email'] . "\n" .
        "Tipo de Seguro: " . $row['tipo_seguro'] . "\n" .
        "Valor Pago: R$ " . number_format($row['premio_liquido'], 2, ',', '.'),
        0, 'L', false
    );
    $pdf->Ln(5);
}

// Resumo final
$pdf->Ln(10);
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, "Resumo da Produção do Mês", 0, 1, 'C');
$pdf->SetFont('helvetica', '', 12);
$pdf->MultiCell(0, 10, 
    "Total de Clientes: " . $totalClientes . "\n" .
    "Total do Prêmio Líquido: R$ " . number_format($totalPremioLiquido, 2, ',', '.'),
    0, 'L', false
);

// Finaliza buffer e gera PDF
ob_end_clean();
$pdf->Output("Relatorio_{$reportType}_{$startDate}_a_{$endDate}.pdf", 'D');
exit;
?>
