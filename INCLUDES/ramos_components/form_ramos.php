<?php
/**
 * Componente: Formulário para Adicionar/Editar um Ramo de Seguro.
 * Apresentado dentro de um card com layout aprimorado.
 */
?>
<div class="card h-100 shadow-sm border-light-subtle">
    <div class="card-header bg-white">
        <h5 class="mb-0">
            <i class="bi <?php echo $edit_mode ? 'bi-pencil-square text-success' : 'bi-plus-circle-dotted text-primary'; ?> me-2"></i>
            <?php echo $edit_mode ? 'Editar Ramo de Seguro' : 'Adicionar Novo Ramo'; ?>
        </h5>
    </div>
    <div class="card-body">
        <p class="card-text text-muted small mb-3">
            <?php echo $edit_mode ? 'Faça as alterações necessárias no nome do ramo abaixo e clique em "Salvar".' : 'Preencha o nome do novo ramo de seguro que deseja cadastrar no sistema.'; ?>
        </p>

        <form method="POST" action="../PHP_ACTION/handle_ramos.php">
            <input type="hidden" name="action" value="<?php echo $edit_mode ? 'update' : 'add'; ?>">
            <?php if ($edit_mode): ?>
                <input type="hidden" name="id" value="<?php echo $ramo_to_edit['id']; ?>">
            <?php endif; ?>
            
            <div class="mb-3">
                <label for="ramoNome" class="form-label">Nome do Ramo</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-shield-shaded"></i></span>
                    <input type="text" class="form-control" id="ramoNome" name="nome" placeholder="Ex: Seguro de Automóvel" value="<?php echo $edit_mode ? htmlspecialchars($ramo_to_edit['nome']) : ''; ?>" required>
                </div>
            </div>
            
            <div class="d-flex justify-content-end gap-2 mt-4">
                <?php if ($edit_mode): ?>
                    <a href="ramos_seguro.php" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </a>
                <?php endif; ?>

                <button type="submit" class="btn <?php echo $edit_mode ? 'btn-success' : 'btn-primary'; ?>">
                    <i class="bi <?php echo $edit_mode ? 'bi-check-circle-fill' : 'bi-plus-circle-fill'; ?>"></i>
                    <?php echo $edit_mode ? 'Salvar Alterações' : 'Adicionar Ramo'; ?>
                </button>
            </div>
        </form>
    </div>
</div>