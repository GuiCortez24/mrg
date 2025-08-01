<?php
/**
 * Localização: INCLUDES/month_card.php
 * Exibe um card para um mês específico.
 * Espera que as variáveis $month_num e $month_name estejam definidas.
 */
?>
<div class="col-md-4 mb-4">
    <div class="card month-card h-100 shadow-sm">
        <div class="card-header">
            <i class="bi bi-calendar-day card-icon"></i> <?php echo htmlspecialchars($month_name); ?>
        </div>
        <div class="card-body d-flex flex-column justify-content-center align-items-center">
            <a href="clients_by_month.php?month=<?php echo htmlspecialchars($month_num); ?>" class="btn btn-gradient w-100 mb-3">
                <i class="bi bi-people-fill"></i> Ver Produção
            </a>
            <button class="btn btn-outline-success w-100" onclick="showYearSelection('<?php echo htmlspecialchars($month_num); ?>')">
                <i class="bi bi-bar-chart-line-fill"></i> Ver Resumo
            </button>
        </div>
    </div>
</div>