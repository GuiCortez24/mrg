<div class="card card-body mb-4">
    <form method="GET" action="info_loja.php" class="d-flex">
        <input type="text" name="search" class="form-control me-2" placeholder="Filtrar por nome da seguradora..." value="<?php echo htmlspecialchars($search_term); ?>">
        <button type="submit" class="btn btn-success"><i class="bi bi-search"></i> Filtrar</button>
        <?php if(!empty($search_term)): ?>
            <a href="../PHP_PAGES/info_loja.php" class="btn btn-outline-secondary ms-2"><i class="bi bi-x"></i> Limpar</a>
        <?php endif; ?>
    </form>
</div>