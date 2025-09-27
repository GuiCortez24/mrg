<?php
// INCLUDES/navbar.php
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">
            <i class="bi bi-shield-fill text-success" style="font-size: 2rem;"></i>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                
                <li class="nav-item">
                    <a class="nav-link" href="relatorio_bi.php"><i class="bi bi-bar-chart-line-fill"></i> BI / Análise</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="months.php"><i class="bi bi-calendar3-week"></i> Produção Mensal</a>
                </li>
                
                <?php
                if (isset($_SESSION['user_email'])) {
                    $user_email = $_SESSION['user_email'];
                    if ($user_email == 'ja@mrgseguros.com.br' || $user_email == 'william@mrgseguros.com.br') {
                        // Se for um dos e-mails de admin, exibe o link
                        echo '<li class="nav-item">';
                        echo '    <a class="nav-link" href=""></i> Fluxo de Caixa</a>';
                        echo '</li>';
                    }
                }
                ?>
                <li class="nav-item">
                    <a class="nav-link" href="info_loja.php"><i class="bi bi-building"></i> Seguradoras</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link position-relative" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell" style="font-size: 1.2rem;"></i>
                        <?php if (isset($notificacoes_result) && $notificacoes_result->num_rows > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?php echo $notificacoes_result->num_rows; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" aria-labelledby="notificationDropdown" style="width: 380px; max-height: 400px; overflow-y: auto;">
                        <li class="px-3 py-2">
                            <h6 class="mb-0">Notificações</h6>
                        </li>
                        <li><hr class="dropdown-divider my-0"></li>
                        
                        <?php if (isset($notificacoes_result) && $notificacoes_result->num_rows > 0):
                                $notificacoes_result->data_seek(0);
                                while ($notificacao = $notificacoes_result->fetch_assoc()): ?>
                            <li>
                                <div class="dropdown-item d-flex align-items-start py-2">
                                    <i class="bi bi-info-circle text-success me-3 mt-1"></i>
                                    <div class="flex-grow-1">
                                        <p class="mb-0 small"><?php echo htmlspecialchars($notificacao['mensagem']); ?></p>
                                        <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($notificacao['data_hora'])); ?></small>
                                    </div>
                                    <form method="POST" action="../PHP_ACTION/delete_notification.php" class="ms-2">
                                        <input type="hidden" name="notificacao_id" value="<?php echo $notificacao['id']; ?>">
                                        <button type="submit" name="delete_one" title="Marcar como lida" class="btn btn-sm btn-light border-0 p-0"><i class="bi bi-x"></i></button>
                                    </form>
                                </div>
                            </li>
                        <?php endwhile;
                                else: ?>
                            <li><span class="dropdown-item text-center text-muted py-3">Sem novas notificações</span></li>
                        <?php endif; ?>
                        
                        <?php if (isset($notificacoes_result) && $notificacoes_result->num_rows > 0): ?>
                        <li><hr class="dropdown-divider my-0"></li>
                        <li class="text-center py-2">
                            <form method="POST" action="../PHP_ACTION/delete_notification.php">
                                <button type="submit" name="delete_all" class="btn btn-link btn-sm text-danger">
                                    <i class="bi bi-trash"></i> Limpar todas as notificações
                                </button>
                            </form>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <li class="nav-item ms-2">
                    <form method="POST" action="../PHP_ACTION/logout.php">
                        <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-box-arrow-right"></i> Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>