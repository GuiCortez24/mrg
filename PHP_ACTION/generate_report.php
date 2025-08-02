<?php
ob_start();

require_once('../db.php');
require_once('vendor/tecnickcom/tcpdf/tcpdf.php');

// --- 1. VERIFICAÇÃO DE PARÂMETROS ---
if (!isset($_GET['reportType']) || !isset($_GET['startDate']) || !isset($_GET['endDate'])) {
    die("Todos os parâmetros (reportType, startDate, endDate) são necessários.");
}

$reportType = $_GET['reportType'];
$startDateParam = $_GET['startDate'];
$endDateParam = $_GET['endDate'];

// Formata as datas para exibição no cabeçalho do PDF
$startDateFormatted = date('d/m/Y', strtotime($startDateParam));
$endDateFormatted = date('d/m/Y', strtotime($endDateParam));

// --- 2. CONSULTA AO BANCO DE DADOS (AJUSTADA) ---
$stmt = $conn->prepare("
    SELECT 
        nome, 
        seguradora, 
        inicio_vigencia, 
        final_vigencia, 
        numero, 
        item_segurado,
        item_identificacao,
        premio_liquido, 
        status 
    FROM clientes 
    WHERE 
        (
            (inicio_vigencia BETWEEN ? AND ?) OR (final_vigencia BETWEEN ? AND ?)
        )
        AND (final_vigencia IS NULL OR final_vigencia != '0000-00-00')
        AND status != 'Cancelado'
    ORDER BY inicio_vigencia ASC, seguradora ASC
");

$stmt->bind_param("ssss", $startDateParam, $endDateParam, $startDateParam, $endDateParam);
$stmt->execute();
$result = $stmt->get_result();

$totalPremioLiquido = 0;
$totalClientes = 0;

// --- 3. CRIAÇÃO DO PDF ---
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('MRG Seguros');
$pdf->SetTitle("Relatório de $reportType");
$pdf->SetSubject("Relatório de Produção de $startDateFormatted a $endDateFormatted");

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(true);

$pdf->SetAutoPageBreak(true, 15);
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage('L'); // 'L' para Paisagem (Landscape), ideal para tabelas largas

// --- Título do Relatório ---
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, "MRG Seguros - Relatório de $reportType", 0, 1, 'C');
$pdf->SetFont('helvetica', '', 11);
$pdf->Cell(0, 5, "Período: $startDateFormatted a $endDateFormatted", 0, 1, 'C');
$pdf->Ln(8);

// --- 4. CONSTRUÇÃO DO HTML PARA A TABELA ---

// Estilos CSS para a tabela
$html = '<style>
    table {
        border-collapse: collapse;
        width: 100%;
        font-size: 9pt;
    }
    th, td {
        border: 1px solid #dee2e6;
        padding: 6px;
        text-align: left;
    }
    th {
        background-color: #f2f2f2;
        font-weight: bold;
        text-align: center;
    }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .summary-table { font-size: 11pt; width: 50%; margin-top: 20px; }
    .summary-table td { border: 1px solid #dee2e6; }
</style>';

// Cabeçalho da tabela principal
$html .= '<table>
    <thead>
        <tr>
            <th>Cliente</th>
            <th>Telefone</th>
            <th>Item Segurado</th>
            <th>Identificação</th>
            <th>Seguradora</th>
            <th class="text-center">Início Vig.</th>
            <th class="text-center">Final Vig.</th>
            <th>Status</th>
            <th class="text-right">Prêmio Líquido</th>
        </tr>
    </thead>
    <tbody>';

// Loop para preencher as linhas da tabela com dados do banco
while ($row = $result->fetch_assoc()) {
    $totalClientes++;
    $totalPremioLiquido += $row['premio_liquido'];

    // Tratamento de datas e formatação de valores
    $inicioVigencia = date('d/m/Y', strtotime($row['inicio_vigencia']));
    
    $finalVigencia = (!empty($row['final_vigencia']) && $row['final_vigencia'] !== '0000-00-00')
        ? date('d/m/Y', strtotime($row['final_vigencia']))
        : 'N/A';
    
    $premioFormatado = 'R$ ' . number_format($row['premio_liquido'], 2, ',', '.');

    // Adiciona uma linha (<tr>) para cada cliente
    $html .= '<tr>';
    $html .= '<td>' . htmlspecialchars($row['nome']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['numero']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['item_segurado']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['item_identificacao']) . '</td>';
    $html .= '<td>' . htmlspecialchars($row['seguradora']) . '</td>';
    $html .= '<td class="text-center">' . $inicioVigencia . '</td>';
    $html .= '<td class="text-center">' . $finalVigencia . '</td>';
    $html .= '<td>' . htmlspecialchars($row['status']) . '</td>';
    $html .= '<td class="text-right">' . $premioFormatado . '</td>';
    $html .= '</tr>';
}

$html .= '</tbody></table>'; // Fecha a tabela de dados

// --- 5. TABELA DE RESUMO ---
$html .= '<br><br>'; // Espaçamento
$html .= '<table class="summary-table" align="right">
    <tr>
        <td colspan="2" style="text-align:center; background-color:#f2f2f2;"><b>Resumo da Produção</b></td>
    </tr>
    <tr>
        <td><b>Total de Clientes:</b></td>
        <td class="text-right">' . $totalClientes . '</td>
    </tr>
    <tr>
        <td><b>Total do Prêmio Líquido:</b></td>
        <td class="text-right"><b>R$ ' . number_format($totalPremioLiquido, 2, ',', '.') . '</b></td>
    </tr>
</table>';


// --- 6. RENDERIZA O HTML NO PDF ---
$pdf->SetFont('helvetica', '', 10);
$pdf->writeHTML($html, true, false, true, false, '');


// --- 7. FINALIZA E ENVIA O PDF PARA O NAVEGADOR ---
ob_end_clean(); // Limpa o buffer de saída para evitar erros

// Alterado de 'D' para 'I' para abrir no navegador
$pdf->Output("Relatorio_{$reportType}_{$startDateParam}_a_{$endDateParam}.pdf", 'I'); 

exit;
?>