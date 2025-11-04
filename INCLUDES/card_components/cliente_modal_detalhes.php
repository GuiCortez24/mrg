<?php
/**
 * Componente do modal com os detalhes completos da proposta.
 * A visualização dos dados de comissão é controlada pela função hasPermission().
 */
?>
<div class="modal fade" id="modal-<?php echo $cliente['id']; ?>" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalhes da Proposta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><i class="bi bi-person text-muted me-2"></i> <strong>Nome:</strong> <?php echo htmlspecialchars($cliente['nome']); ?></li>
                    <li class="list-group-item"><i class="bi bi-credit-card text-muted me-2"></i> <strong>CPF/CNPJ:</strong> <?php echo htmlspecialchars($cliente['cpf']); ?></li>
                    <li class="list-group-item"><i class="bi bi-phone text-muted me-2"></i> <strong>Celular:</strong> <?php echo htmlspecialchars($cliente['numero']); ?></li>
                    <li class="list-group-item"><i class="bi bi-envelope text-muted me-2"></i> <strong>Email:</strong> <?php echo htmlspecialchars($cliente['email']); ?></li>
                </ul>
                <hr>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><i class="bi bi-shield-check text-muted me-2"></i> <strong>Ramo:</strong> <?php echo htmlspecialchars($cliente['tipo_seguro']); ?></li>
                    <?php if(!empty($cliente['item_segurado'])): ?>
                        <li class="list-group-item"><i class="bi bi-box-seam text-muted me-2"></i> <strong>Item Segurado:</strong> <?php echo htmlspecialchars($cliente['item_segurado']); ?></li>
                    <?php endif; ?>
                    <?php if(!empty($cliente['item_identificacao'])): ?>
                        <li class="list-group-item"><i class="bi bi-hash text-muted me-2"></i> <strong>Placa/ID:</strong> <?php echo htmlspecialchars($cliente['item_identificacao']); ?></li>
                    <?php endif; ?>
                    <li class="list-group-item"><i class="bi bi-cash-stack text-muted me-2"></i> <strong>Prêmio Líquido:</strong> R$ <?php echo number_format($cliente['premio_liquido'], 2, ',', '.'); ?></li>
                    
                    <?php // --- AJUSTE DE PERMISSÃO APLICADO AQUI ---
                    if (hasPermission('pode_ver_comissao_card')): ?>
                        <li class="list-group-item"><i class="bi bi-percent text-muted me-2"></i> <strong>Comissão:</strong> <?php echo htmlspecialchars($cliente['comissao']); ?>%</li>
                        <li class="list-group-item"><i class="bi bi-calculator text-muted me-2"></i> <strong>Valor Comissão:</strong> R$ <?php echo number_format($cliente['premio_liquido'] * ($cliente['comissao'] / 100), 2, ',', '.'); ?></li>
                    <?php endif; ?>

                    <li class="list-group-item"><i class="bi bi-tags text-muted me-2"></i> <strong>Status:</strong> <?php echo htmlspecialchars($cliente['status']); ?></li>
                    <?php if(isset($cliente['tipo_operacao'])): ?>
                        <li class="list-group-item"><i class="bi bi-arrow-repeat text-muted me-2"></i> <strong>Tipo de Operação:</strong> <?php echo htmlspecialchars($cliente['tipo_operacao']); ?></li>
                    <?php endif; ?>
                    <li class="list-group-item">
                        <i class="bi bi-paperclip text-muted me-2"></i> <strong>Anexos:</strong>
                        <?php
                        $pdf_data = $cliente['pdf_path'];
                        $pdfs = json_decode($pdf_data, true);
                        if (is_array($pdfs) && !empty($pdfs)) {
                            echo '<ul class="list-unstyled mt-2 ps-3">';
                            foreach ($pdfs as $pdf_path) {
                                echo '<li><a href="../' . htmlspecialchars($pdf_path) . '" target="_blank"><i class="bi bi-file-earmark-pdf text-danger"></i> ' . htmlspecialchars(basename($pdf_path)) . '</a></li>';
                            }
                            echo '</ul>';
                        } elseif (!empty($pdf_data)) {
                            echo '<ul class="list-unstyled mt-2 ps-3">';
                            echo '<li><a href="../' . htmlspecialchars($pdf_data) . '" target="_blank"><i class="bi bi-file-earmark-pdf text-danger"></i> ' . htmlspecialchars(basename($pdf_data)) . '</a></li>';
                            echo '</ul>';
                        } else {
                            echo ' (Nenhum anexo)';
                        }
                        ?>
                    </li>
                </ul>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-info text-white me-auto" data-bs-toggle="modal" data-bs-target="#notesModal-<?php echo $cliente['id']; ?>">
                    <i class="bi bi-journal-text"></i> Anotações
                </button>
                <a href="edit.php?id=<?php echo $cliente['id']; ?>" class="btn btn-secondary"><i class="bi bi-pencil"></i> Editar</a>
                <a href="../PHP_ACTION/delete.php?id=<?php echo $cliente['id']; ?>" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja excluir este cliente?');"><i class="bi bi-trash"></i> Excluir</a>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><i class="bi bi-x"></i> Fechar</button>
            </div>
        </div>
    </div>
</div>