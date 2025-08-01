<?php
/**
 * Localização: INCLUDES/cliente_card.php
 * Exibe um card de cliente e seus modais, incluindo Item Segurado, múltiplos PDFs e Anotações.
 */

// Este componente espera que a variável $cliente já esteja definida.

// --- LÓGICA DE COR AJUSTADA ---

$borderColorClass = '';
$textColorClass = '';

// Garante que a final_vigencia seja tratada mesmo se for nula, calculando 1 ano
$inicioVigencia = new DateTime($cliente['inicio_vigencia']);
$finalVigencia = $cliente['final_vigencia'] ? new DateTime($cliente['final_vigencia']) : (clone $inicioVigencia)->add(new DateInterval('P1Y'));

$intervalo = $inicioVigencia->diff($finalVigencia);
// Considera vigência curta apenas se for menor que um ano e maior que zero dias
$isVigenciaCurta = $intervalo->y < 1 && ($intervalo->days > 0);


// 1. Define a COR DO TEXTO baseada SEMPRE no status
switch ($cliente['status']) {
    case 'Emitida':
        $textColorClass = 'text-success';
        break;
    case 'Cancelado':
        $textColorClass = 'text-danger';
        break;
    default: // 'Aguardando Emissão' e outros
        $textColorClass = 'custom-text-blue';
        break;
}

// 2. Define a COR DA BORDA
if ($isVigenciaCurta) {
    // Se a vigência for curta, a borda é SEMPRE amarela
    $borderColorClass = 'border-warning';
} else {
    // Se não for curta, a borda segue a mesma lógica do texto
    switch ($cliente['status']) {
        case 'Emitida':
            $borderColorClass = 'border-success';
            break;
        case 'Cancelado':
            $borderColorClass = 'border-danger';
            break;
        default:
            $borderColorClass = 'custom-border-blue';
            break;
    }
}
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
                    <li class="list-group-item"><i class="bi bi-percent text-muted me-2"></i> <strong>Comissão:</strong> <?php echo htmlspecialchars($cliente['comissao']); ?>%</li>
                    <li class="list-group-item"><i class="bi bi-calculator text-muted me-2"></i> <strong>Valor Comissão:</strong> R$ <?php echo number_format($cliente['premio_liquido'] * ($cliente['comissao'] / 100), 2, ',', '.'); ?></li>
                    <li class="list-group-item"><i class="bi bi-tags text-muted me-2"></i> <strong>Status:</strong> <?php echo htmlspecialchars($cliente['status']); ?></li>
                    
                    <?php if(isset($cliente['tipo_operacao'])): ?>
                        <li class="list-group-item"><i class="bi bi-arrow-repeat text-muted me-2"></i> <strong>Tipo de Operação:</strong> <?php echo htmlspecialchars($cliente['tipo_operacao']); ?></li>
                    <?php endif; ?>

                    <li class="list-group-item">
                        <i class="bi bi-paperclip text-muted me-2"></i> <strong>Anexos:</strong>
                        <?php
                        $pdf_data = $cliente['pdf_path'];
                        $pdfs = json_decode($pdf_data, true);

                        if (is_array($pdfs) && !empty($pdfs)) { // Formato novo (lista JSON)
                            echo '<ul class="list-unstyled mt-2" style="padding-left: 20px;">';
                            foreach ($pdfs as $pdf_path) {
                                echo '<li><a href="../' . htmlspecialchars($pdf_path) . '" target="_blank"><i class="bi bi-file-earmark-pdf text-danger"></i> ' . htmlspecialchars(basename($pdf_path)) . '</a></li>';
                            }
                            echo '</ul>';
                        } elseif (!is_array($pdfs) && !empty($pdf_data)) { // Formato antigo (string simples)
                            echo '<ul class="list-unstyled mt-2" style="padding-left: 20px;">';
                            echo '<li><a href="../' . htmlspecialchars($pdf_data) . '" target="_blank"><i class="bi bi-file-earmark-pdf text-danger"></i> ' . htmlspecialchars(basename($pdf_data)) . '</a></li>';
                            echo '</ul>';
                        } else { // Nenhum anexo
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

<div class="modal fade" id="notesModal-<?php echo $cliente['id']; ?>" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-journal-text"></i> Anotações sobre <?php echo htmlspecialchars(strtok($cliente['nome'], ' ')); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="../PHP_ACTION/handle_notes.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="cliente_id" value="<?php echo $cliente['id']; ?>">
                    <div class="form-group">
                        <label for="anotacoes-<?php echo $cliente['id']; ?>" class="form-label">Digite suas anotações abaixo:</label>
                        <textarea class="form-control" id="anotacoes-<?php echo $cliente['id']; ?>" name="anotacoes" rows="8"><?php echo htmlspecialchars($cliente['anotacoes'] ?? ''); ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Salvar Anotações</button>
                </div>
            </form>
        </div>
    </div>
</div>