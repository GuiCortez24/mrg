<div class="modal fade" id="yearSelectionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-calendar"></i> Selecione o Ano do Resumo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <select id="yearSelect" class="form-select">
                    <?php foreach ($years as $year): ?>
                        <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Cancelar</button>
                <button type="button" id="confirmYear" class="btn btn-gradient"><i class="bi bi-check-circle"></i> Confirmar</button>
            </div>
        </div>
    </div>
</div>