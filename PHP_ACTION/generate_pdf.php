<?php
require_once('vendor/tecnickcom/tcpdf/tcpdf.php');
include '../db.php'; // Ajuste o caminho conforme a estrutura do seu projeto

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

// Consulta para contar as apólices canceladas e emitidas
$status_sql = "SELECT status, COUNT(*) as total 
               FROM clientes 
               WHERE MONTH(inicio_vigencia) = '$month' AND YEAR(inicio_vigencia) = '$year' 
               GROUP BY status";
$status_result = $conn->query($status_sql);

// Variáveis para armazenar totais e contagens
$total_premio_liquido_mes = 0;
$total_comissao_mes = 0;
$seguradoras = [];
$tipos_seguro = [];
$canceladas = 0;
$emitidas = 0;

// Variáveis para armazenar dados de produção do mês
$seguradorasArray = [];
$premioArray = [];
$comissaoArray = [];
$clientesArray = [];

// Armazena os dados para o gráfico
while ($row = $result->fetch_assoc()) {
    $seguradorasArray[] = $row['seguradora'];
    $premioArray[] = $row['total_premio_liquido'];
    $comissaoArray[] = $row['total_comissao'];
    $clientesArray[] = $row['total_clientes'];
    $total_premio_liquido_mes += $row['total_premio_liquido'];
    $total_comissao_mes += $row['total_comissao'];
    $seguradoras[$row['seguradora']] = true;
    $tipos_seguro[$row['tipo_seguro']] = true;
}

// Ordena os dados em ordem crescente com base no prêmio líquido
array_multisort($premioArray, SORT_ASC, $seguradorasArray, $comissaoArray, $clientesArray);


// Processa o total de apólices canceladas e emitidas
while ($status_row = $status_result->fetch_assoc()) {
    if ($status_row['status'] == 'Cancelado') {
        $canceladas = $status_row['total'];
    } elseif ($status_row['status'] == 'Emitida') {
        $emitidas = $status_row['total'];
    }
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

// Gráfico 1: Prêmio Líquido e Comissão por Seguradora (Horizontal)
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Produção do Mês - Prêmio Líquido e Comissão por Seguradora', 0, 1, 'C');
$pdf->Ln(5);

$barHeight = 8; // Altura das barras
$xPos = 60; // Posição inicial no eixo X
$yPos = 60; // Posição inicial no eixo Y
$maxWidth = 100; // Largura máxima da barra

$maxValue = max(max($premioArray), max($comissaoArray));
$scale = $maxWidth / $maxValue; // Ajuste de escala

foreach ($seguradorasArray as $index => $seguradora) {
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Text($xPos - 50, $yPos + ($index * 20), $seguradora); // Exibe o nome da seguradora com mais espaço entre as linhas

    // Prêmio líquido (barra azul horizontal)
    $pdf->SetFillColor(0, 102, 204); // Cor azul
    $pdf->Rect($xPos, $yPos + ($index * 20), $premioArray[$index] * $scale, $barHeight, 'DF');
    $pdf->Text($xPos + ($premioArray[$index] * $scale) + 5, $yPos + ($index * 20), 'R$ ' . number_format($premioArray[$index], 2, ',', '.'));

    // Comissão (barra laranja horizontal)
    $pdf->SetFillColor(255, 165, 0); // Cor laranja
    $pdf->Rect($xPos, $yPos + ($index * 20) + $barHeight + 2, $comissaoArray[$index] * $scale, $barHeight, 'DF');
    $pdf->Text($xPos + ($comissaoArray[$index] * $scale) + 5, $yPos + ($index * 20) + $barHeight + 2, 'R$ ' . number_format($comissaoArray[$index], 2, ',', '.'));

    // Adiciona uma linha abaixo de cada seguradora para separação
    $pdf->SetDrawColor(0, 0, 0); // Cor da linha (preta)
    $pdf->Line($xPos - 50, $yPos + ($index * 20) + $barHeight + 10, $xPos + $maxWidth + 10, $yPos + ($index * 20) + $barHeight + 10); // Linha separadora
}


$pdf->Ln(10);

// Legenda do Gráfico 1
$pdf->SetFillColor(0, 102, 204);
$pdf->Rect(150, $yPos, 5, 5, 'DF');
$pdf->SetXY(155, $yPos);
$pdf->Cell(0, 5, 'Prêmio Líquido', 0, 1);

$pdf->SetFillColor(255, 165, 0);
$pdf->Rect(150, $yPos + 7, 5, 5, 'DF');
$pdf->SetXY(155, $yPos + 7);
$pdf->Cell(0, 5, 'Comissão', 0, 1);

$pdf->Ln(30);

// Gráfico 2: Apólices e Clientes por Seguradora (Horizontal)
$pdf->SetFont('helvetica', 'B', 12);

// Ajuste a posição Y para que o título fique acima das barras
$pdf->SetXY($xPos, $yPos - 10); // Posição do título acima do gráfico
$pdf->Ln(10);


// Ajuste do gráfico para Total de Apólices Canceladas, Emitidas e Clientes por Seguradora
$pdf->SetFont('helvetica', '', 10);
$pdf->Text($xPos - 50, $yPos + 160, "Total Apólices Canceladas");
$pdf->SetFillColor(255, 69, 0); // Cor vermelha
$pdf->Rect($xPos, $yPos + 160, $canceladas * $scale, $barHeight, 'DF');
$pdf->Text($xPos + ($canceladas * $scale) + 5, $yPos + 160, $canceladas);

$pdf->Text($xPos - 50, $yPos + 180, "Total Apólices Emitidas");
$pdf->SetFillColor(50, 205, 50); // Cor verde
$pdf->Rect($xPos, $yPos + 180, $emitidas * $scale, $barHeight, 'DF');
$pdf->Text($xPos + ($emitidas * $scale) + 5, $yPos + 180, $emitidas);

$pdf->Text($xPos - 50, $yPos + 200, "Clientes por Seguradora");
foreach ($clientesArray as $index => $totalClientes) {
    $pdf->SetFillColor(30, 144, 255); // Cor azul claro
    $pdf->Rect($xPos, $yPos + 200 + ($index * 20), $totalClientes * $scale, $barHeight, 'DF');
    $pdf->Text($xPos + ($totalClientes * $scale) + 5, $yPos + 200 + ($index * 20), $totalClientes);
}

$pdf->Ln(40);

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
    $pdf->Cell(0, 10, 'Total de Apólices Canceladas: ' . $canceladas, 0, 1);
    $pdf->Cell(0, 10, 'Total de Apólices Emitidas: ' . $emitidas, 0, 1);
} else {
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, 'Nenhum cliente encontrado para este mês.', 0, 1);
}
// Processa os dados para o gráfico de Tipos de Seguro
$tiposSeguroArray = [];
$premioTiposArray = [];

// Agrupando os tipos de seguro e seus valores
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

$pdf->Ln(30);


// Fecha a conexão com o banco de dados
$conn->close();

// Gera o PDF para download
$pdf->Output("Resumo_Mes_{$month}_Ano_{$year}.pdf", 'D');
exit;
