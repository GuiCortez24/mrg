<?php
require_once('vendor/tecnickcom/tcpdf/tcpdf.php');
include '../db.php';

// ----------------------------
// Parâmetros de entrada
// ----------------------------
$month = isset($_GET['month']) ? $_GET['month'] : '';
$year  = isset($_GET['year'])  ? $_GET['year']  : date('Y');

if (!$month) {
    die("Parâmetro de mês inválido.");
}

// ----------------------------
// Consulta ao banco de dados
// ----------------------------
$sql = "SELECT 
            seguradora, 
            tipo_seguro, 
            SUM(premio_liquido) AS total_premio_liquido, 
            SUM(premio_liquido * (comissao / 100)) AS total_comissao, 
            COUNT(*) AS total_clientes,
            SUM(CASE WHEN status = 'Emitida' THEN 1 ELSE 0 END) AS apolices_emitidas,
            SUM(CASE WHEN status = 'Cancelada' THEN 1 ELSE 0 END) AS apolices_canceladas
        FROM clientes
        WHERE MONTH(inicio_vigencia) = '$month' AND YEAR(inicio_vigencia) = '$year'
        GROUP BY seguradora, tipo_seguro
        ORDER BY seguradora, tipo_seguro";

$result = $conn->query($sql);

// ----------------------------
// Processamento dos totais
// ----------------------------
$totalPremioLiquido = 0;
$totalComissao      = 0;
$totalEmitidas      = 0;
$totalCanceladas    = 0;
$seguradorasSet     = [];
$tiposSeguroSet     = [];

while ($row = $result->fetch_assoc()) {
    $totalPremioLiquido += $row['total_premio_liquido'];
    $totalComissao      += $row['total_comissao'];
    $totalEmitidas      += $row['apolices_emitidas'];
    $totalCanceladas    += $row['apolices_canceladas'];
    $seguradorasSet[$row['seguradora']] = true;
    $tiposSeguroSet[$row['tipo_seguro']] = true;
}

// Limpa o buffer para evitar conflito de saída
ob_clean();

// ----------------------------
// Criação do PDF
// ----------------------------
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Sistema de Seguros');
$pdf->SetTitle("Análise Produção Mensal da MRG - $month/$year");
$pdf->SetMargins(15, 20, 15);
$pdf->SetAutoPageBreak(TRUE, 20);
$pdf->AddPage();

// ----------------------------
// Título do PDF
// ----------------------------
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');
$nomeMes = strftime('%B', mktime(0,0,0,$month,1));
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 12, "Análise Produção Mensal da MRG do Ano $year - " . ucfirst($nomeMes), 0, 1, 'C');
$pdf->Ln(5);

// ----------------------------
// Tabela de Dados
// ----------------------------
if ($result->num_rows > 0) {
    // Cabeçalho em verde claro
    $pdf->SetFillColor(144, 238, 144); // verde claro
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(40, 10, 'Seguradora', 1, 0, 'C', 1);
    $pdf->Cell(40, 10, 'Tipo de Seguro', 1, 0, 'C', 1);
    $pdf->Cell(40, 10, 'Prêmio Líquido', 1, 0, 'C', 1);
    $pdf->Cell(40, 10, 'Comissão', 1, 0, 'C', 1);
    $pdf->Cell(30, 10, 'Clientes', 1, 1, 'C', 1);

    // Linhas da tabela
    $pdf->SetFont('helvetica', '', 12);
    $pdf->SetTextColor(0,0,0);
    $result->data_seek(0);
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(40, 8, $row['seguradora'], 1, 0, 'C', 0);
        $pdf->Cell(40, 8, $row['tipo_seguro'], 1, 0, 'C', 0);
        $pdf->Cell(40, 8, 'R$ ' . number_format($row['total_premio_liquido'], 2, ',', '.'), 1, 0, 'C', 0);
        $pdf->Cell(40, 8, 'R$ ' . number_format($row['total_comissao'], 2, ',', '.'), 1, 0, 'C', 0);
        $pdf->Cell(30, 8, $row['total_clientes'], 1, 1, 'C', 0);
    }

    // Resumo adicional
    $pdf->Ln(5);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 8, 'Resumo Adicional', 0, 1);
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 6, 'Total Prêmio Líquido do Mês: R$ ' . number_format($totalPremioLiquido, 2, ',', '.'), 0, 1);
    $pdf->Cell(0, 6, 'Total Comissão do Mês: R$ ' . number_format($totalComissao, 2, ',', '.'), 0, 1);
    $pdf->Cell(0, 6, 'Total de Seguradoras: ' . count($seguradorasSet), 0, 1);
    $pdf->Cell(0, 6, 'Total de Tipos de Seguro: ' . count($tiposSeguroSet), 0, 1);
    $pdf->Cell(0, 6, 'Apólices Emitidas: ' . $totalEmitidas, 0, 1);
    $pdf->Cell(0, 6, 'Apólices Canceladas: ' . $totalCanceladas, 0, 1);

} else {
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, 'Nenhum cliente encontrado para este mês.', 0, 1);
}

// ----------------------------
// Fechamento
// ----------------------------
$conn->close();

// ----------------------------
// Exibe PDF no navegador
// ----------------------------
$pdf->Output("Analise_Producao_Mensal_{$month}_{$year}.pdf", 'I');
exit;
?>
