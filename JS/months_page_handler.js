/**
 * Localização: /JS/months_page_handler.js
 * Gerencia toda a interatividade da página de Produção Mensal.
 */

// A variável `userPermissions` é definida em um <script> inline na página months.php
document.addEventListener('DOMContentLoaded', () => {
    let selectedMonth;
    let selectedYear;
    const chartInstances = {};

    window.showYearSelection = function(month) {
        selectedMonth = month;
        const yearModalEl = document.getElementById('yearSelectionModal');
        if (yearModalEl) {
            const yearModal = new bootstrap.Modal(yearModalEl);
            yearModal.show();
        }
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
        if (!summaryModalEl) return;
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
                    
                    // ================================================================
                    // AJUSTE APLICADO AQUI
                    // ================================================================
                    // Procura pelo botão de download
                    const downloadBtn = document.getElementById('downloadPdfBtn');
                    // Só tenta ajustar o href SE o botão existir na página
                    if (downloadBtn) { 
                        downloadBtn.href = `../PHP_ACTION/generate_pdf.php?month=${month}&year=${year}`;
                    }
                    // ================================================================

                } else {
                    summaryBody.innerHTML = `<p class="text-danger">${result.error || 'Erro desconhecido.'}</p>`;
                }
            })
            .catch((error) => {
                console.error("Fetch Error:", error);
                summaryBody.innerHTML = '<p class="text-danger">Erro ao carregar resumo.</p>';
            });
    }

    function fetchComparison(month, year1, year2) {
        const summaryModalEl = document.getElementById('summaryModal');
        if (!summaryModalEl) return;
        const summaryModal = bootstrap.Modal.getOrCreateInstance(summaryModalEl);
        const summaryBody = summaryModalEl.querySelector('.modal-body');

        summaryBody.innerHTML = '<div class="text-center p-5"><div class="spinner-border text-success" role="status"></div></div>';
        summaryModal.show();

        fetch(`../PHP_ACTION/comparison.php?month=${month}&year1=${year1}&year2=${year2}`)
            .then(res => res.text())
            .then(html => {
                summaryBody.innerHTML = html;
            })
            .catch(() => {
                summaryBody.innerHTML = '<p class="text-danger">Erro ao carregar a comparação.</p>';
            });
    }
    
    const yearModalEl = document.getElementById('yearSelectionModal');
    const summaryModalEl = document.getElementById('summaryModal');
    const compareYearModalEl = document.getElementById('compareYearModal');

    const confirmYearBtn = document.getElementById('confirmYear');
    if (confirmYearBtn && yearModalEl) {
        confirmYearBtn.addEventListener('click', () => {
            const year = document.getElementById('yearSelect').value;
            selectedYear = year;
            bootstrap.Modal.getInstance(yearModalEl).hide();
            fetchSummary(selectedMonth, selectedYear);
        });
    }

    const compareYearBtn = document.getElementById('compareYearBtn');
    if (compareYearBtn && summaryModalEl && compareYearModalEl) {
        compareYearBtn.addEventListener('click', () => {
            bootstrap.Modal.getInstance(summaryModalEl).hide();
            const compareModal = new bootstrap.Modal(compareYearModalEl);
            compareModal.show();
        });
    }
    
    const confirmCompareYearBtn = document.getElementById('confirmCompareYear');
    if (confirmCompareYearBtn && compareYearModalEl) {
        confirmCompareYearBtn.addEventListener('click', () => {
            const compareYear = document.getElementById('compareYearSelect').value;
            bootstrap.Modal.getInstance(compareYearModalEl).hide();
            fetchComparison(selectedMonth, selectedYear, compareYear);
        });
    }
});