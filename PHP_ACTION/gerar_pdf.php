<?php
include '../db.php';
require_once('vendor/tecnickcom/tcpdf/tcpdf.php');

$month = isset($_GET['month']) ? $_GET['month'] : date('m');
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');

// Consulta SQL para buscar dados dos clientes
$sql = "SELECT nome, seguradora, tipo_seguro FROM clientes WHERE MONTH(inicio_vigencia) = '$month' AND YEAR(inicio_vigencia) = '$year'";
$result = $conn->query($sql);

// Inicia o PDF com TCPDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Seu Nome');
$pdf->SetTitle('Relatório de Clientes');
$pdf->SetSubject('Clientes - ' . date('F Y', strtotime("$year-$month-01")));

// Configurações do layout da página
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();
$pdf->SetFont('helvetica', 'B', 12);

// Cabeçalho
$pdf->Cell(0, 10, 'Relatório de Clientes - ' . date('F Y', strtotime("$year-$month-01")), 0, 1, 'C');
$pdf->Ln(10);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(60, 10, 'Nome', 1, 0, 'C');
$pdf->Cell(60, 10, 'Seguradora', 1, 0, 'C');
$pdf->Cell(60, 10, 'Tipo de Seguro', 1, 1, 'C');

// Adiciona os dados dos clientes
$pdf->SetFont('helvetica', '', 10);
while ($row = $result->fetch_assoc()) {
    $pdf->MultiCell(60, 10, utf8_decode($row['nome']), 1, 'L', 0, 0);
    $pdf->MultiCell(60, 10, utf8_decode($row['seguradora']), 1, 'L', 0, 0);
    $pdf->MultiCell(60, 10, utf8_decode($row['tipo_seguro']), 1, 'L', 0, 1);
}

// Exibe ou faz o download do PDF
$pdf->Output('Relatorio_Clientes_' . $month . '_' . $year . '.pdf', 'D');
?>
