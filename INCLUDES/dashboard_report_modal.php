<?php
/**
 * Componente que renderiza o modal de geração de relatórios.
 */
?>
<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportModalLabel"><i class="bi bi-file-earmark-medical"></i> Gerar Relatório</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="../PHP_ACTION/generate_report.php" method="GET" target="_blank">
                    <div class="mb-3">
                        <label for="reportType" class="form-label">Tipo de Relatório</label>
                        <select class="form-select" id="reportType" name="reportType" required>
                            <option value="Renovacao">Renovações</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="startDate" class="form-label">Data Início</label>
                            <input type="date" class="form-control" id="startDate" name="startDate" required>
                        </div>
                        <div class="col-md-6">
                            <label for="endDate" class="form-label">Data Fim</label>
                            <input type="date" class="form-control" id="endDate" name="endDate" required>
                        </div>
                    </div>
                    <div class="modal-footer mt-4 border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-download"></i> Gerar Relatório
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>