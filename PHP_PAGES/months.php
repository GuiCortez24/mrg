<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes por Mês</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Custom Styles -->
    <style>
        :root {
            --primary: #8BC34A;
            --primary-dark: #689F38;
            --accent: #ffdd57;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            color: #333;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            text-align: center;
            padding: 4rem 1rem;
            position: relative;
            overflow: hidden;
        }

        .hero .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.3);
            z-index: 1;
        }

        .hero h1,
        .hero p {
            position: relative;
            z-index: 2;
        }

        .hero h1 {
            font-size: 3rem;
            font-weight: 700;
        }

        .breadcrumb-custom {
            --bs-breadcrumb-divider: '>';
            margin-bottom: 2rem;
            padding: 0 1rem;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 1rem;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background: var(--primary);
            color: #fff;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
        }

        .card-icon {
            font-size: 1.5rem;
        }

        .btn-gradient {
            background: linear-gradient(90deg, var(--primary), var(--primary-dark));
            color: #fff;
            border: none;
        }

        .btn-gradient:hover {
            background: linear-gradient(90deg, var(--primary-dark), var(--primary));
            color: #fff;
        }

        .modal-header {
            background: var(--accent);
            color: #333;
            font-weight: 500;
        }

        .modal-footer .btn {
            min-width: 150px;
        }

        .modal-body.scrollable {
            max-height: 60vh;
            overflow-y: auto;
        }

        /* Footer */
        footer {
            text-align: center;
            padding: 2rem 1rem;
            font-size: 0.9rem;
            color: #666;
        }
    </style>
</head>

