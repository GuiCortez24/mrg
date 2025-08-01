<?php
/**
 * Localização: /INCLUDES/pagination.php
 * Componente que renderiza os controles de paginação.
 * Espera que as variáveis $pagina_atual, $total_paginas e $query_string já estejam definidas.
 */
?>
<nav class="d-flex justify-content-center mt-4">
    <ul class="pagination">
        <?php if ($pagina_atual > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?pagina=<?php echo $pagina_atual - 1; ?>&<?php echo $query_string; ?>">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>
        <?php endif; ?>
        
        <?php if ($total_paginas > 0): ?>
        <li class="page-item active">
            <span class="page-link">Página <?php echo $pagina_atual; ?> de <?php echo $total_paginas; ?></span>
        </li>
        <?php endif; ?>

        <?php if ($pagina_atual < $total_paginas): ?>
            <li class="page-item">
                <a class="page-link" href="?pagina=<?php echo $pagina_atual + 1; ?>&<?php echo $query_string; ?>">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</nav>