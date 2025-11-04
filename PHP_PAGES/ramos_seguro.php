<?php
/**
 * Localização: /PHP_PAGES/ramos_seguro.php
 * Página de gerenciamento de Ramos de Seguro com interface moderna baseada em modais.
 */

$page_title = "Gerenciar Ramos de Seguro";

// 1. Inclui a lógica de preparação de dados
require_once __DIR__ . '/../PHP_LOGIC/ramos_logic.php';

// 2. Inclui o cabeçalho HTML
require_once __DIR__ . '/../INCLUDES/header.php';

// 3. Inclui a barra de navegação
require_once __DIR__ . '/../INCLUDES/navbar.php';
?>

<div class="container mt-5 mb-5">
    <div class="text-center mb-4">
        <h2 class="display-6"><i class="bi bi-shield-check text-primary"></i> Gerenciar Ramos de Seguro</h2>
        <p class="lead text-muted">Adicione novos ramos ou gerencie os já existentes.</p>
    </div>

    <?php // Exibe o alerta de status, se houver
    if (!empty($alert_message)): ?>
        <div class="alert <?php echo $alert_class; ?> alert-dismissible fade show" role="alert">
            <?php echo $alert_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-7">
            <?php require_once __DIR__ . '/../INCLUDES/ramos_components/form_ramos.php'; ?>
        </div>

        <div class="col-lg-5">
            <?php require_once __DIR__ . '/../INCLUDES/ramos_components/card_gerenciar_ramos.php'; ?>
        </div>
    </div>
    
    <div class="text-center mt-5">
        <a href="dashboard.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left-circle"></i> Voltar para o Painel</a>
    </div>
</div>

<?php 
// Inclui o HTML do modal (fica oculto até ser chamado)
require_once __DIR__ . '/../INCLUDES/ramos_components/modal_lista_ramos.php';

// Inclui o rodapé da página
require_once __DIR__ . '/../INCLUDES/footer.php'; 
?>