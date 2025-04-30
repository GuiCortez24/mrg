<?php
include 'db.php';
include 'auth.php'; // Certifique-se de que auth.php gerencia a sessão e a autenticação

// Define o fuso horário para Brasília
date_default_timezone_set('America/Sao_Paulo');

// Número de registros por página
$registros_por_pagina = 50;

// Obter a página atual da URL, padrão é 1 se não for definido
$pagina_atual = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
$pagina_atual = max(1, $pagina_atual); // Garantir que a página atual seja pelo menos 1

// Calcular o OFFSET para a consulta
$offset = ($pagina_atual - 1) * $registros_por_pagina;

// Inicialização das variáveis de busca
$search_nome = '';
$search_cpf = '';
$search_vigencia_de = '';
$search_vigencia_ate = '';

// Verifique se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Processar exclusão de notificações
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_notification'])) {
    $notificacao_id = $_POST['notificacao_id'];

    // Excluir a notificação
    $stmt = $conn->prepare("DELETE FROM notificacoes WHERE id = ?");
    $stmt->bind_param("i", $notificacao_id);
    $stmt->execute();
    $stmt->close();

    // Redirecionar para evitar resubmissão do formulário
    header('Location: index.php');
    exit();
}

// Obter todas as notificações
$stmt = $conn->prepare("SELECT * FROM notificacoes ORDER BY data_hora DESC");
$stmt->execute();
$notificacoes_result = $stmt->get_result();

// Saudação com base na hora do dia
$nome_usuario = isset($_SESSION['user_nome']) ? htmlspecialchars($_SESSION['user_nome']) : 'Usuário';
$hora_atual = (int) date('H');

if ($hora_atual >= 5 && $hora_atual < 12) {
    $saudacao = "Bom dia, $nome_usuario!";
} elseif ($hora_atual >= 12 && $hora_atual < 18) {
    $saudacao = "Boa tarde, $nome_usuario!";
} else {
    $saudacao = "Boa noite, $nome_usuario!";
}

// Processar busca se o formulário for enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $search_nome = $_POST['search_nome'] ?? '';
    $search_cpf = $_POST['search_cpf'] ?? '';
    $search_vigencia_de = $_POST['search_vigencia_de'] ?? '';
    $search_vigencia_ate = $_POST['search_vigencia_ate'] ?? '';
}

// Inicializar o array de parâmetros
$params = ["%$search_nome%", "%$search_cpf%"];

// Preparar a consulta com base nos filtros de busca
$sql = "SELECT * FROM clientes WHERE nome LIKE ? AND cpf LIKE ?";

if ($search_vigencia_de && $search_vigencia_ate) {
    $sql .= " AND inicio_vigencia BETWEEN ? AND ?";
    $params[] = $search_vigencia_de;
    $params[] = $search_vigencia_ate;
} elseif ($search_vigencia_de) {
    $sql .= " AND inicio_vigencia >= ?";
    $params[] = $search_vigencia_de;
} elseif ($search_vigencia_ate) {
    $sql .= " AND inicio_vigencia <= ?";
    $params[] = $search_vigencia_ate;
}

// Adicionar LIMIT e OFFSET
$sql .= " LIMIT ? OFFSET ?";
$params[] = $registros_por_pagina;
$params[] = $offset;

