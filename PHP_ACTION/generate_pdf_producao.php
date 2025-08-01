<?php
/**
 * Localização: /PHP_ACTION/generate_pdf_producao.php
 * Gera um relatório em PDF da produção do mês com visual aprimorado e ícones corretos.
 */

// CORREÇÃO 1: Caminho ajustado para a estrutura de pastas do projeto
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
$sql = "SELECT nome, cpf, apolice, seguradora, inicio_vigencia, premio_liquido 
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
        $this->SetFont('dejavusans', 'B', 20); // CORREÇÃO 2: Usar fonte com suporte a UTF-8
        $this->SetTextColor(80, 80, 80);
        $this->Cell(0, 15, 'Relatório de Produção', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->SetFont('dejavusans', 'I', 10);
        $this->Cell(0, 15, $this->periodo, 0, true, 'R', 0, '', 0, false, 'M', 'M');
        $this->Line(15, 25, $this->getPageWidth() - 15, 25);
    }

    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('dejavusans', 'I', 8); // CORREÇÃO 2: Usar fonte com suporte a UTF-8
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
$pdf->SetFont('dejavusans', 'B', 9); // CORREÇÃO 2: Usar fonte com suporte a UTF-8
$pdf->SetFillColor(240, 240, 240);
$pdf->SetTextColor(0);
$pdf->SetDrawColor(200, 200, 200);
$pdf->SetLineWidth(0.3);

$header = ['👤 Cliente', '📄 CPF', '📝 Nº Proposta', '🏢 Seguradora', '🗓️ Início', '💰 Prêmio Líquido'];
$widths = [80, 35, 40, 45, 25, 30];

for($i = 0; $i < count($header); ++$i) {
    $pdf->Cell($widths[$i], 7, $header[$i], 1, 0, 'C', 1);
}
$pdf->Ln();

// 6. Preencher a tabela com os dados
$pdf->SetFont('dejavusans', '', 8); // CORREÇÃO 2: Usar fonte com suporte a UTF-8
$pdf->SetFillColor(255);
$total_premio_liquido = 0;

if (count($clientes) > 0) {
    foreach ($clientes as $cliente) {
        $total_premio_liquido += $cliente['premio_liquido'];
        $rowHeight = $pdf->getStringHeight($widths[0], $cliente['nome']);

        $pdf->MultiCell($widths[0], $rowHeight, htmlspecialchars($cliente['nome']), 1, 'L', 1, 0);
        $pdf->MultiCell($widths[1], $rowHeight, htmlspecialchars($cliente['cpf']), 1, 'L', 1, 0);
        $pdf->MultiCell($widths[2], $rowHeight, htmlspecialchars($cliente['apolice']), 1, 'L', 1, 0);
        $pdf->MultiCell($widths[3], $rowHeight, htmlspecialchars($cliente['seguradora']), 1, 'L', 1, 0);
        $pdf->MultiCell($widths[4], $rowHeight, date('d/m/Y', strtotime($cliente['inicio_vigencia'])), 1, 'C', 1, 0);
        $pdf->MultiCell($widths[5], $rowHeight, 'R$ ' . number_format($cliente['premio_liquido'], 2, ',', '.'), 1, 'R', 1, 1);
    }
} else {
    $pdf->Cell(array_sum($widths), 10, 'Nenhum registro encontrado para este período.', 1, 1, 'C', 1);
}

// 7. Adicionar o sumário no final
$pdf->Ln(5);
$pdf->SetFont('dejavusans', 'B', 10); // CORREÇÃO 2: Usar fonte com suporte a UTF-8
$pdf->Cell(0, 8, '📈 Resumo do Período', 0, 1, 'L');
$pdf->SetFont('dejavusans', '', 10);
$pdf->Cell(0, 6, 'Total de Apólices no Período: ' . count($clientes), 0, 1, 'L');
$pdf->Cell(0, 6, 'Soma Total do Prêmio Líquido: R$ ' . number_format($total_premio_liquido, 2, ',', '.'), 0, 1, 'L');

// 8. Finalizar e enviar o PDF
$filename = "relatorio_producao_" . strtolower($month_name) . "_" . $year . ".pdf";
$pdf->Output($filename, 'I');

exit();