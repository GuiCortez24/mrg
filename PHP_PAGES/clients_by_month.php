<?php
/**
 * Localização: /PHP_PAGES/clients_by_month.php
 * Exibe a produção (apólices iniciadas) em um mês/ano específico,
 * com filtro por Placa/ID adicionado.
 */

$page_title = "Produção do Mês";
include '../db.php';
include '../INCLUDES/functions.php';

include '../INCLUDES/header.php';

// --- LÓGICA DA PÁGINA ---

$month = $_GET['month'] ?? date('m');
$year = $_GET['year'] ?? date('Y');
$searchName = $_GET['name'] ?? '';
$searchCpf = $_GET['cpf'] ?? '';
$searchItem = $_GET['item_id'] ?? ''; // NOVO: Campo para Placa/ID
$searchStatus = $_GET['status'] ?? 'Todos';
$searchSeguradora = $_GET['seguradora'] ?? 'Todas';

$months = [
    '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril',
    '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto',
    '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
];
$statuses = ['Todos', 'Aguardando Emissão', 'Emitida', 'Cancelado'];
$seguradoras_result = $conn->query("SELECT nome FROM seguradoras ORDER BY nome ASC");

// Lógica de busca com prepared statements
$sql_conditions = ["MONTH(inicio_vigencia) = ?", "YEAR(inicio_vigencia) = ?"];
$params = [$month, $year];
$types = "ss";

if (!empty($searchName)) {
    $sql_conditions[] = "nome LIKE ?";
    $params[] = "%$searchName%";
    $types .= "s";
}
if (!empty($searchCpf)) {
    $sql_conditions[] = "cpf LIKE ?";
    $params[] = "%$searchCpf%";
    $types .= "s";
}
// NOVO: Adiciona a condição de busca para Placa/ID
if (!empty($searchItem)) {
    $sql_conditions[] = "item_identificacao LIKE ?";
    $params[] = "%$searchItem%";
    $types .= "s";
}
if (!empty($searchStatus) && $searchStatus !== 'Todos') {
    $sql_conditions[] = "status = ?";
    $params[] = $searchStatus;
    $types .= "s";
}
if (!empty($searchSeguradora) && $searchSeguradora !== 'Todas') {
    $sql_conditions[] = "seguradora = ?";
    $params[] = $searchSeguradora;
    $types .= "s";
}

$sql = "SELECT * FROM clientes WHERE " . implode(" AND ", $sql_conditions) . " ORDER BY inicio_vigencia ASC";
$stmt = $conn->prepare($sql);
if ($types) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<?php include '../INCLUDES/navbar.php'; ?>

<div class="container mt-4">
    <h2 class="text-center mb-4">
        <i class="bi bi-calendar-plus text-success"></i>
        Produção de <?php echo $months[$month] ?? 'Mês Inválido'; ?> de <?php echo htmlspecialchars($year); ?>
    </h2>

    <div class="card card-body shadow-sm mb-4">
        <form method="GET" action="clients_by_month.php">
            <input type="hidden" name="month" value="<?php echo htmlspecialchars($month); ?>">
            <div class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label-sm">Ano</label>
                    <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="" disabled <?php if (empty($year)) echo 'selected'; ?>>Selecione o ano</option>
                        <?php for ($y = 2020; $y <= date('Y') + 5; $y++): ?>
                            <option value="<?php echo $y; ?>" <?php if ($year == $y) echo 'selected'; ?>><?php echo $y; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label-sm">Nome</label>
                    <input type="text" name="name" class="form-control form-control-sm" placeholder="Buscar por Nome" value="<?php echo htmlspecialchars($searchName); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label-sm">CPF</label>
                    <input type="text" name="cpf" class="form-control form-control-sm" placeholder="Buscar por CPF" value="<?php echo htmlspecialchars($searchCpf); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label-sm">Placa / ID</label>
                    <input type="text" name="item_id" class="form-control form-control-sm" placeholder="Buscar por Placa/ID" value="<?php echo htmlspecialchars($searchItem); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label-sm">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <?php foreach ($statuses as $status): ?>
                            <option value="<?php echo $status; ?>" <?php if ($searchStatus == $status) echo 'selected'; ?>><?php echo $status; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label-sm">Seguradora</label>
                     <select name="seguradora" class="form-select form-select-sm">
                        <option value="Todas">Todas Seguradoras</option>
                        <?php while($row = $seguradoras_result->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($row['nome']); ?>" <?php if ($searchSeguradora == $row['nome']) echo 'selected'; ?>><?php echo htmlspecialchars($row['nome']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                 <div class="col-md-12 mt-3">
                    <button type="submit" class="btn btn-success btn-sm w-100"><i class="bi bi-search"></i> Filtrar</button>
                </div>
            </div>
        </form>
    </div>

    <div class="d-flex justify-content-between mb-4">
        <a href="months.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Voltar para Meses</a>
        <form method="GET" action="../PHP_ACTION/generate_pdf_producao.php" target="_blank">
            <input type="hidden" name="month" value="<?php echo htmlspecialchars($month); ?>">
            <input type="hidden" name="year" value="<?php echo htmlspecialchars($year); ?>">
            <button type="submit" class="btn btn-danger"><i class="bi bi-file-earmark-pdf"></i> Gerar PDF</button>
        </form>
    </div>

    <div class="row">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($cliente = $result->fetch_assoc()): ?>
                <?php include '../INCLUDES/cliente_card.php'; ?>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center mt-4">Nenhuma apólice iniciada neste mês com os filtros aplicados.</p>
        <?php endif; ?>
    </div>
</div>

<?php include '../INCLUDES/footer.php'; ?>