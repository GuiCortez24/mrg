<?php
/**
 * Componente: Modal para exibir o resumo do mês.
 * Os botões de ações avançadas (Comparar, Baixar PDF) são exibidos
 * com base na permissão 'pode_ver_comissao_total'.
 */
?>
<div class="modal fade" id="summaryModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="summaryModalLabel"><i class="bi bi-calendar-check-fill"></i> Resumo do Mês</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Fechar</button>
                
                <?php
                // ===================================================================
                // AJUSTE DE PERMISSÃO
                // Botões que dependem de dados de comissão só aparecem se o usuário tiver permissão.
                // ===================================================================
                if ($user_can_see_commission):
                ?>
                    <button type="button" id="compareYearBtn" class="btn btn-gradient"><i class="bi bi-bar-chart-fill"></i> Comparar Ano</button>
                    <a href="#" id="downloadPdfBtn" target="_blank" class="btn btn-danger"><i class="bi bi-file-earmark-pdf"></i> Baixar PDF do Resumo</a>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>