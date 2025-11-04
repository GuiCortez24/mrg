<?php
/**
 * Localização: /PHP_PAGES/settings.php
 * Página de gerenciamento de usuários do sistema.
 * Este arquivo agora atua como um "montador" de componentes.
 */

$page_title = "Gerenciamento de Usuários";

// 1. Inclui a lógica que busca os dados dos usuários
require_once __DIR__ . '/../PHP_LOGIC/settings_logic.php';

// 2. Inclui o cabeçalho HTML principal (<head>, <body>, etc.)
require_once __DIR__ . '/../INCLUDES/header.php';

// 3. Inclui a barra de navegação
require_once __DIR__ . '/../INCLUDES/navbar.php';
?>

<div class="container mt-4">
    <?php
    // 4. Inclui o cabeçalho da página (título e botão de adicionar)
    require_once __DIR__ . '/../INCLUDES/settings_components/settings_header.php';

    // 5. Inclui a tabela com a lista de usuários
    require_once __DIR__ . '/../INCLUDES/user_management_table.php';
    ?>
</div>

<?php
// 6. Inclui o HTML do modal de formulário (fica oculto até ser chamado)
require_once __DIR__ . '/../INCLUDES/user_form_modal.php';

// 7. Inclui o rodapé da página
require_once __DIR__ . '/../INCLUDES/footer.php';
?>

<script src="../JS/settings_page_handler.js" defer></script>