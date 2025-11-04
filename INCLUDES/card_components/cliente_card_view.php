<?php
/**
 * Componente visual do card do cliente no dashboard.
 */
?>
<div class="col-md-4 mb-4">
    <div class="card h-100 shadow-sm <?php echo $borderColorClass; ?>" style="border-width: 2px;">
        <div class="card-body d-flex flex-column">
            <h5 class="card-title <?php echo $textColorClass; ?> mb-3">
                <i class="bi bi-person"></i> <?php echo htmlspecialchars($cliente['nome']); ?>
            </h5>
            <p class="card-text small">
                <strong><i class="bi bi-building"></i> Seguradora:</strong> <?php echo htmlspecialchars($cliente['seguradora']); ?><br>
                <strong><i class="bi bi-calendar-date"></i> Vigência:</strong> <?php echo formatDate($cliente['inicio_vigencia']); ?><br>
                <strong><i class="bi bi-calendar-date"></i> Final da Vigência:</strong> <?php echo formatDate($cliente['final_vigencia']); ?><br>
                <strong><i class="bi bi-file-earmark-text"></i> Proposta:</strong> <?php echo htmlspecialchars($cliente['apolice']); ?><br>
                <?php if (!empty($cliente['item_identificacao'])): ?>
                    <strong><i class="bi bi-hash"></i> Placa/ID:</strong> <?php echo htmlspecialchars($cliente['item_identificacao']); ?><br>
                <?php endif; ?>
            </p>
            <div class="mt-auto pt-3">
                <button type="button" class="btn btn-sm <?php echo 'btn-outline-' . str_replace(['custom-text-', 'text-'], '', $textColorClass); ?>" data-bs-toggle="modal" data-bs-target="#modal-<?php echo $cliente['id']; ?>">
                    <i class="bi bi-info-circle"></i> Saiba Mais
                </button>
            </div>
        </div>
    </div>
</div>