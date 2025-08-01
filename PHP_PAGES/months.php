<?php
/**
 * Localização: /PHP_PAGES/months.php
 * Página que exibe a seleção de meses com funcionalidade de comparação de anos.
 */

$page_title = "Produção por Mês";
include '../INCLUDES/header.php';
?>

<link rel="stylesheet" href="../CSS/months.css">

<?php include '../INCLUDES/navbar.php'; ?>

<div class="hero">
    <h1><i class="bi bi-calendar-event-fill"></i> Produção Mensal</h1>
    <p class="lead">Selecione um mês para ver a produção ou um resumo detalhado.</p>
</div>

<div class="container">
    <div class="row g-4">
        <?php
        $months = [
            '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril',
            '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto',
            '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
        ];
        foreach ($months as $month_num => $month_name) {
            include '../INCLUDES/month_card.php';
        }
        ?>
    </div>
</div>

<div class="modal fade" id="yearSelectionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-calendar"></i> Selecione o Ano do Resumo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <select id="yearSelect" class="form-select">
                    <?php
                    $currentYear = date('Y');
                    for ($i = $currentYear; $i >= $currentYear - 10; $i--) {
                        echo "<option value='$i'>$i</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Cancelar</button>
                <button type="button" id="confirmYear" class="btn btn-gradient"><i class="bi bi-check-circle"></i> Confirmar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="summaryModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="summaryModalLabel"><i class="bi bi-calendar-check-fill"></i> Resumo do Mês</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Fechar</button>
                <button type="button" id="compareYearBtn" class="btn btn-gradient"><i class="bi bi-bar-chart-fill"></i> Comparar Ano</button>
                <a href="#" id="downloadPdfBtn" target="_blank" class="btn btn-danger"><i class="bi bi-file-earmark-pdf"></i> Baixar PDF do Resumo</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="compareYearModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-calendar-range"></i> Selecionar Ano de Comparação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <select id="compareYearSelect" class="form-select">
                    <?php
                    for ($i = $currentYear; $i >= $currentYear - 10; $i--) {
                        echo "<option value='$i'>$i</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Cancelar</button>
                <button type="button" id="confirmCompareYear" class="btn btn-gradient"><i class="bi bi-check-circle"></i> Confirmar</button>
            </div>
        </div>
    </div>
</div>

<?php include '../INCLUDES/footer.php'; ?>

<script>
    let selectedMonth;
    let selectedYear;
    const chartInstances = {};

    function showYearSelection(month) {
        selectedMonth = month;
        const yearModal = new bootstrap.Modal(document.getElementById('yearSelectionModal'));
        yearModal.show();
    }

    function destroyCharts() {
        for (const chartId in chartInstances) {
            if (chartInstances[chartId]) {
                chartInstances[chartId].destroy();
            }
        }
    }
    
    const generateColors = (numColors) => {
        const colors = [];
        for (let i = 0; i < numColors; i++) {
            const r = Math.floor(Math.random() * 200);
            const g = Math.floor(Math.random() * 200);
            const b = Math.floor(Math.random() * 200);
            colors.push(`rgba(${r}, ${g}, ${b}, 0.7)`);
        }
        return colors;
    };

    function renderCharts(chartData) {
        destroyCharts();
        const createPieChart = (canvasId, label, chartInfo) => {
            const ctx = document.getElementById(canvasId);
            if (!ctx || !chartInfo || !chartInfo.labels || !chartInfo.labels.length) return;
            chartInstances[canvasId] = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: chartInfo.labels,
                    datasets: [{
                        label: label,
                        data: chartInfo.data,
                        backgroundColor: generateColors(chartInfo.data.length),
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' },
                        title: { display: true, text: label }
                    }
                }
            });
        };
        createPieChart('chartPremioPorSeguradora', 'Prêmio Líquido por Seguradora', chartData.premioPorSeguradora);
        createPieChart('chartClientesPorSeguradora', 'Clientes por Seguradora', chartData.clientesPorSeguradora);
        createPieChart('chartPremioPorTipo', 'Prêmio Líquido por Tipo de Seguro', chartData.premioPorTipo);
        createPieChart('chartClientesPorTipo', 'Clientes por Tipo de Seguro', chartData.clientesPorTipo);
    }

    function fetchSummary(month, year) {
        const summaryModalEl = document.getElementById('summaryModal');
        const summaryModal = bootstrap.Modal.getOrCreateInstance(summaryModalEl);
        const summaryBody = summaryModalEl.querySelector('.modal-body');

        summaryBody.innerHTML = '<div class="text-center p-5"><div class="spinner-border text-success" role="status"><span class="visually-hidden">Carregando...</span></div></div>';
        summaryModal.show();

        fetch(`../PHP_ACTION/summary.php?month=${month}&year=${year}`)
            .then(res => res.json())
            .then(result => {
                if(result.success) {
                    summaryBody.innerHTML = result.html;
                    renderCharts(result.chartData);
                    document.getElementById('downloadPdfBtn').href = `../PHP_ACTION/generate_pdf.php?month=${month}&year=${year}`;
                } else {
                    summaryBody.innerHTML = `<p class="text-danger">${result.error || 'Erro desconhecido.'}</p>`;
                }
            })
            .catch((error) => {
                console.error("Fetch Error:", error);
                summaryBody.innerHTML = '<p class="text-danger">Erro ao carregar resumo.</p>';
            });
    }

    // ================================================================
    // NOVO: Função para buscar e exibir a comparação de anos
    // ================================================================
    function fetchComparison(month, year1, year2) {
        const summaryModalEl = document.getElementById('summaryModal');
        const summaryModal = bootstrap.Modal.getOrCreateInstance(summaryModalEl);
        const summaryBody = summaryModalEl.querySelector('.modal-body');

        summaryBody.innerHTML = '<div class="text-center p-5"><div class="spinner-border text-success" role="status"></div></div>';
        summaryModal.show(); // Reabre o modal de resumo para mostrar a comparação

        fetch(`../PHP_ACTION/comparison.php?month=${month}&year1=${year1}&year2=${year2}`)
            .then(res => res.text())
            .then(html => {
                summaryBody.innerHTML = html; // Exibe a tabela de comparação
            })
            .catch(() => {
                summaryBody.innerHTML = '<p class="text-danger">Erro ao carregar a comparação.</p>';
            });
    }
    
    document.addEventListener('DOMContentLoaded', () => {
        const yearModalEl = document.getElementById('yearSelectionModal');
        const summaryModalEl = document.getElementById('summaryModal');
        const compareYearModalEl = document.getElementById('compareYearModal');

        document.getElementById('confirmYear').addEventListener('click', () => {
            const year = document.getElementById('yearSelect').value;
            selectedYear = year; // Armazena o ano principal
            bootstrap.Modal.getInstance(yearModalEl).hide();
            fetchSummary(selectedMonth, selectedYear);
        });

        document.getElementById('compareYearBtn').addEventListener('click', () => {
            bootstrap.Modal.getInstance(summaryModalEl).hide();
            const compareModal = new bootstrap.Modal(compareYearModalEl);
            compareModal.show();
        });

        // Evento do botão de confirmação do modal de comparação
        document.getElementById('confirmCompareYear').addEventListener('click', () => {
            const compareYear = document.getElementById('compareYearSelect').value;
            bootstrap.Modal.getInstance(compareYearModalEl).hide();
            // Chama a nova função de comparação
            fetchComparison(selectedMonth, selectedYear, compareYear);
        });
    });
</script>