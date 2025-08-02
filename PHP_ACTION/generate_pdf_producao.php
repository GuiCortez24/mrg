<?php
/**
 * Localização: /PHP_ACTION/generate_pdf_producao.php
 * Gera um relatório em PDF da produção do mês com visual aprimorado e todos os campos solicitados.
 */

require_once 'vendor/autoload.php';
session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    die("Acesso negado.");
}

// 1. Obter e validar os parâmetros
$month = $_GET['month'] ?? date('m');
$year = $_GET['year'] ?? date('Y');
$months_map = [
    '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril', '05' => 'Maio', '06' => 'Junho', 
    '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
];
$month_name = $months_map[$month] ?? 'Mês Inválido';

// 2. Buscar os dados do banco
// ================================================================
// ALTERADO: Adicionado o campo `status` na consulta
// ================================================================
$sql = "SELECT nome, cpf, apolice, seguradora, inicio_vigencia, final_vigencia, premio_liquido, item_segurado, item_identificacao, status 
        FROM clientes 
        WHERE MONTH(inicio_vigencia) = ? AND YEAR(inicio_vigencia) = ?
        ORDER BY inicio_vigencia ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $month, $year);
$stmt->execute();
$result = $stmt->get_result();
$clientes = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();


// 3. Estender a classe TCPDF para criar um Cabeçalho e Rodapé personalizados
class MYPDF extends TCPDF {
    public $periodo = '';

    public function Header() {
        $this->SetFont('dejavusans', 'B', 20);
        $this->SetTextColor(80, 80, 80);
        $this->Cell(0, 15, 'Relatório de Produção', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->SetFont('dejavusans', 'I', 10);
        $this->Cell(0, 15, $this->periodo, 0, true, 'R', 0, '', 0, false, 'M', 'M');
        $this->Line(15, 25, $this->getPageWidth() - 15, 25);
    }

    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('dejavusans', 'I', 8);
        $this->SetTextColor(128, 128, 128);
        $this->Cell(0, 10, 'Página ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
        $this->Cell(0, 10, 'Gerado em: ' . date('d/m/Y H:i'), 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }
}

// 4. Iniciar e configurar o documento PDF
$pdf = new MYPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);
$pdf->periodo = 'Apólices de ' . $month_name . ' de ' . $year;
$pdf->SetCreator('MRG Seguros System');
$pdf->SetAuthor($_SESSION['user_nome']);
$pdf->SetTitle('Relatório de Produção');
$pdf->SetMargins(10, 30, 10);
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(15);
$pdf->SetAutoPageBreak(TRUE, 25);

$pdf->AddPage();

// 5. Desenhar o cabeçalho da tabela
$pdf->SetFont('dejavusans', 'B', 9);
$pdf->SetFillColor(240, 240, 240);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(200, 200, 200);
$pdf->SetLineWidth(0.3);

// ================================================================
// ALTERADO: Cabeçalho da tabela atualizado com "Status"
// ================================================================
$header = ['Cliente', 'Item Segurado', 'Placa / ID', 'Seguradora', 'Início Vig.', 'Final Vig.', 'Status', 'Prêmio'];
$widths = [60, 45, 30, 35, 25, 25, 25, 25]; // Larguras reajustadas

for($i = 0; $i < count($header); ++$i) {
    $pdf->Cell($widths[$i], 7, $header[$i], 1, 0, 'C', 1);
}
$pdf->Ln();

// 6. Preencher a tabela com os dados
$pdf->SetFont('dejavusans', '', 8);
$pdf->SetFillColor(255);
$totalPremioLiquido = 0;

if (count($clientes) > 0) {
    foreach ($clientes as $cliente) {
        $totalPremioLiquido += $cliente['premio_liquido'];
        $finalVigenciaReal = $cliente['final_vigencia'] ?? date('Y-m-d', strtotime($cliente['inicio_vigencia'] . ' +1 year'));
        
        // ================================================================
        // ALTERADO: Adicionado `status` ao array de dados da linha
        // ================================================================
        $row_data = [
            htmlspecialchars($cliente['nome']),
            htmlspecialchars($cliente['item_segurado']),
            htmlspecialchars($cliente['item_identificacao']),
            htmlspecialchars($cliente['seguradora']),
            date('d/m/Y', strtotime($cliente['inicio_vigencia'])),
            date('d/m/Y', strtotime($finalVigenciaReal)),
            htmlspecialchars($cliente['status']),
            'R$ ' . number_format($cliente['premio_liquido'], 2, ',', '.')
        ];

        $rowHeight = 0;
        for ($i = 0; $i < count($widths); ++$i) {
            $rowHeight = max($rowHeight, $pdf->getStringHeight($widths[$i], $row_data[$i]));
        }
        
        $pdf->MultiCell($widths[0], $rowHeight, $row_data[0], 1, 'L', 1, 0);
        $pdf->MultiCell($widths[1], $rowHeight, $row_data[1], 1, 'L', 1, 0);
        $pdf->MultiCell($widths[2], $rowHeight, $row_data[2], 1, 'L', 1, 0);
        $pdf->MultiCell($widths[3], $rowHeight, $row_data[3], 1, 'L', 1, 0);
        $pdf->MultiCell($widths[4], $rowHeight, $row_data[4], 1, 'C', 1, 0);
        $pdf->MultiCell($widths[5], $rowHeight, $row_data[5], 1, 'C', 1, 0);
        $pdf->MultiCell($widths[6], $rowHeight, $row_data[6], 1, 'C', 1, 0);
        $pdf->MultiCell($widths[7], $rowHeight, $row_data[7], 1, 'R', 1, 1);
    }
} else {
    $pdf->Cell(array_sum($widths), 10, 'Nenhum registro encontrado para este período.', 1, 1, 'C', 1);
}

// 7. Adicionar o sumário no final
$pdf->Ln(5);
$pdf->SetFont('dejavusans', 'B', 10);
$pdf->Cell(0, 8, 'Resumo do Período', 0, 1, 'L');
$pdf->SetFont('dejavusans', '', 10);
$pdf->Cell(0, 6, 'Total de Apólices no Período: ' . count($clientes), 0, 1, 'L');
$pdf->Cell(0, 6, 'Soma Total do Prêmio Líquido: R$ ' . number_format($totalPremioLiquido, 2, ',', '.'), 0, 1, 'L');

// 8. Finalizar e enviar o PDF
$filename = "relatorio_producao_" . strtolower($month_name) . "_" . $year . ".pdf";
$pdf->Output($filename, 'I');

exit();
?>