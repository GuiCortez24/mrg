<?php
/**
 * Localização: /PHP_PAGES/dashboard.php
 * Painel principal do sistema.
 * Este arquivo agora atua como um "montador", incluindo a lógica de negócios
 * e os componentes de visualização de arquivos separados para melhor organização.
*/

$page_title = "Gerenciamento de Clientes";

// 1. Inclui o arquivo que processa toda a lógica da página
require_once __DIR__ . '/../PHP_LOGIC/dashboard_logic.php';

// 2. Inclui o cabeçalho HTML
require_once __DIR__ . '/../INCLUDES/header.php';

// 3. Inclui a barra de navegação
require_once __DIR__ . '/../INCLUDES/navbar.php';
?>

<div class="container mt-4">
    <p class="h5"><?php echo htmlspecialchars($saudacao); ?></p>
    <h2 class="mb-4"><i class="bi bi-clipboard-data"></i> Painel de Gerenciamento</h2>

    <?php
    // Inclui o formulário de busca
    require_once __DIR__ . '/../INCLUDES/dashboard_search_form.php';
    ?>

    <div class="d-flex gap-2 mb-4">
        <a href="add.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Adicionar Proposta</a>
        <button type="button" class="btn btn-info text-white" data-bs-toggle="modal" data-bs-target="#reportModal">
            <i class="bi bi-file-earmark-arrow-down"></i> Relatório
        </button>
    </div>

    <?php
    // Inclui a lista de clientes (cards)
    require_once __DIR__ . '/../INCLUDES/dashboard_client_list.php';

    // Inclui o componente de paginação
    require_once __DIR__ . '/../INCLUDES/pagination.php';
    ?>
</div>

<?php
// Inclui o HTML do modal de relatórios
require_once __DIR__ . '/../INCLUDES/dashboard_report_modal.php';

// ===================================================================
// LÓGICA DO ÍCONE FLUTUANTE DE ADMINISTRAÇÃO
// ===================================================================

// Lista de e-mails de administradores
$admin_emails = [
    'ja@mrgseguros.com.br',
    'william@mrgseguros.com.br'
];

// Pega o e-mail do usuário logado da sessão.
// IMPORTANTE: Verifique se a chave 'user_email' está correta para a sua sessão!
$logged_in_user_email = $_SESSION['user_email'] ?? null;

// Verifica se o usuário logado é um administrador e inclui o ícone
if (in_array($logged_in_user_email, $admin_emails)) {
    require_once __DIR__ . '/../INCLUDES/floating_settings_button.php';
}

// ===================================================================

// Inclui o rodapé da página
require_once __DIR__ . '/../INCLUDES/footer.php';
?>