<body>
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="breadcrumb-custom">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php"><i class="bi bi-house-fill"></i> Início</a></li>
            <li class="breadcrumb-item active" aria-current="page">Clientes por Mês</li>
        </ol>
    </nav>

    <!-- Hero Section -->
    <div class="hero mb-5">
        <div class="overlay"></div>
        <h1><i class="bi bi-calendar-event-fill"></i> Clientes por Mês</h1>
        <p class="lead">Visualize e compare a performance mensal de clientes.</p>
    </div>

    <!-- Cards Grid -->
    <div class="container">
        <div class="row g-4">
            <?php
            $months = [
                '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril',
                '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto',
                '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
            ];

            foreach ($months as $num => $name) {
                echo '<div class="col-md-4">';
                echo '<div class="card h-100 shadow-sm">';
                echo '<div class="card-header"><i class="bi bi-calendar-day card-icon"></i> ' . $name . '</div>';
                echo '<div class="card-body d-flex flex-column justify-content-center align-items-center">';
                echo '<a href="clients_by_month.php?month=' . $num . '" class="btn btn-gradient w-100 mb-3"><i class="bi bi-people-fill"></i> Ver Clientes</a>';
                echo '<button class="btn btn-outline-success w-100" onclick="showYearSelection(\'' . $num . '\')"><i class="bi bi-info-circle-fill"></i> Mostrar Resumo</button>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
    </div>

    <!-- Modals -->
    <!-- Year Selection Modal -->
    <div class="modal fade" id="yearSelectionModal" tabindex="-1" aria-labelledby="yearSelectionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="yearSelectionModalLabel"><i class="bi bi-calendar"></i> Selecione o Ano</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="yearSelect" class="form-label">Ano</label>
                        <select id="yearSelect" class="form-select">
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
                    <button type="button" id="confirmYear" class="btn btn-gradient"><i class="bi bi-check-circle-fill"></i> Confirmar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle-fill"></i> Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Modal -->
    <div class="modal fade" id="summaryModal" tabindex="-1" aria-labelledby="summaryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="summaryModalLabel"><i class="bi bi-calendar-check-fill"></i> Resumo do Mês</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <!-- Conteúdo carregado via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle-fill"></i> Fechar</button>
                    <button type="button" id="compareYearBtn" class="btn btn-gradient"><i class="bi bi-bar-chart-fill"></i> Comparar Ano</button>
                    <a href="#" id="downloadPdfBtn" target="_blank" class="btn btn-outline-success"><i class="bi bi-download"></i> Baixar PDF</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Compare Year Modal -->
    <div class="modal fade" id="compareYearModal" tabindex="-1" aria-labelledby="compareYearModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="compareYearModalLabel"><i class="bi bi-calendar-range"></i> Selecionar Ano de Comparação</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="compareYearSelect" class="form-label">Ano</label>
                        <select id="compareYearSelect" class="form-select">
                            <?php
                            for ($i = $currentYear; $i >= $currentYear - 10; $i--) {
                                echo "<option value='$i'>$i</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="confirmCompareYear" class="btn btn-gradient"><i class="bi bi-check-circle-fill"></i> Confirmar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle-fill"></i> Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // função que abre o modal de Seleção de Ano (chamada pelo botão da card)
    function showYearSelection(month) {
        const yearModalEl = document.getElementById('yearSelectionModal');
        const yearModal   = new bootstrap.Modal(yearModalEl);

        // guardo o mês para usar depois na comparação
        yearModalEl.dataset.month = month;

        document.getElementById('confirmYear').onclick = () => {
            const year = document.getElementById('yearSelect').value;
            // carrega o resumo e guarda também o month no compareModal
            fetchSummary(month, year);
            // atualiza o link do PDF
            document.getElementById('downloadPdfBtn').href =
              `../PHP_ACTION/generate_pdf.php?month=${month}&year=${year}`;
            yearModal.hide();
        };

        yearModal.show();
    }

    function fetchSummary(month, year) {
        const summaryModalEl = document.getElementById('summaryModal');
        const summaryModal   = new bootstrap.Modal(summaryModalEl);

        fetch(`../PHP_ACTION/summary.php?month=${month}&year=${year}`)
            .then(res => res.text())
            .then(html => {
                document.querySelector('#summaryModal .modal-body').innerHTML = html;

                // guardo o mês também no compareModal para usar depois
                document.getElementById('compareYearModal').dataset.month = month;

                // removo possível overflow anterior
                document.querySelector('#summaryModal .modal-body')
                        .classList.remove('scrollable');

                summaryModal.show();
            })
            .catch(() => {
                document.querySelector('#summaryModal .modal-body')
                        .innerHTML = '<p class="text-danger">Erro ao carregar resumo.</p>';
                summaryModal.show();
            });
    }

    document.addEventListener('DOMContentLoaded', () => {
        // botão “Comparar Ano” fecha automaticamente o summary e abre o compare
        document.getElementById('compareYearBtn').onclick = () => {
            bootstrap.Modal.getInstance(
              document.getElementById('summaryModal')
            ).hide();
            new bootstrap.Modal(
              document.getElementById('compareYearModal')
            ).show();
        };

        // confirmação do ano de comparação
        document.getElementById('confirmCompareYear').onclick = () => {
            const compareEl   = document.getElementById('compareYearModal');
            const compareModal= bootstrap.Modal.getInstance(compareEl);

            // **fechar o modal de seleção de ano**
            compareModal.hide();

            // recuperar dados
            const month       = compareEl.dataset.month;
            const year        = document.getElementById('yearSelect').value;
            const compareYear = document.getElementById('compareYearSelect').value;

            // buscar e reabrir o summary já com comparação
            fetchComparison(month, year, compareYear);
        };
    });

    function fetchComparison(month, year, compareYear) {
        const summaryModalEl = document.getElementById('summaryModal');
        const summaryModal   = new bootstrap.Modal(summaryModalEl);

        fetch(`../PHP_ACTION/comparison.php?month=${month}&year=${year}&compareYear=${compareYear}`)
            .then(res => res.text())
            .then(html => {
                const body = document.querySelector('#summaryModal .modal-body');
                body.innerHTML = html;
                body.classList.add('scrollable');
                summaryModal.show();
            })
            .catch(() => {
                document.querySelector('#summaryModal .modal-body')
                        .innerHTML = '<p class="text-danger">Erro ao carregar comparação.</p>';
                summaryModal.show();
            });
    }
</script>

    <!-- Footer -->
    <footer>
        &copy; <?= date('Y') ?> Sua Empresa. Todos os direitos reservados.
    </footer>
</body>

</html>
