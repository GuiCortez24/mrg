<?php
/**
 * Localização: /PHP_ACTION/generate_report.php
 * Gera um relatório de apólices cuja data de início OU fim de vigência
 * estejam dentro do período selecionado.
 */

require_once 'vendor/autoload.php';
session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    die("Acesso negado.");
}

// 1. Obter e validar os parâmetros
if (!isset($_GET['reportType'], $_GET['startDate'], $_GET['endDate'])) {
    die("Parâmetros de relatório insuficientes.");
}
$reportType = $_GET['reportType'];
$startDateParam = $_GET['startDate'];
$endDateParam = $_GET['endDate'];

// 2. Buscar os dados do banco com a nova lógica
// ================================================================
// AJUSTE CRÍTICO: A consulta foi reescrita para buscar apólices
// onde o INÍCIO OU o FIM da vigência estejam no período.
// ================================================================
$calculated_end_date = "COALESCE(final_vigencia, DATE_ADD(inicio_vigencia, INTERVAL 1 YEAR))";

$sql = "SELECT nome, seguradora, inicio_vigencia, final_vigencia, numero, email, tipo_seguro, premio_liquido, status, item_identificacao, item_segurado 
        FROM clientes 
        WHERE 
            (
                (inicio_vigencia BETWEEN ? AND ?)
                OR 
                ($calculated_end_date BETWEEN ? AND ?)
            )
            AND status != 'Cancelado'
        ORDER BY inicio_vigencia ASC, seguradora ASC";

$stmt = $conn->prepare($sql);
// Agora precisamos passar os parâmetros 4 vezes para cobrir as duas condições do OR
$stmt->bind_param("ssss", $startDateParam, $endDateParam, $startDateParam, $endDateParam);
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
        $this->Cell(0, 15, 'Relatório de Movimentação', 0, false, 'C', 0, '', 0, false, 'M', 'M');
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
$pdf->periodo = 'Período: ' . date('d/m/Y', strtotime($startDateParam)) . ' a ' . date('d/m/Y', strtotime($endDateParam));
$pdf->SetCreator('MRG Seguros System');
$pdf->SetAuthor($_SESSION['user_nome']);
$pdf->SetTitle('Relatório de Movimentação');
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

$header = ['Cliente', 'Telefone', 'Item Segurado', 'Placa / ID', 'Seguradora', 'Início Vig.', 'Final Vig.', 'Prêmio'];
$widths = [60, 25, 50, 40, 25, 25, 25, 25];

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
        // Lógica para determinar a data final real para exibição (mesma lógica da query)
        $finalVigenciaReal = $cliente['final_vigencia'] && $cliente['final_vigencia'] !== '0000-00-00' ? $cliente['final_vigencia'] : date('Y-m-d', strtotime($cliente['inicio_vigencia'] . ' +1 year'));
        
        $row_data = [
            htmlspecialchars($cliente['nome']),
            htmlspecialchars($cliente['numero']),
            htmlspecialchars($cliente['item_segurado']),
            htmlspecialchars($cliente['item_identificacao']),
            htmlspecialchars($cliente['seguradora']),
            date('d/m/Y', strtotime($cliente['inicio_vigencia'])),
            date('d/m/Y', strtotime($finalVigenciaReal)),
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
$filename = "Relatorio_Movimentacao.pdf";
$pdf->Output($filename, 'I');

exit();
?>