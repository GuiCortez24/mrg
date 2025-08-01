<?php
/**
 * Localização: /PHP_PAGES/info_loja.php
 * Página para visualizar e gerenciar as informações das seguradoras.
 */

$page_title = "Gerenciar Seguradoras";
include '../db.php';
include '../INCLUDES/header.php';

// Busca todas as seguradoras para exibição
$result = $conn->query("SELECT * FROM seguradoras ORDER BY nome ASC");

include '../INCLUDES/navbar.php';
?>

<div class="container mt-4">
    <h2 class="mb-4"><i class="bi bi-building text-success"></i> Informações das Seguradoras</h2>
    
    <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Ação realizada com sucesso!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="bi bi-plus-circle"></i> Adicionar Nova Seguradora
    </button>

    <!-- Exibição das seguradoras como cards -->
    <div class="row">
        <?php while ($seguradora = $result->fetch_assoc()): ?>
            <?php include '../INCLUDES/seguradora_card.php'; ?>
        <?php endwhile; ?>
    </div>
</div>

<!-- Modal para adicionar nova seguradora -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Adicionar Nova Seguradora</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="../PHP_ACTION/handle_seguradoras.php">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" class="form-control" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Usuário</label>
                        <input type="text" class="form-control" name="usuario" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Senha</label>
                        <input type="text" class="form-control" name="senha" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Número 0800</label>
                        <input type="text" class="form-control" name="numero_0800" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" name="add_seguradora" class="btn btn-primary">Adicionar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../INCLUDES/footer.php'; ?>