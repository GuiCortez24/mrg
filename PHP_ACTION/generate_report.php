<?php
// Iniciar buffer de saída para evitar que qualquer saída interfira na geração do PDF
ob_start();

require_once('../db.php');
require_once('vendor/tecnickcom/tcpdf/tcpdf.php'); // Ajuste o caminho conforme necessário

// Verificar se os parâmetros foram passados
if (!isset($_GET['reportType']) || !isset($_GET['startDate']) || !isset($_GET['endDate'])) {
    die("Todos os parâmetros são necessários.");
}

$reportType = $_GET['reportType'];
$startDate = date('d/m/Y', strtotime($_GET['startDate']));
$endDate = date('d/m/Y', strtotime($_GET['endDate']));

// Obter dados do banco com base no período e no tipo de relatório
$stmt = $conn->prepare("SELECT nome, seguradora, inicio_vigencia, numero, email, tipo_seguro, premio_liquido 
                        FROM clientes 
                        WHERE inicio_vigencia BETWEEN ? AND ? 
                        ORDER BY inicio_vigencia ASC, seguradora ASC");
$stmt->bind_param("ss", $_GET['startDate'], $_GET['endDate']);
$stmt->execute();
$result = $stmt->get_result();

// Calcular o total de produção
$totalPremioLiquido = 0;
$totalClientes = $result->num_rows;

// Configuração do PDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetAutoPageBreak(true, 10);
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage('P'); // 'P' define a orientação vertical
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, "Relatório de $reportType - Período: $startDate a $endDate", 0, 1, 'C');
$pdf->Ln(10);
$pdf->SetFont('helvetica', '', 12);

// Adicionar dados do cliente organizados como cartões
while ($row = $result->fetch_assoc()) {
    $totalPremioLiquido += $row['premio_liquido'];

    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 8, "Cliente: " . $row['nome'], 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 11);
    $pdf->MultiCell(0, 6, 
        "Seguradora: " . $row['seguradora'] . "\n" .
        "Início Vigência: " . date('d/m/Y', strtotime($row['inicio_vigencia'])) . "\n" .
        "Telefone: " . $row['numero'] . "\n" .
        "Email: " . $row['email'] . "\n" .
        "Tipo de Seguro: " . $row['tipo_seguro'] . "\n" .
        "Valor Pago: R$ " . number_format($row['premio_liquido'], 2, ',', '.'),
        0, 'L', false
    );
    $pdf->Ln(5); // Espaço entre os cartões de cada cliente
}

// Adicionar resumo da produção no final do PDF
$pdf->Ln(10);
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, "Resumo da Produção do Mês", 0, 1, 'C');
$pdf->SetFont('helvetica', '', 12);
$pdf->MultiCell(0, 10, 
    "Total de Clientes: " . $totalClientes . "\n" .
    "Total do Prêmio Líquido: R$ " . number_format($totalPremioLiquido, 2, ',', '.'),
    0, 'L', false
);

// Fechar buffer de saída para que nenhum conteúdo extra interfira no PDF
ob_end_clean();

// Baixar o PDF
$pdf->Output("Relatorio_{$reportType}_{$startDate}_a_{$endDate}.pdf", 'D');
?>
