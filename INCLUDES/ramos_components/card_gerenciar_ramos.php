<?php
/**
 * Componente: Card que contém o botão para abrir o modal de gerenciamento.
 */
?>
<div class="card h-100 border-light-subtle">
    <div class="card-body text-center d-flex flex-column justify-content-center">
        <h5 class="card-title"><i class="bi bi-card-list"></i> Gerenciar Ramos</h5>
        <p class="card-text text-muted small">Clique no botão abaixo para visualizar, editar ou excluir os ramos de seguro já cadastrados no sistema.</p>
        <div class="mt-2">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#manageRamosModal">
                <i class="bi bi-eye-fill me-2"></i> Ver Ramos Cadastrados
            </button>
        </div>
    </div>
</div>