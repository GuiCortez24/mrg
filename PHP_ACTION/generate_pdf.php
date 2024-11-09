<?php
require_once('vendor/tecnickcom/tcpdf/tcpdf.php');
include '../db.php';

// Verifique se o mês e o ano foram passados
$month = isset($_GET['month']) ? $_GET['month'] : '';
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');

if (!$month) {
    die("Parâmetro de mês inválido.");
}

// Consulta para obter as seguradoras, tipos de seguro, prêmio líquido e comissão
$sql = "SELECT seguradora, tipo_seguro, SUM(premio_liquido) as total_premio_liquido, SUM(premio_liquido * (comissao / 100)) as total_comissao, COUNT(*) as total_clientes 
        FROM clientes 
        WHERE MONTH(inicio_vigencia) = '$month' AND YEAR(inicio_vigencia) = '$year' 
        GROUP BY seguradora, tipo_seguro";
$result = $conn->query($sql);

// Variáveis para armazenar totais
$total_premio_liquido_mes = 0;
$total_comissao_mes = 0;
$seguradoras = [];
$tipos_seguro = [];

// Processa os dados da tabela e os totais
while ($row = $result->fetch_assoc()) {
    $total_premio_liquido_mes += $row['total_premio_liquido'];
    $total_comissao_mes += $row['total_comissao'];
    $seguradoras[$row['seguradora']] = true;
    $tipos_seguro[$row['tipo_seguro']] = true;
}

// Limpeza do buffer para evitar conflitos de saída
ob_clean();

// Criação do PDF
$pdf = new TCPDF();
$pdf->AddPage();

// Título do PDF
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, "Resumo do Mês: " . date('F', mktime(0, 0, 0, $month, 1)) . " - $year", 0, 1, 'C');
$pdf->Ln(10);

// Verifique se há dados para exibir
if ($result->num_rows > 0) {
    // Cabeçalho da tabela
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(40, 10, 'Seguradora', 1);
    $pdf->Cell(40, 10, 'Tipo de Seguro', 1);
    $pdf->Cell(40, 10, 'Prêmio Líquido', 1);
    $pdf->Cell(40, 10, 'Comissão', 1);
    $pdf->Cell(30, 10, 'Clientes', 1);
    $pdf->Ln();

    // Linhas da tabela
    $pdf->SetFont('helvetica', '', 12);
    $result->data_seek(0); // Reseta o ponteiro para reprocessar os dados na tabela
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(40, 10, $row['seguradora'], 1);
        $pdf->Cell(40, 10, $row['tipo_seguro'], 1);
        $pdf->Cell(40, 10, 'R$ ' . number_format($row['total_premio_liquido'], 2, ',', '.'), 1);
        $pdf->Cell(40, 10, 'R$ ' . number_format($row['total_comissao'], 2, ',', '.'), 1);
        $pdf->Cell(30, 10, $row['total_clientes'], 1);
        $pdf->Ln();
    }

    // Totais e contagem adicional
    $pdf->Ln(10);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Resumo Adicional', 0, 1);
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, 'Total Prêmio Líquido do Mês: R$ ' . number_format($total_premio_liquido_mes, 2, ',', '.'), 0, 1);
    $pdf->Cell(0, 10, 'Total Comissão do Mês: R$ ' . number_format($total_comissao_mes, 2, ',', '.'), 0, 1);
    $pdf->Cell(0, 10, 'Total de Seguradoras: ' . count($seguradoras), 0, 1);
    $pdf->Cell(0, 10, 'Total de Tipos de Seguro: ' . count($tipos_seguro), 0, 1);
} else {
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, 'Nenhum cliente encontrado para este mês.', 0, 1);
}

// Processa os dados para o gráfico de Tipos de Seguro
$tiposSeguroArray = [];
$result->data_seek(0); // Reinicia o ponteiro dos resultados para reutilizar os dados da consulta
while ($row = $result->fetch_assoc()) {
    if (isset($tiposSeguroArray[$row['tipo_seguro']])) {
        $tiposSeguroArray[$row['tipo_seguro']] += $row['total_premio_liquido'];
    } else {
        $tiposSeguroArray[$row['tipo_seguro']] = $row['total_premio_liquido'];
    }
}

// Ordena os dados em ordem decrescente de valor para uma melhor apresentação
arsort($tiposSeguroArray);

// Gráfico de Tipos de Seguro: Valor de Produção do Mês
$pdf->AddPage();
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Produção do Mês - Valor por Tipo de Seguro', 0, 1, 'C');
$pdf->Ln(10);

// Configuração para o gráfico de tipos de seguro
$xPos = 60; // Posição inicial no eixo X
$yPos = 50; // Posição inicial no eixo Y
$barHeight = 8; // Altura das barras
$maxWidth = 100; // Largura máxima da barra
$maxValue = max($tiposSeguroArray); // Valor máximo para escalar as barras
$scale = $maxWidth / $maxValue; // Escala para ajustar as barras ao gráfico

// Exibe cada tipo de seguro com uma barra proporcional ao valor total
foreach ($tiposSeguroArray as $tipoSeguro => $valorProduzido) {
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Text($xPos - 50, $yPos, $tipoSeguro); // Exibe o nome do tipo de seguro

    // Barra horizontal para o valor de produção
    $pdf->SetFillColor(100, 149, 237); // Cor azul clara
    $pdf->Rect($xPos, $yPos, $valorProduzido * $scale, $barHeight, 'DF');
    $pdf->Text($xPos + ($valorProduzido * $scale) + 5, $yPos, 'R$ ' . number_format($valorProduzido, 2, ',', '.'));

    // Avança a posição Y para a próxima barra
    $yPos += 20;
}

// Legenda do Gráfico de Tipos de Seguro
$pdf->Ln(10);
$pdf->SetFillColor(100, 149, 237);
$pdf->Rect(150, $yPos, 5, 5, 'DF');
$pdf->SetXY(155, $yPos);
$pdf->Cell(0, 5, 'Valor Produzido por Tipo de Seguro', 0, 1);

// Fecha a conexão com o banco de dados
$conn->close();

// Gera o PDF para download
$pdf->Output("Resumo_Mes_{$month}_Ano_{$year}.pdf", 'D');
exit;
?>
