<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes por Mês</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../CSS/months.css">
    <style>
        .modal-body.scrollable {
            max-height: 500px;
            overflow-y: auto;
        }
        .card:hover {
            transform: scale(1.02);
            transition: all 0.3s ease-in-out;
        }
        .modal-footer .btn {
            min-width: 150px;
        }
        .card-header {
            color: #ffffff; /* Mantém o texto visível */
        }
    </style>
</head>

<body>
<div class="container mt-4">
    <div class="mb-4 text-start">
        <a href="../index.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Voltar para Início</a>
    </div>

    <h2 class="text-center mb-4"><i class="bi bi-calendar-event"></i> Clientes por Mês</h2>

    <div class="row">
        <?php
        $months = [
            '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril',
            '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto',
            '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
        ];

        foreach ($months as $num => $name) {
            echo '<div class="col-md-4 mb-3">';
            echo '<div class="card shadow-sm border-0">';
            echo '<div class="card-header text-white bg-primary"><i class="bi bi-calendar-day"></i> ' . $name . '</div>';
            echo '<div class="card-body text-center">';
            echo '<a href="clients_by_month.php?month=' . $num . '" class="btn btn-primary w-100 mb-2"><i class="bi bi-people"></i> Ver Clientes</a>';
            echo '<button class="btn btn-info w-100" onclick="showYearSelection(\'' . $num . '\')"><i class="bi bi-info-circle"></i> Mostrar Resumo</button>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
        ?>
    </div>
</div>

<!-- Modal para selecionar o ano -->
<div class="modal fade" id="yearSelectionModal" tabindex="-1" aria-labelledby="yearSelectionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="yearSelectionModalLabel"><i class="bi bi-calendar"></i> Selecione o Ano</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="yearSelect">Ano:</label>
                    <select id="yearSelect" class="form-control">
                        <?php
                        $currentYear = date('Y');
                        for ($i = $currentYear; $i >= $currentYear - 10; $i--) {
                            echo "<option value='$i'>$i</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="confirmYear" class="btn btn-primary"><i class="bi bi-check-circle"></i> Confirmar</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Cancelar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Resumo do Mês -->
<div class="modal fade" id="summaryModal" tabindex="-1" aria-labelledby="summaryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="summaryModalLabel"><i class="bi bi-calendar-check"></i> Resumo do Mês</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <!-- Conteúdo do resumo será carregado aqui via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Fechar</button>
                <button type="button" id="compareYearBtn" class="btn btn-primary"><i class="bi bi-balance-scale"></i> Comparar com Outro Ano</button>
                <a href="#" id="downloadPdfBtn" target="_blank" class="btn btn-outline-success"><i class="bi bi-download"></i> Baixar Resumo em PDF</a>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Comparação de Ano -->
<div class="modal fade" id="compareYearModal" tabindex="-1" aria-labelledby="compareYearModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="compareYearModalLabel"><i class="bi bi-calendar-range"></i> Selecione o Ano para Comparação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="compareYearSelect">Ano:</label>
                    <select id="compareYearSelect" class="form-control">
                        <?php
                        for ($i = $currentYear; $i >= $currentYear - 10; $i--) {
                            echo "<option value='$i'>$i</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="confirmCompareYear" class="btn btn-primary"><i class="bi bi-check-circle"></i> Confirmar</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Cancelar</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function showYearSelection(month) {
        const yearSelectionModal = new bootstrap.Modal(document.getElementById('yearSelectionModal'));
        document.getElementById('yearSelectionModal').setAttribute('data-month', month);
        document.getElementById('confirmYear').onclick = function () {
            const selectedYear = document.getElementById('yearSelect').value;
            fetchSummary(month, selectedYear);
            document.getElementById('downloadPdfBtn').href = `../PHP_ACTION/generate_pdf.php?month=${month}&year=${selectedYear}`;
            yearSelectionModal.hide(); // Fechar modal após seleção
        };
        yearSelectionModal.show();
    }

    function fetchSummary(month, year) {
        const summaryModal = new bootstrap.Modal(document.getElementById('summaryModal'));
        const pdfBtn = document.getElementById('downloadPdfBtn');
        pdfBtn.style.display = 'block'; // Mostrar botão PDF
        fetch(`../PHP_ACTION/summary.php?month=${month}&year=${year}`)
            .then(response => response.text())
            .then(data => {
                document.querySelector('#summaryModal .modal-body').innerHTML = data;
                summaryModal.show();
            })
            .catch(() => {
                document.querySelector('#summaryModal .modal-body').innerHTML = '<p>Erro ao carregar resumo. Tente novamente.</p>';
                summaryModal.show();
            });
    }

    function fetchComparison(month, year, compareYear) {
        const summaryModal = new bootstrap.Modal(document.getElementById('summaryModal'));
        const pdfBtn = document.getElementById('downloadPdfBtn');
        pdfBtn.style.display = 'none'; // Ocultar botão PDF no modo de comparação
        const compareYearModal = bootstrap.Modal.getInstance(document.getElementById('compareYearModal'));
        compareYearModal.hide(); // Fechar modal "Selecione o Ano da Comparação"
        fetch(`../PHP_ACTION/comparison.php?month=${month}&year=${year}&compareYear=${compareYear}`)
            .then(response => response.text())
            .then(data => {
                document.querySelector('#summaryModal .modal-body').innerHTML = data;
                document.querySelector('#summaryModal .modal-body').classList.add('scrollable');
                summaryModal.show();
            })
            .catch(() => {
                document.querySelector('#summaryModal .modal-body').innerHTML = '<p>Erro ao carregar comparação. Tente novamente.</p>';
                summaryModal.show();
            });
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('compareYearBtn').onclick = function () {
            const summaryModal = bootstrap.Modal.getInstance(document.getElementById('summaryModal'));
            summaryModal.hide();
            const compareYearModal = new bootstrap.Modal(document.getElementById('compareYearModal'));
            compareYearModal.show();
        };

        document.getElementById('confirmCompareYear').onclick = function () {
            const compareYear = document.getElementById('compareYearSelect').value;
            const month = document.getElementById('yearSelectionModal').getAttribute('data-month');
            const year = document.getElementById('yearSelect').value;
            fetchComparison(month, year, compareYear);
        };
    });
</script>
</body>
</html>
