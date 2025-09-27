<?php
/**
 * Localização: INCLUDES/seguradoras/seguradora_card.php
 * Exibe um card para uma seguradora específica.
 */
?>
<div class="col-md-4 col-lg-3 mb-4 d-flex align-items-stretch">
    <div class="card h-100 shadow-sm w-100">
        <div class="card-header bg-light fw-bold text-success">
            <i class="bi bi-building me-2"></i><?php echo htmlspecialchars($seguradora['nome']); ?>
        </div>
        <div class="card-body d-flex flex-column">
            <p class="card-text mb-2"><strong class="text-muted">Usuário:</strong> <?php echo htmlspecialchars($seguradora['usuario']); ?></p>
            
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <strong class="text-muted">Senha:</strong>
                    <span class="password-text" data-password="<?php echo htmlspecialchars($seguradora['senha']); ?>">••••••••</span>
                </div>
                <span class="toggle-password-btn text-primary" style="cursor: pointer;" title="Mostrar/Ocultar Senha">
                    <i class="bi bi-eye-fill"></i>
                </span>
            </div>
            
            <p class="card-text mt-auto"><strong class="text-muted">0800:</strong> <?php echo htmlspecialchars($seguradora['numero_0800']); ?></p>
        </div>
        <div class="card-footer text-center bg-white border-0 pb-3">
            <button type="button" class="btn btn-warning btn-sm" 
                    data-bs-toggle="modal" 
                    data-bs-target="#editModal"
                    data-id="<?php echo $seguradora['id']; ?>"
                    data-nome="<?php echo htmlspecialchars($seguradora['nome']); ?>"
                    data-usuario="<?php echo htmlspecialchars($seguradora['usuario']); ?>"
                    data-senha="" 
                    data-numero0800="<?php echo htmlspecialchars($seguradora['numero_0800']); ?>">
                <i class="bi bi-pencil-fill"></i> Editar
            </button>
            <a href="../PHP_ACTION/handle_seguradoras.php?delete_id=<?php echo $seguradora['id']; ?>" 
               class="btn btn-danger btn-sm" 
               onclick="return confirm('Tem certeza que deseja excluir a seguradora <?php echo htmlspecialchars($seguradora['nome']); ?>?');">
                <i class="bi bi-trash-fill"></i> Excluir
            </a>
        </div>
    </div>
</div>