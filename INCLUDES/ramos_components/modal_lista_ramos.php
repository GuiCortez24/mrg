<?php
/**
 * Componente: Modal que exibe a lista de ramos cadastrados.
 * Utiliza um List Group para um visual moderno com ícones.
 */
?>
<div class="modal fade" id="manageRamosModal" tabindex="-1" aria-labelledby="manageRamosModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="manageRamosModalLabel"><i class="bi bi-list-ul me-2"></i> Ramos de Seguro Cadastrados</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if ($ramos_result->num_rows > 0): ?>
                    <ul class="list-group">
                        <?php while($row = $ramos_result->fetch_assoc()): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="bi bi-shield-shaded text-muted me-3"></i>
                                    <strong><?php echo htmlspecialchars($row['nome']); ?></strong>
                                </div>
                                <div class="actions">
                                    <a href="ramos_seguro.php?edit_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-secondary" title="Editar">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <a href="../PHP_ACTION/handle_ramos.php?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Tem certeza que deseja excluir este ramo? Esta ação não pode ser desfeita.');" title="Excluir">
                                        <i class="bi bi-trash-fill"></i>
                                    </a>
                                </div>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <div class="alert alert-light text-center mb-0">
                        Nenhum ramo de seguro cadastrado.
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>