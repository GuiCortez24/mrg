<?php
/**
 * Localização: /PHP_ACTION/generate__pdf.php
 * Gera um PDF de resumo do mês com tabela e gráficos de pizza em formato JPG.
 */

require_once 'vendor/autoload.php';
session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    die("Acesso negado.");
}

// 1. Obter e validar parâmetros
$month = $_GET['month'] ?? date('m');
$year = $_GET['year'] ?? date('Y');
$months_map = [
    '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril', '05' => 'Maio', '06' => 'Junho', 
    '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
];
$month_name = $months_map[$month] ?? 'Mês Inválido';

// 2. Buscar e processar os dados
$sql = "SELECT seguradora, tipo_seguro, SUM(premio_liquido) as total_premio, COUNT(id) as total_clientes 
        FROM clientes 
        WHERE MONTH(inicio_vigencia) = ? AND YEAR(inicio_vigencia) = ? AND status != 'Cancelado'
        GROUP BY seguradora, tipo_seguro";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $month, $year);
$stmt->execute();
$result = $stmt->get_result();
$data_agrupada = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Processa os dados em arrays para os gráficos
$premio_por_seguradora = [];
$clientes_por_tipo = [];
foreach ($data_agrupada as $row) {
    @$premio_por_seguradora[$row['seguradora']] += $row['total_premio'];
    @$clientes_por_tipo[$row['tipo_seguro']] += $row['total_clientes'];
}

// 3. Gerar as URLs dos Gráficos com QuickChart.io
function getChartUrl($title, $data_array) {
    if (empty($data_array)) return null;
    $config = [
        'type' => 'pie',
        'data' => [
            'labels' => array_keys($data_array),
            'datasets' => [[ 'data' => array_values($data_array) ]]
        ],
        'options' => [
            'title' => ['display' => true, 'text' => $title],
            'legend' => ['position' => 'right']
        ]
    ];
    
    // ================================================================
    // CORREÇÃO 1: Adicionado o parâmetro '&f=jpeg' para pedir um JPG
    // ================================================================
    return 'https://quickchart.io/chart?f=jpeg&c=' . urlencode(json_encode($config)) . '&backgroundColor=white&width=400&height=250';
}

$chart_url1 = getChartUrl('Prêmio por Seguradora', $premio_por_seguradora);
$chart_url2 = getChartUrl('Clientes por Tipo de Seguro', $clientes_por_tipo);


// 4. Estender a classe TCPDF para cabeçalho/rodapé
class MYPDF extends TCPDF {
    public $periodo = '';
    public function Header() { /* ... (código inalterado) ... */ }
    public function Footer() { /* ... (código inalterado) ... */ }
}

// 5. Iniciar e configurar o PDF
$pdf = new MYPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
$pdf->periodo = 'Resumo de ' . $month_name . ' de ' . $year;
$pdf->SetCreator('MRG Seguros System');
$pdf->SetAuthor($_SESSION['user_nome']);
$pdf->SetTitle('Resumo de Produção');
$pdf->SetMargins(15, 30, 15);
$pdf->SetAutoPageBreak(TRUE, 25);
$pdf->AddPage();

// 6. Adicionar os gráficos no PDF
$pdf->SetFont('dejavusans', 'B', 12);
$pdf->Cell(0, 8, '📊 Análise Gráfica', 0, 1, 'L');
if ($chart_url1 && $chart_url2) {
    $img_data1 = @file_get_contents($chart_url1);
    $img_data2 = @file_get_contents($chart_url2);

    // ================================================================
    // CORREÇÃO 2: Alterado o tipo de imagem de 'PNG' para 'JPG'
    // ================================================================
    if ($img_data1) $pdf->Image('@'.$img_data1, 15, null, 90, 0, 'JPG', '', '', true, 150);
    if ($img_data2) $pdf->Image('@'.$img_data2, 105, null, 90, 0, 'JPG', '', '', true, 150);
    $pdf->Ln(70);
} else {
    $pdf->SetFont('dejavusans', '', 10);
    $pdf->Cell(0, 8, 'Não há dados suficientes para gerar os gráficos.', 0, 1, 'L');
    $pdf->Ln(5);
}

// 7. Desenhar a tabela de resumo
$pdf->SetFont('dejavusans', 'B', 12);
$pdf->Cell(0, 8, '📋 Dados Detalhados', 0, 1, 'L');
$pdf->SetFont('dejavusans', 'B', 9);
$pdf->SetFillColor(240, 240, 240);
$header = ['🏢 Seguradora', '🛡️ Tipo de Seguro', '💰 Total Prêmio', '👥 Total Clientes'];
$widths = [60, 60, 30, 30];
for($i=0; $i<count($header); ++$i) $pdf->Cell($widths[$i], 7, $header[$i], 1, 0, 'C', 1);
$pdf->Ln();

$pdf->SetFont('dejavusans', '', 8);
$pdf->SetFillColor(255);
if (count($data_agrupada) > 0) {
    foreach ($data_agrupada as $row) {
        $pdf->Cell($widths[0], 6, htmlspecialchars($row['seguradora']), 1, 0, 'L', 1);
        $pdf->Cell($widths[1], 6, htmlspecialchars($row['tipo_seguro']), 1, 0, 'L', 1);
        $pdf->Cell($widths[2], 6, 'R$ ' . number_format($row['total_premio'], 2, ',', '.'), 1, 0, 'R', 1);
        $pdf->Cell($widths[3], 6, $row['total_clientes'], 1, 1, 'C', 1);
    }
} else {
    $pdf->Cell(array_sum($widths), 10, 'Nenhum registro encontrado.', 1, 1, 'C', 1);
}

// 8. Finalizar e enviar o PDF
$filename = "resumo_producao_" . strtolower($month_name) . "_" . $year . ".pdf";
$pdf->Output($filename, 'I');
exit();