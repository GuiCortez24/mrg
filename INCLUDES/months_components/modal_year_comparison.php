<div class="modal fade" id="compareYearModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-calendar-range"></i> Selecionar Ano de Comparação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <select id="compareYearSelect" class="form-select">
                    <?php foreach ($years as $year): ?>
                        <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Cancelar</button>
                <button type="button" id="confirmCompareYear" class="btn btn-gradient"><i class="bi bi-check-circle"></i> Confirmar</button>
            </div>
        </div>
    </div>
</div>