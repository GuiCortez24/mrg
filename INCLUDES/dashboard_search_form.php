<?php
/**
 * Localização: /INCLUDES/dashboard_search_form.php
 * Componente que renderiza o formulário de busca do painel com layout corrigido, espaçoso e botão para limpar os filtros.
 */
?>
<div class="card card-body mb-4 shadow-sm">
    <form method="GET" action="dashboard.php">
        <div class="row g-3 mb-2 align-items-end">
            <div class="col-lg-4 col-md-6">
                <label for="search_nome" class="form-label-sm"><i class="bi bi-person me-1"></i> Nome do Cliente</label>
                <input type="text" class="form-control form-control-sm" id="search_nome" name="search_nome" value="<?php echo htmlspecialchars($search_nome ?? ''); ?>">
            </div>
            <div class="col-lg-2 col-md-6">
                <label for="search_cpf" class="form-label-sm"><i class="bi bi-credit-card me-1"></i> CPF / CNPJ</label>
                <input type="text" class="form-control form-control-sm" id="search_cpf" name="search_cpf" value="<?php echo htmlspecialchars($search_cpf ?? ''); ?>">
            </div>
            <div class="col-lg-4 col-md-6">
                <label for="search_item_segurado" class="form-label-sm"><i class="bi bi-box-seam me-1"></i> Item Segurado</label>
                <input type="text" class="form-control form-control-sm" id="search_item_segurado" name="search_item_segurado" value="<?php echo htmlspecialchars($search_item_segurado ?? ''); ?>">
            </div>
            <div class="col-lg-2 col-md-6">
                <label for="search_item" class="form-label-sm"><i class="bi bi-hash me-1"></i> Placa / ID</label>
                <input type="text" class="form-control form-control-sm" id="search_item" name="search_item" value="<?php echo htmlspecialchars($search_item ?? ''); ?>">
            </div>
        </div>
        <div class="row g-3 align-items-end">
            <div class="col-lg-3 col-md-4">
                <label for="search_vigencia_de" class="form-label-sm"><i class="bi bi-calendar-range me-1"></i> Período (De)</label>
                <input type="date" class="form-control form-control-sm" id="search_vigencia_de" name="search_vigencia_de" value="<?php echo htmlspecialchars($search_vigencia_de ?? ''); ?>">
            </div>
            <div class="col-lg-3 col-md-4">
                <label for="search_vigencia_ate" class="form-label-sm"><i class="bi bi-calendar-range me-1"></i> Período (Até)</label>
                <input type="date" class="form-control form-control-sm" id="search_vigencia_ate" name="search_vigencia_ate" value="<?php echo htmlspecialchars($search_vigencia_ate ?? ''); ?>">
            </div>

            <div class="col-lg-3 col-md-4">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success btn-sm w-100" title="Buscar">
                        <i class="bi bi-search"></i> Buscar
                    </button>
                    <a href="dashboard.php" class="btn btn-outline-secondary btn-sm" title="Limpar Filtros">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>