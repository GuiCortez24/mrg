<?php
require_once('vendor/tecnickcom/tcpdf/tcpdf.php');
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../INCLUDES/functions.php'; // Garante acesso à função hasPermission

// ===================================================================
// 1. AJUSTE DE SEGURANÇA: VERIFICAÇÃO DE PERMISSÃO
// ===================================================================
// Verifica se o usuário tem permissão para ver dados de comissão.
if (!hasPermission('pode_ver_comissao_total')) {
    // Se não tiver, interrompe a execução com uma mensagem de erro.
    http_response_code(403); // Código de "Acesso Proibido"
    die('Acesso negado. Você não tem permissão para gerar este relatório.');
}

// ----------------------------
// Parâmetros de entrada
// ----------------------------
$month = $_GET['month'] ?? '';
$year  = $_GET['year']  ?? date('Y');

if (empty($month) || !checkdate($month, 1, $year)) {
    die("Parâmetro de mês inválido.");
}

// ===================================================================
// 2. AJUSTE DE SEGURANÇA: PROTEÇÃO CONTRA SQL INJECTION
// ===================================================================
$sql = "SELECT 
            seguradora, 
            tipo_seguro, 
            SUM(premio_liquido) AS total_premio_liquido, 
            SUM(premio_liquido * (comissao / 100)) AS total_comissao, 
            COUNT(*) AS total_clientes,
            SUM(CASE WHEN status = 'Emitida' THEN 1 ELSE 0 END) AS apolices_emitidas,
            SUM(CASE WHEN status = 'Cancelado' THEN 1 ELSE 0 END) AS apolices_canceladas
        FROM clientes
        WHERE MONTH(inicio_vigencia) = ? AND YEAR(inicio_vigencia) = ?
        GROUP BY seguradora, tipo_seguro
        ORDER BY seguradora, tipo_seguro";

// Usando prepared statements para segurança
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $month, $year);
$stmt->execute();
$result = $stmt->get_result();
// ===================================================================

// ----------------------------
// Processamento dos totais
// ----------------------------
$totalPremioLiquido = 0;
$totalComissao      = 0;
$totalEmitidas      = 0;
$totalCanceladas    = 0;
$totalClientes      = 0;
$seguradorasSet     = [];
$tiposSeguroSet     = [];

// Criamos um array com os dados para não precisar re-executar a query
$data = $result->fetch_all(MYSQLI_ASSOC);

foreach ($data as $row) {
    $totalPremioLiquido += $row['total_premio_liquido'];
    $totalComissao      += $row['total_comissao'];
    $totalEmitidas      += $row['apolices_emitidas'];
    $totalCanceladas    += $row['apolices_canceladas'];
    $totalClientes      += $row['total_clientes'];
    $seguradorasSet[$row['seguradora']] = true;
    $tiposSeguroSet[$row['tipo_seguro']] = true;
}

// Limpa o buffer para evitar conflito de saída
if (ob_get_level()) {
    ob_end_clean();
}

// ----------------------------
// Criação do PDF
// ----------------------------
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Sistema MRG Seguros');
$pdf->SetTitle("Análise Produção Mensal da MRG - $month/$year");
$pdf->SetMargins(15, 20, 15);
$pdf->SetAutoPageBreak(TRUE, 20);
$pdf->AddPage();

// ----------------------------
// Título do PDF
// ----------------------------
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');
$nomeMes = ucfirst(strftime('%B', mktime(0, 0, 0, $month, 1)));
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 12, "Análise Produção Mensal da MRG - $nomeMes de $year", 0, 1, 'C');
$pdf->Ln(5);

// ----------------------------
// Tabela de Dados
// ----------------------------
if (count($data) > 0) {
    // Cabeçalho
    $pdf->SetFillColor(220, 230, 240); // Azul claro suave
    $pdf->SetTextColor(0);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(45, 7, 'Seguradora', 1, 0, 'C', 1);
    $pdf->Cell(45, 7, 'Tipo de Seguro', 1, 0, 'C', 1);
    $pdf->Cell(35, 7, 'Prêmio Líquido', 1, 0, 'C', 1);
    $pdf->Cell(35, 7, 'Comissão', 1, 0, 'C', 1);
    $pdf->Cell(20, 7, 'Apólices', 1, 1, 'C', 1);

    // Linhas da tabela
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetFillColor(255);
    $fill = 0;
    foreach ($data as $row) {
        $pdf->Cell(45, 6, $row['seguradora'], 'LR', 0, 'L', $fill);
        $pdf->Cell(45, 6, $row['tipo_seguro'], 'LR', 0, 'L', $fill);
        $pdf->Cell(35, 6, 'R$ ' . number_format($row['total_premio_liquido'], 2, ',', '.'), 'LR', 0, 'R', $fill);
        $pdf->Cell(35, 6, 'R$ ' . number_format($row['total_comissao'], 2, ',', '.'), 'LR', 0, 'R', $fill);
        $pdf->Cell(20, 6, $row['total_clientes'], 'LR', 1, 'C', $fill);
        $fill = !$fill;
    }
    $pdf->Cell(180, 0, '', 'T'); // Linha final da tabela
    $pdf->Ln(10);

    // Resumo adicional
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 8, 'Resumo Geral do Mês', 0, 1);
    $pdf->SetFont('helvetica', '', 11);
    $pdf->Cell(0, 7, 'Total Prêmio Líquido: R$ ' . number_format($totalPremioLiquido, 2, ',', '.'), 0, 1);
    $pdf->Cell(0, 7, 'Total Comissão: R$ ' . number_format($totalComissao, 2, ',', '.'), 0, 1);
    $pdf->Cell(0, 7, 'Total de Apólices (Clientes): ' . $totalClientes, 0, 1);
    $pdf->Ln(2);
    $pdf->Cell(0, 7, 'Total de Seguradoras Envolvidas: ' . count($seguradorasSet), 0, 1);
    $pdf->Cell(0, 7, 'Total de Ramos Envolvidos: ' . count($tiposSeguroSet), 0, 1);
    $pdf->Ln(2);
    $pdf->Cell(0, 7, 'Apólices Emitidas no Período: ' . $totalEmitidas, 0, 1);
    $pdf->Cell(0, 7, 'Apólices Canceladas no Período: ' . $totalCanceladas, 0, 1);

} else {
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, 'Nenhum dado de produção encontrado para este mês.', 0, 1);
}

// ----------------------------
// Fechamento
// ----------------------------
$stmt->close();
$conn->close();

// ----------------------------
// Exibe PDF no navegador
// ----------------------------
$pdf->Output("Analise_Producao_Mensal_{$month}_{$year}.pdf", 'I');
exit;
?>