<?php
/**
 * Localização: /PHP_PAGES/dashboard.php
 * Painel principal do sistema, agora refatorado com componentes.
 */

$page_title = "Gerenciamento de Clientes";
include '../db.php';
include '../INCLUDES/functions.php';
include '../INCLUDES/header.php';

// --- LÓGICA DA PÁGINA ---

// Saudação
$nome_usuario = $_SESSION['user_nome'] ?? 'Usuário';
date_default_timezone_set('America/Sao_Paulo');
$hora_atual = (int) date('H');
if ($hora_atual >= 5 && $hora_atual < 12) { $saudacao = "Bom dia, $nome_usuario!"; }
elseif ($hora_atual >= 12 && $hora_atual < 18) { $saudacao = "Boa tarde, $nome_usuario!"; }
else { $saudacao = "Boa noite, $nome_usuario!"; }

// Busca por Notificações
$notificacoes_result = $conn->query("SELECT * FROM notificacoes ORDER BY data_hora DESC");

// Lógica de Paginação e Busca
$registros_por_pagina = 50;
$pagina_atual = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
$offset = ($pagina_atual - 1) * $registros_por_pagina;

// Processa filtros de busca
$search_nome = $_REQUEST['search_nome'] ?? '';
$search_cpf = $_REQUEST['search_cpf'] ?? '';
$search_item_segurado = $_REQUEST['search_item_segurado'] ?? '';
$search_item = $_REQUEST['search_item'] ?? '';
$search_vigencia_de = $_REQUEST['search_vigencia_de'] ?? '';
$search_vigencia_ate = $_REQUEST['search_vigencia_ate'] ?? '';

// Lógica de busca dinâmica
$sql_conditions = [];
$params = [];
$types = "";
$sql_base = "FROM clientes WHERE 1=1";

if (!empty($search_nome)) { $sql_conditions[] = "nome LIKE ?"; $params[] = "%$search_nome%"; $types .= "s"; }
if (!empty($search_cpf)) { $sql_conditions[] = "cpf LIKE ?"; $params[] = "%$search_cpf%"; $types .= "s"; }
if (!empty($search_item_segurado)) { $sql_conditions[] = "item_segurado LIKE ?"; $params[] = "%$search_item_segurado%"; $types .= "s"; }
if (!empty($search_item)) { $sql_conditions[] = "item_identificacao LIKE ?"; $params[] = "%$search_item%"; $types .= "s"; }
if (!empty($search_vigencia_de)) { $sql_conditions[] = "final_vigencia >= ?"; $params[] = $search_vigencia_de; $types .= "s"; }
if (!empty($search_vigencia_ate)) { $sql_conditions[] = "final_vigencia <= ?"; $params[] = $search_vigencia_ate; $types .= "s"; }

if (!empty($sql_conditions)) {
    $sql_base .= " AND " . implode(" AND ", $sql_conditions);
}

// Query para contar o total
$count_sql = "SELECT COUNT(*) AS total " . $sql_base;
$count_stmt = $conn->prepare($count_sql);
if ($types) { $count_stmt->bind_param($types, ...$params); }
$count_stmt->execute();
$total_registros = $count_stmt->get_result()->fetch_assoc()['total'];
$total_paginas = ceil($total_registros / $registros_por_pagina);
$count_stmt->close();

// Query para buscar os dados
$data_sql = "SELECT * " . $sql_base . " ORDER BY final_vigencia ASC LIMIT ? OFFSET ?";
$params[] = $registros_por_pagina;
$params[] = $offset;
$types .= "ii";
$data_stmt = $conn->prepare($data_sql);
if ($types) { $data_stmt->bind_param($types, ...$params); }
$data_stmt->execute();
$result = $data_stmt->get_result();
$data_stmt->close();

// Prepara a string de consulta para a paginação
$query_string = http_build_query([
    'search_nome' => $search_nome,
    'search_cpf' => $search_cpf,
    'search_item_segurado' => $search_item_segurado,
    'search_item' => $search_item,
    'search_vigencia_de' => $search_vigencia_de,
    'search_vigencia_ate' => $search_vigencia_ate
]);

// --- VISUALIZAÇÃO (HTML) ---
?>

<?php include '../INCLUDES/navbar.php'; ?>

<div class="container mt-4">
    <p class="h5"><?php echo $saudacao; ?></p>
    <h2 class="mb-4"><i class="bi bi-clipboard-data"></i> Painel de Gerenciamento</h2>

    <?php include '../INCLUDES/dashboard_search_form.php'; ?>

    <div class="d-flex gap-2 mb-4">
        <a href="add.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Adicionar Proposta</a>
        <button type="button" class="btn btn-info text-white" data-bs-toggle="modal" data-bs-target="#reportModal">
            <i class="bi bi-file-earmark-arrow-down"></i> Relatório
        </button>
    </div>

    <div class="row">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($cliente = $result->fetch_assoc()): ?>
                <?php include '../INCLUDES/cliente_card.php'; ?>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center mt-4">Nenhum cliente encontrado com os filtros aplicados.</p>
        <?php endif; ?>
    </div>

    <?php include '../INCLUDES/pagination.php'; ?>
</div>

<div class="modal fade" id="reportModal" tabindex="-1">
    </div>

<?php include '../INCLUDES/footer.php'; ?>