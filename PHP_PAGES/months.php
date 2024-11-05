<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Clientes por Mês</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../CSS/months.css"> <!-- Link para o CSS separado -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        /* Garantindo que o modal seja exibido corretamente acima de outros elementos */
        .modal {
            z-index: 1050;
        }

        .modal-backdrop {
            z-index: 1040;
        }

        .modal-content {
            z-index: 1050;
        }

        /* Adicionando uma altura máxima e scroll no modal de comparação */
        .modal-body.scrollable {
            max-height: 500px;
            overflow-y: auto;
        }
    </style>
    <script>
        function showYearSelection(month) {
            $('#yearSelectionModal').modal('show');
            $('#yearSelectionModal').data('month', month);
            $('#confirmYear').attr('onclick', `fetchSummary('${month}', $('#yearSelect').val())`);
        }

        function fetchSummary(month, year) {
            $('#yearSelectionModal').data('year', year);
            $.ajax({
                url: '../PHP_ACTION/summary.php',
                method: 'GET',
                data: { month: month, year: year },
                success: function(data) {
                    $('#summaryModal .modal-body').html(data);
                    $('#summaryModal').modal('show');
                },
                error: function(xhr, status, error) {
                    $('#summaryModal .modal-body').html('<p>Erro ao carregar resumo. Tente novamente.</p>');
                    $('#summaryModal').modal('show');
                }
            });
        }

        $(document).ready(function () {
            $('#compareYearBtn').click(function () {
                $('#summaryModal').modal('hide');  // Fechar o modal de resumo
                $('#compareYearModal').modal('show');  // Mostrar o modal de seleção de ano para comparação
            });

            $('#confirmCompareYear').click(function () {
                const compareYear = $('#compareYearSelect').val();
                const month = $('#yearSelectionModal').data('month');
                const year = $('#yearSelectionModal').data('year');

                fetchComparison(month, year, compareYear);
            });
        });

        function fetchComparison(month, year, compareYear) {
            $.ajax({
                url: '../PHP_ACTION/comparison.php',
                method: 'GET',
                data: { month: month, year: year, compareYear: compareYear },
                success: function(data) {
                    $('#compareYearModal').modal('hide');  // Fechar o modal de seleção de ano para comparação
                    $('#summaryModal .modal-body').html(data);
                    $('#summaryModal .modal-body').addClass('scrollable');
                    $('#summaryModal').modal('show');  // Reabrir o modal de resumo com a comparação
                },
                error: function(xhr, status, error) {
                    $('#compareYearModal').modal('hide');  // Fechar o modal de seleção de ano para comparação
                    $('#summaryModal .modal-body').html('<p>Erro ao carregar comparação. Tente novamente.</p>');
                    $('#summaryModal').modal('show');  // Reabrir o modal de resumo com a mensagem de erro
                }
            });
        }
    </script>
</head>
<body>
<div class="container">
    <!-- Botão para retornar ao index.php -->
    <div class="mb-3">
        <a href="../index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar para Início</a>
    </div>
    <h2 class="text-center"><i class="fas fa-calendar-alt"></i> Clientes por Mês</h2>
    <div class="row">
        <?php
        $months = [
            '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril',
            '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto',
            '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
        ];

        foreach ($months as $num => $name) {
            echo '<div class="col-md-4 mb-3">';
            echo '<div class="card">';
            echo '<div class="card-header"><i class="fas fa-calendar-day"></i> ' . $name . '</div>';
            echo '<div class="card-body">';
            echo '<a href="clients_by_month.php?month=' . $num . '" class="btn btn-primary btn-block mb-2"><i class="fas fa-calendar-day"></i> Ver Clientes</a>';
            echo '<button class="btn btn-info btn-block" onclick="showYearSelection(\'' . $num . '\')"><i class="fas fa-info-circle"></i> Mostrar Resumo</button>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
        ?>
    </div>
</div>

<!-- Modal para selecionar o ano -->
<div class="modal fade" id="yearSelectionModal" tabindex="-1" role="dialog" aria-labelledby="yearSelectionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="yearSelectionModalLabel"><i class="fas fa-calendar-alt"></i> Selecione o Ano</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
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
                <button type="button" id="confirmYear" class="btn btn-primary"><i class="fas fa-check"></i> Confirmar</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Cancelar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de resumo -->
<div class="modal fade" id="summaryModal" tabindex="-1" role="dialog" aria-labelledby="summaryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="summaryModalLabel"><i class="fas fa-calendar-check"></i> Resumo do Mês</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Conteúdo do resumo será carregado aqui via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Fechar</button>
                <button type="button" id="compareYearBtn" class="btn btn-primary"><i class="fas fa-balance-scale"></i> Comparar com Outro Ano</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para selecionar o ano para comparação -->
<div class="modal fade" id="compareYearModal" tabindex="-1" role="dialog" aria-labelledby="compareYearModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="compareYearModalLabel"><i class="fas fa-calendar-alt"></i> Selecione o Ano para Comparação</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="compareYearSelect">Ano:</label>
                    <select id="compareYearSelect" class="form-control">
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
                <button type="button" id="confirmCompareYear" class="btn btn-primary"><i class="fas fa-check"></i> Confirmar</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Cancelar</button>
            </div>
        </div>
    </div>
</div>
</body>
</html>
