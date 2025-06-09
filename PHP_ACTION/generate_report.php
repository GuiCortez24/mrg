<?php
ob_start();

require_once('../db.php');
require_once('vendor/tecnickcom/tcpdf/tcpdf.php');

// Verificar parâmetros
if (!isset($_GET['reportType']) || !isset($_GET['startDate']) || !isset($_GET['endDate'])) {
    die("Todos os parâmetros são necessários.");
}

$reportType = $_GET['reportType'];
$startDateParam = $_GET['startDate'];
$endDateParam = $_GET['endDate'];

$startDate = date('d/m/Y', strtotime($startDateParam));
$endDate = date('d/m/Y', strtotime($endDateParam));

// Consulta com filtro correto
$stmt = $conn->prepare("SELECT nome, seguradora, inicio_vigencia, final_vigencia, numero, email, tipo_seguro, premio_liquido, status 
                        FROM clientes 
                        WHERE 
                            (
                                (inicio_vigencia BETWEEN ? AND ?)
                                OR (final_vigencia BETWEEN ? AND ?)
                            )
                            AND (final_vigencia IS NULL OR final_vigencia != '0000-00-00')
                            AND status != 'Cancelado'
                        ORDER BY inicio_vigencia ASC, seguradora ASC");
$stmt->bind_param("ssss", $startDateParam, $endDateParam, $startDateParam, $endDateParam);
$stmt->execute();
$result = $stmt->get_result();

$totalPremioLiquido = 0;
$totalClientes = 0;

// Criar o PDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetAutoPageBreak(true, 10);
$pdf->SetMargins(10, 20, 10);
$pdf->AddPage('P');

$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, "MRG Seguros - Relatório de $reportType", 0, 1, 'C');
$pdf->SetFont('helvetica', '', 11);
$pdf->Cell(0, 5, "Período: $startDate a $endDate", 0, 1, 'C');
$pdf->Ln(10);

// Estilo modal por cliente
$pdf->SetFont('helvetica', '', 11);

while ($row = $result->fetch_assoc()) {
    $totalClientes++;
    $totalPremioLiquido += $row['premio_liquido'];

    $inicioVigencia = date('d/m/Y', strtotime($row['inicio_vigencia']));
    $finalVigencia = (!empty($row['final_vigencia']) && $row['final_vigencia'] !== '0000-00-00')
        ? date('d/m/Y', strtotime($row['final_vigencia']))
        : 'Não informado';

    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 8, "Cliente: " . strtoupper($row['nome']), 0, 1, 'L');

    $pdf->SetFont('helvetica', '', 11);
    $pdf->MultiCell(0, 6,
        "Seguradora: " . $row['seguradora'] . "\n" .
        "Início Vigência: " . $inicioVigencia . "\n" .
        "Final Vigência: " . $finalVigencia . "\n" .
        "Telefone: " . $row['numero'] . "\n" .
        "Email: " . $row['email'] . "\n" .
        "Tipo de Seguro: " . $row['tipo_seguro'] . "\n" .
        "Status: " . $row['status'] . "\n" .
        "Valor Pago: R$ " . number_format($row['premio_liquido'], 2, ',', '.'),
        0, 'L', false
    );

    $pdf->Ln(5); // espaço entre blocos
}

// Resumo
$pdf->Ln(10);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 8, "Resumo da Produção", 0, 1, 'C');
$pdf->SetFont('helvetica', '', 11);
$pdf->MultiCell(0, 8,
    "Total de Clientes: " . $totalClientes . "\n" .
    "Total do Prêmio Líquido: R$ " . number_format($totalPremioLiquido, 2, ',', '.'),
    0, 'L', false
);

ob_end_clean();
$pdf->Output("Relatorio_{$reportType}_{$startDate}_a_{$endDate}.pdf", 'D');
exit;
?>
