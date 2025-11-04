<?php
/**
 * Componente para renderizar a lista de cards de clientes.
 * Depende da variável $result.
 */
?>
<div class="row">
    <?php if (isset($result) && $result->num_rows > 0): ?>
        <?php while ($cliente = $result->fetch_assoc()): ?>
            <?php include __DIR__ . '/cliente_card.php'; ?>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="text-center mt-4">Nenhum cliente encontrado com os filtros aplicados.</p>
    <?php endif; ?>
</div>