<?php
/**
 * Componente do modal para visualizar e editar as anotações do cliente.
 */
?>
<div class="modal fade" id="notesModal-<?php echo $cliente['id']; ?>" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-journal-text"></i> Anotações sobre <?php echo htmlspecialchars(strtok($cliente['nome'], ' ')); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="../PHP_ACTION/handle_notes.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="cliente_id" value="<?php echo $cliente['id']; ?>">
                    <div class="form-group">
                        <label for="anotacoes-<?php echo $cliente['id']; ?>" class="form-label">Digite suas anotações abaixo:</label>
                        <textarea class="form-control" id="anotacoes-<?php echo $cliente['id']; ?>" name="anotacoes" rows="8"><?php echo htmlspecialchars($cliente['anotacoes'] ?? ''); ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Salvar Anotações</button>
                </div>
            </form>
        </div>
    </div>
</div>