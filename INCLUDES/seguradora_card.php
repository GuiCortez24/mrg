<?php
/**
 * Localização: INCLUDES/seguradora_card.php
 * Exibe um card para uma seguradora específica e seus modais de edição/exclusão.
 * Espera que a variável $seguradora esteja definida.
 */
?>
<div class="col-md-4 mb-4">
    <div class="card h-100 shadow-sm">
        <div class="card-body">
            <h5 class="card-title text-success"><i class="bi bi-building me-2"></i><?php echo htmlspecialchars($seguradora['nome']); ?></h5>
            <p class="card-text mb-1"><i class="bi bi-person-fill me-2"></i><strong>Usuário:</strong> <?php echo htmlspecialchars($seguradora['usuario']); ?></p>
            <p class="card-text mb-1"><i class="bi bi-key-fill me-2"></i><strong>Senha:</strong> <?php echo htmlspecialchars($seguradora['senha']); ?></p>
            <p class="card-text"><i class="bi bi-telephone-fill me-2"></i><strong>0800:</strong> <?php echo htmlspecialchars($seguradora['numero_0800']); ?></p>
        </div>
        <div class="card-footer bg-white border-0 d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $seguradora['id']; ?>">
                <i class="bi bi-pencil-square"></i> Editar
            </button>
            <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $seguradora['id']; ?>">
                <i class="bi bi-trash"></i> Excluir
            </button>
        </div>
    </div>
</div>

<!-- Modal de Edição -->
<div class="modal fade" id="editModal<?php echo $seguradora['id']; ?>" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Editar Seguradora</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="../PHP_ACTION/handle_seguradoras.php">
                <div class="modal-body">
                    <input type="hidden" name="id" value="<?php echo $seguradora['id']; ?>">
                    <div class="mb-3">
                        <label class="form-label">Usuário</label>
                        <input type="text" class="form-control" name="usuario" value="<?php echo htmlspecialchars($seguradora['usuario']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nova Senha</label>
                        <input type="text" class="form-control" name="senha" placeholder="Deixe em branco para manter a atual">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Número 0800</label>
                        <input type="text" class="form-control" name="numero_0800" value="<?php echo htmlspecialchars($seguradora['numero_0800']); ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" name="edit_seguradora" class="btn btn-primary">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Exclusão -->
<div class="modal fade" id="deleteModal<?php echo $seguradora['id']; ?>" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill"></i> Confirmar Exclusão</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="../PHP_ACTION/handle_seguradoras.php">
                <div class="modal-body">
                    <p>Você tem certeza que deseja excluir a seguradora <strong><?php echo htmlspecialchars($seguradora['nome']); ?></strong>?</p>
                    <p class="text-danger">Esta ação não pode ser desfeita.</p>
                    <input type="hidden" name="id" value="<?php echo $seguradora['id']; ?>">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" name="delete_seguradora" class="btn btn-danger">Sim, Excluir</button>
                </div>
            </form>
        </div>
    </div>
</div>