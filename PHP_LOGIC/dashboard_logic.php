<?php
/**
 * Localização: /PHP_LOGIC/dashboard_logic.php
 *
 * Responsável por toda a lógica de negócios da página do dashboard:
 * - Saudação ao usuário.
 * - Processamento dos filtros de busca.
 * - Construção e execução das queries de dados e contagem.
 * - Preparação das variáveis para a paginação.
 */

// Garante que a sessão seja iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Dependências
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../INCLUDES/functions.php';

// --- LÓGICA DA PÁGINA ---

// Saudação
$nome_usuario = $_SESSION['user_nome'] ?? 'Usuário';
date_default_timezone_set('America/Sao_Paulo');
$hora_atual = (int) date('H');

if ($hora_atual >= 5 && $hora_atual < 12) {
    $saudacao = "Bom dia, $nome_usuario!";
} elseif ($hora_atual >= 12 && $hora_atual < 18) {
    $saudacao = "Boa tarde, $nome_usuario!";
} else {
    $saudacao = "Boa noite, $nome_usuario!";
}

// Lógica de Paginação e Busca
$registros_por_pagina = 50;
$pagina_atual = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
$offset = ($pagina_atual - 1) * $registros_por_pagina;

// Processa filtros de busca
$search_nome = $_GET['search_nome'] ?? '';
$search_cpf = $_GET['search_cpf'] ?? '';
$search_item_segurado = $_GET['search_item_segurado'] ?? '';
$search_item = $_GET['search_item'] ?? '';
$search_vigencia_de = $_GET['search_vigencia_de'] ?? '';
$search_vigencia_ate = $_GET['search_vigencia_ate'] ?? '';

// Lógica de busca dinâmica
$sql_conditions = [];
$params = [];
$types = "";
$sql_base = "FROM clientes WHERE 1=1";
$calculated_end_date = "COALESCE(final_vigencia, DATE_ADD(inicio_vigencia, INTERVAL 1 YEAR))";

if (!empty($search_nome)) { $sql_conditions[] = "nome LIKE ?"; $params[] = "%$search_nome%"; $types .= "s"; }
if (!empty($search_cpf)) { $sql_conditions[] = "cpf LIKE ?"; $params[] = "%$search_cpf%"; $types .= "s"; }
if (!empty($search_item_segurado)) { $sql_conditions[] = "item_segurado LIKE ?"; $params[] = "%$search_item_segurado%"; $types .= "s"; }
if (!empty($search_item)) { $sql_conditions[] = "item_identificacao LIKE ?"; $params[] = "%$search_item%"; $types .= "s"; }

if (!empty($search_vigencia_de) && !empty($search_vigencia_ate)) {
    // Filtra para garantir que o início da vigência seja dentro do período, 
    // excluindo apólices que começaram muito antes
    $sql_conditions[] = "inicio_vigencia >= ? AND inicio_vigencia <= ?";
    array_push($params, $search_vigencia_de, $search_vigencia_ate);
    $types .= "ss";
}

if (!empty($sql_conditions)) {
    $sql_base .= " AND " . implode(" AND ", $sql_conditions);
}

// Query para contar o total de registros
$count_sql = "SELECT COUNT(*) AS total " . $sql_base;
$count_stmt = $conn->prepare($count_sql);
if ($types) { $count_stmt->bind_param($types, ...$params); }
$count_stmt->execute();
$total_registros = $count_stmt->get_result()->fetch_assoc()['total'];
$total_paginas = ceil($total_registros / $registros_por_pagina);
$count_stmt->close();

// Query para buscar os dados da página atual
// Ordena pela data de início da vigência e, em seguida, pela data final
$data_sql = "SELECT * " . $sql_base . " ORDER BY inicio_vigencia ASC, final_vigencia ASC LIMIT ? OFFSET ?";
$params[] = $registros_por_pagina;
$params[] = $offset;
$types .= "ii";
$data_stmt = $conn->prepare($data_sql);
if ($types) { $data_stmt->bind_param($types, ...$params); }
$data_stmt->execute();
$result = $data_stmt->get_result(); // A variável $result será usada na view
$data_stmt->close();

// Prepara a query string para os links de paginação
$query_string_params = array_filter([
    'search_nome' => $search_nome,
    'search_cpf' => $search_cpf,
    'search_item_segurado' => $search_item_segurado,
    'search_item' => $search_item,
    'search_vigencia_de' => $search_vigencia_de,
    'search_vigencia_ate' => $search_vigencia_ate
]);
$query_string = http_build_query($query_string_params);

?>