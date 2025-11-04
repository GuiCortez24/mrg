<?php
/**
 * Componente: Layout de cards para gerenciamento de usuários.
 * Exibe os usuários e suas permissões em um formato moderno e responsivo.
 */
?>

<div class="row">
    <?php if ($users_result->num_rows > 0): ?>
        <?php while($user = $users_result->fetch_assoc()): ?>
            <div class="col-12 col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm border-light-subtle">
                    
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fs-6 fw-bold">
                            <i class="bi bi-person-circle text-primary me-2"></i>
                            <?php echo htmlspecialchars($user['nome']); ?>
                        </h5>
                        <div class="actions">
                            <button type="button" class="btn btn-sm btn-outline-secondary edit-btn" title="Editar Usuário"
                                    data-bs-toggle="modal"
                                    data-bs-target="#userModal"
                                    data-id="<?php echo $user['id']; ?>"
                                    data-nome="<?php echo htmlspecialchars($user['nome']); ?>"
                                    data-email="<?php echo htmlspecialchars($user['email']); ?>"
                                    data-pode-ver-bi="<?php echo $user['pode_ver_bi']; ?>"
                                    data-pode-ver-comissao-total="<?php echo $user['pode_ver_comissao_total']; ?>"
                                    data-pode-ver-comissao-card="<?php echo $user['pode_ver_comissao_card']; ?>">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                            <a href="../PHP_ACTION/handle_users.php?action=delete&id=<?php echo $user['id']; ?>"
                               class="btn btn-sm btn-outline-danger" title="Excluir Usuário"
                               onclick="return confirm('Tem certeza que deseja excluir este usuário?');">
                               <i class="bi bi-trash-fill"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item px-0">
                                <i class="bi bi-envelope-fill text-muted me-2" style="width: 20px;"></i>
                                <span class="small"><?php echo htmlspecialchars($user['email']); ?></span>
                            </li>
                            <li class="list-group-item px-0">
                                <i class="bi bi-shield-check-fill text-muted me-2" style="width: 20px;"></i>
                                <strong class="small">Permissões:</strong>
                                <div class="mt-2">
                                    <?php
                                        $hasPermission = false;
                                        if ($user['pode_ver_bi']) {
                                            echo '<span class="badge text-bg-info me-1">BI/Análise</span>';
                                            $hasPermission = true;
                                        }
                                        if ($user['pode_ver_comissao_total']) {
                                            echo '<span class="badge text-bg-success me-1">Comissão Total</span>';
                                            $hasPermission = true;
                                        }
                                        if ($user['pode_ver_comissao_card']) {
                                            echo '<span class="badge text-bg-secondary me-1">Comissão Card</span>';
                                            $hasPermission = true;
                                        }
                                        if (!$hasPermission) {
                                            echo '<span class="small text-muted">Nenhuma permissão especial.</span>';
                                        }
                                    ?>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle-fill me-2"></i> Nenhum usuário cadastrado no sistema.
            </div>
        </div>
    <?php endif; ?>
</div>