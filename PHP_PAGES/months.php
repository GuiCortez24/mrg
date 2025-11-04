<?php
/**
 * Localização: /PHP_PAGES/months.php
 * Página que exibe a seleção de meses com funcionalidade de comparação de anos.
 * Este arquivo agora atua como um "montador" de componentes.
 */

$page_title = "Produção por Mês";

// 1. Inclui a lógica da página e verificação de permissões
require_once __DIR__ . '/../PHP_LOGIC/months_logic.php';

// 2. Inclui o cabeçalho
require_once __DIR__ . '/../INCLUDES/header.php';
?>

<link rel="stylesheet" href="../CSS/months.css">

<?php
// 3. Inclui a barra de navegação
require_once __DIR__ . '/../INCLUDES/navbar.php';

// 4. Inclui os componentes visuais da página
require_once __DIR__ . '/../INCLUDES/months_components/months_hero.php';
require_once __DIR__ . '/../INCLUDES/months_components/months_grid.php';

// 5. Inclui os modais (HTML oculto)
require_once __DIR__ . '/../INCLUDES/months_components/modal_year_selection.php';
require_once __DIR__ . '/../INCLUDES/months_components/modal_summary.php';
if ($user_can_see_commission) { // O modal de comparação só é carregado se o usuário tiver permissão
    require_once __DIR__ . '/../INCLUDES/months_components/modal_year_comparison.php';
}

// 6. Inclui o rodapé
require_once __DIR__ . '/../INCLUDES/footer.php';
?>

<script>
    const userPermissions = {
        canSeeCommission: <?php echo json_encode($user_can_see_commission); ?>
    };
</script>

<script src="../JS/months_page_handler.js"></script>