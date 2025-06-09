<?php
include '../db.php';
require_once('vendor/tecnickcom/tcpdf/tcpdf.php');

$month = isset($_GET['month']) ? $_GET['month'] : date('m');
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');

// Consulta com os novos campos
$sql = "SELECT nome, seguradora, tipo_seguro, final_vigencia, status, premio_liquido, comissao 
        FROM clientes 
        WHERE MONTH(inicio_vigencia) = ? AND YEAR(inicio_vigencia) = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $month, $year);
$stmt->execute();
$result = $stmt->get_result();

// Inicia o PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Relatório MRG');
$pdf->SetTitle('Relatório de Clientes');
$pdf->SetSubject('Clientes - ' . date('F Y', strtotime("$year-$month-01")));

$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();
$pdf->SetFont('helvetica', 'B', 14);

// Cabeçalho
$pdf->Cell(0, 10, 'Relatório de Clientes - ' . date('F Y', strtotime("$year-$month-01")), 0, 1, 'C');
$pdf->Ln(5);

// Estilo tipo “card”
$pdf->SetFont('helvetica', '', 11);

while ($row = $result->fetch_assoc()) {
    $finalVigencia = (!empty($row['final_vigencia']) && $row['final_vigencia'] !== '0000-00-00')
        ? date('d/m/Y', strtotime($row['final_vigencia']))
        : 'Não informado';

    $valorPago = number_format($row['premio_liquido'], 2, ',', '.');
    $comissao = number_format(($row['premio_liquido'] * $row['comissao']) / 100, 2, ',', '.');

    $cardContent = "Nome: " . strtoupper($row['nome']) . "\n"
        . "Seguradora: " . $row['seguradora'] . "\n"
        . "Tipo de Seguro: " . $row['tipo_seguro'] . "\n"
        . "Final da Vigência: " . $finalVigencia . "\n"
        . "Status: " . $row['status'] . "\n"
        . "Valor Pago: R$ " . $valorPago . "\n"
        . "Comissão Gerada: R$ " . $comissao;

    $pdf->MultiCell(0, 6, $cardContent, 1, 'L', false);
    $pdf->Ln(5);
}

// Saída do PDF
$pdf->Output('Relatorio_Clientes_' . $month . '_' . $year . '.pdf', 'D');
?>