$stmt = $conn->prepare($sql);
$stmt->bind_param(str_repeat('s', count($params) - 2) . 'ii', ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Contar o total de registros
$count_sql = "SELECT COUNT(*) AS total FROM clientes WHERE nome LIKE ? AND cpf LIKE ?";
$count_params = ["%$search_nome%", "%$search_cpf%"];

if ($search_vigencia_de && $search_vigencia_ate) {
    $count_sql .= " AND inicio_vigencia BETWEEN ? AND ?";
    $count_params[] = $search_vigencia_de;
    $count_params[] = $search_vigencia_ate;
} elseif ($search_vigencia_de) {
    $count_sql .= " AND inicio_vigencia >= ?";
    $count_params[] = $search_vigencia_de;
} elseif ($search_vigencia_ate) {
    $count_sql .= " AND inicio_vigencia <= ?";
    $count_params[] = $search_vigencia_ate;
}

$count_stmt = $conn->prepare($count_sql);
$count_stmt->bind_param(str_repeat('s', count($count_params)), ...$count_params);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_registros = $count_result->fetch_assoc()['total'];

// Calcular o número total de páginas
$total_paginas = ceil($total_registros / $registros_por_pagina);

// Função para formatar a data
function formatDate($date)
{
    return date('d/m/Y', strtotime($date));
}
?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="icon" href="IMG/logoM.png" type="image/x-icon">
    <link rel="stylesheet" href="CSS/index.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <i class="bi bi-shield-fill" style="font-size: 2rem; color: #0d6efd;"></i>
                <i class="bi bi-person-circle ms-2" style="font-size: 1.5rem; color: #0d6efd;"></i>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="PHP_PAGES/months.php">
                            <i class="bi bi-people"></i> Clientes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="PHP_PAGES/info_loja.php">
                            <i class="bi bi-speedometer2"></i> Info MRG
                        </a>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="PHP_ACTION/logout.php" class="nav-link m-0 p-0">
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </button>
                        </form>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link position-relative" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="bi bi-bell"></i>
                            <?php if ($notificacoes_result->num_rows > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?php echo $notificacoes_result->num_rows; ?>
                                </span>
                            <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown">
                            <?php if ($notificacoes_result->num_rows > 0): ?>
                                <?php while ($notificacao = $notificacoes_result->fetch_assoc()): ?>
                                    <li class="dropdown-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="bi bi-info-circle-fill text-primary"></i>
                                            <?php echo htmlspecialchars($notificacao['mensagem']); ?>
                                            <small class="text-muted d-block"><?php echo date('d/m/Y H:i', strtotime($notificacao['data_hora'])); ?></small>
                                        </div>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="notificacao_id" value="<?php echo $notificacao['id']; ?>">
                                            <button type="submit" name="delete_notification" class="btn btn-sm btn-outline-danger ms-2">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </form>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <li><span class="dropdown-item">Sem notificações</span></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Modal de Notificações -->
    <div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notificationModalLabel">
                        <i class="bi bi-bell-fill"></i> Notificações
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if ($notificacoes_result->num_rows > 0): ?>
                        <?php while ($notificacao = $notificacoes_result->fetch_assoc()): ?>
                            <div class="notification-item d-flex align-items-start mb-3">
                                <i class="bi bi-info-circle-fill text-primary me-2"></i>
                                <div>
                                    <p class="mb-1"><?php echo htmlspecialchars($notificacao['mensagem']); ?></p>
                                    <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($notificacao['data_hora'])); ?></small>
                                </div>
                                <form method="POST" class="ms-3" style="display:inline;">
                                    <input type="hidden" name="notificacao_id" value="<?php echo $notificacao['id']; ?>">
                                    <button type="submit" name="delete_notification" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                            <hr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-center">Sem notificações</p>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <p class="saudacao"><?php echo $saudacao; ?></p>
        <h2 class="text-center mb-4"><i class="bi bi-clipboard-data"></i> Gerenciamento de Clientes</h2>
        <form method="POST" action="index.php" class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <label for="search_nome"><i class="bi bi-person"></i> Nome</label>
                    <input type="text" class="form-control" id="search_nome" name="search_nome" placeholder="Buscar por Nome" value="<?php echo htmlspecialchars($search_nome); ?>">
                </div>
                <div class="col-md-3">
                    <label for="search_cpf"><i class="bi bi-credit-card"></i> CPF</label>
                    <input type="text" class="form-control" id="search_cpf" name="search_cpf" placeholder="Buscar por CPF" value="<?php echo htmlspecialchars($search_cpf); ?>">
                </div>
                <div class="col-md-2">
                    <label for="search_vigencia_de"><i class="bi bi-calendar"></i> Vigência De</label>
                    <input type="date" class="form-control" id="search_vigencia_de" name="search_vigencia_de" value="<?php echo htmlspecialchars($search_vigencia_de); ?>">
                </div>
                <div class="col-md-2">
                    <label for="search_vigencia_ate"><i class="bi bi-calendar"></i> Vigência Até</label>
                    <input type="date" class="form-control" id="search_vigencia_ate" name="search_vigencia_ate" value="<?php echo htmlspecialchars($search_vigencia_ate); ?>">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-success w-100"><i class="bi bi-search"></i> Buscar</button>
                </div>
            </div>
        </form>
        <div class="d-flex gap-2 mb-4">
    <!-- Botão de Relatório que abre o modal -->
    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#reportModal">
        <i class="bi bi-file-earmark-arrow-down"></i> Relatório
    </button>
</div>

<!-- Modal para Escolha do Tipo de Relatório e Período -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportModalLabel"><i class="bi bi-file-earmark"></i> Gerar Relatório</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Formulário para Seleção do Tipo de Relatório e Período -->
                <form action="PHP_ACTION/generate_report.php" method="GET" target="_blank">
                    <div class="mb-3">
                        <label for="reportType" class="form-label"><i class="bi bi-file-text"></i> Tipo de Relatório</label>
                        <select class="form-select" id="reportType" name="reportType" required>
                            <option value="Renovacao">Renovação</option>
                            <!-- Outros tipos de relatórios podem ser adicionados aqui -->
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="startDate" class="form-label"><i class="bi bi-calendar"></i> Data Início</label>
                            <input type="date" class="form-control" id="startDate" name="startDate" required>
                        </div>
                        <div class="col-md-6">
                            <label for="endDate" class="form-label"><i class="bi bi-calendar"></i> Data Fim</label>
                            <input type="date" class="form-control" id="endDate" name="endDate" required>
                        </div>
                    </div>
                    <div class="modal-footer mt-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-file-earmark-arrow-down"></i> Gerar Relatório
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

        <a href="PHP_PAGES/add.php" class="btn btn-primary mb-3"><i class="bi bi-plus-lg"></i> Adicionar Cliente</a>
        <h3>Lista Geral</h3>
        <div class="row">
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php
                    // Define o estilo do cartão com base no status
                    $inicioVigencia = new DateTime($row['inicio_vigencia']);
$fimVigencia    = new DateTime($row['final_vigencia']);
$intervalo      = $inicioVigencia->diff($fimVigencia);

$vigenciaMenorQueUmAno = $intervalo->y < 1;

if ($vigenciaMenorQueUmAno) {
    // Borda amarela, fundo claro e texto conforme status
    switch ($row['status']) {
        case 'Emitida':
            $cardClass = 'bg-light border border-warning text-success';
            break;
        case 'Cancelado':
            $cardClass = 'bg-light border border-warning text-danger';
            break;
        default:
            $cardClass = 'bg-light border border-warning text-primary';
            break;
    }
} else {
    // Comportamento original
    switch ($row['status']) {
        case 'Emitida':
            $cardClass = 'bg-light border border-success text-success';
            break;
        case 'Cancelado':
            $cardClass = 'bg-light border border-danger text-danger';
            break;
        default:
            $cardClass = 'bg-light border border-primary text-primary';
            break;
    }
}

                ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 <?php echo $cardClass; ?>">
                        <div class="card-body">
                            <h5 class="card-title"><i class="bi bi-person"></i> <?php echo htmlspecialchars($row['nome']); ?></h5>
                            <p class="card-text">
                                <strong><i class="bi bi-building"></i> Seguradora:</strong> <?php echo htmlspecialchars($row['seguradora']); ?><br>
                                <strong><i class="bi bi-calendar-date"></i> Vigência:</strong> <?php echo formatDate($row['inicio_vigencia']); ?><br>
                                <strong><i class="bi bi-calendar-date"></i> Final da Vigência:</strong> <?php echo formatDate($row['final_vigencia']); ?><br>
                                <strong><i class="bi bi-file-earmark"></i> Proposta:</strong> <?php echo htmlspecialchars($row['apolice']); ?>
                            </p>
                            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal-<?php echo $row['id']; ?>">
                                <i class="bi bi-info-circle"></i> Saiba Mais
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="modal-<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="modalLabel-<?php echo $row['id']; ?>" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalLabel-<?php echo $row['id']; ?>">Detalhes da Proposta</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p><strong><i class="bi bi-credit-card"></i> CPF:</strong> <?php echo htmlspecialchars($row['cpf']); ?></p>
                                <p><strong><i class="bi bi-phone"></i> Celular:</strong> <?php echo htmlspecialchars($row['numero']); ?></p>
                                <p><strong><i class="bi bi-envelope"></i> Email:</strong> <?php echo htmlspecialchars($row['email']); ?></p>
                                <p><strong><i class="bi bi-cash-stack"></i> Prêmio Líquido:</strong> <?php echo htmlspecialchars($row['premio_liquido']); ?></p>
                                <p><strong><i class="bi bi-shield"></i> Tipo de Seguro:</strong> <?php echo htmlspecialchars($row['tipo_seguro']); ?></p>
                                <p><strong><i class="bi bi-percent"></i> Comissão (%):</strong> <?php echo htmlspecialchars($row['comissao']); ?></p>
                                <p><strong><i class="bi bi-calculator"></i> Comissão Calculada:</strong> <?php echo htmlspecialchars($row['premio_liquido'] * ($row['comissao'] / 100)); ?></p>
                                <p><strong><i class="bi bi-tachometer"></i> Status:</strong> <?php echo htmlspecialchars($row['status']); ?></p>
                                <p><strong><i class="bi bi-calendar-range"></i> Final Vigência:</strong> <?php echo formatDate($row['final_vigencia']); ?></p>
                                <?php if ($row['pdf_path']): ?>
                                    <p><a href="uploads/<?php echo htmlspecialchars($row['pdf_path']); ?>" target="_blank" class="text-decoration-none text-primary"><i class="bi bi-file-earmark-pdf"></i> Visualizar PDF</a></p>
                                <?php endif; ?>
                            </div>
                            <div class="modal-footer">
                                <a href="PHP_PAGES/edit.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-pencil"></i></a>
                                <a href="PHP_ACTION/delete.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-trash"></i></a>
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal"><i class="bi bi-x"></i> Fechar</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        <?php if ($pagina_atual > 1): ?>
            <a href="?pagina=<?php echo $pagina_atual - 1; ?>" class="btn btn-primary mx-2">
                <i class="bi bi-chevron-left"></i> Página Anterior
            </a>
        <?php endif; ?>
        <?php if ($pagina_atual < $total_paginas): ?>
            <a href="?pagina=<?php echo $pagina_atual + 1; ?>" class="btn btn-primary mx-2">
                Página Seguinte <i class="bi bi-chevron-right"></i>
            </a>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="JS/index.js"></script>
</body>
</html>