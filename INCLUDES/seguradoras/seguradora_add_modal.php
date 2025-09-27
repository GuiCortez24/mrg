<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Adicionar Nova Seguradora</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="../PHP_ACTION/handle_seguradoras.php">
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Nome</label><input type="text" class="form-control" name="nome" required></div>
                    <div class="mb-3"><label class="form-label">Usuário</label><input type="text" class="form-control" name="usuario" required></div>
                    <div class="mb-3"><label class="form-label">Senha</label><input type="text" class="form-control" name="senha" required></div>
                    <div class="mb-3"><label class="form-label">Número 0800</label><input type="text" class="form-control" name="numero_0800" required></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" name="action" value="add" class="btn btn-primary">Adicionar</button>
                </div>
            </form>
        </div>
    </div>
</div>