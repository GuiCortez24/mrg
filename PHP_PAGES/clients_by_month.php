<?php
include '../db.php';

$month = isset($_GET['month']) ? $_GET['month'] : date('m');
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');
$searchName = isset($_GET['name']) ? $_GET['name'] : '';
$searchCpf = isset($_GET['cpf']) ? $_GET['cpf'] : '';
$searchStatus = isset($_GET['status']) ? $_GET['status'] : '';

// Atualize a consulta SQL para incluir a busca
$sql = "SELECT * FROM clientes WHERE MONTH(inicio_vigencia) = '$month' AND YEAR(inicio_vigencia) = '$year'";

if ($searchName) {
    $searchName = $conn->real_escape_string($searchName);
    $sql .= " AND nome LIKE '%$searchName%'";
}

if ($searchCpf) {
    $searchCpf = $conn->real_escape_string($searchCpf);
    $sql .= " AND cpf LIKE '%$searchCpf%'";
}

if ($searchStatus && $searchStatus !== 'Todos') {
    $searchStatus = $conn->real_escape_string($searchStatus);
    $sql .= " AND status = '$searchStatus'";
}

$result = $conn->query($sql);

$months = [
    '01' => 'Janeiro',
    '02' => 'Fevereiro',
    '03' => 'Março',
    '04' => 'Abril',
    '05' => 'Maio',
    '06' => 'Junho',
    '07' => 'Julho',
    '08' => 'Agosto',
    '09' => 'Setembro',
    '10' => 'Outubro',
    '11' => 'Novembro',
    '12' => 'Dezembro'
];

$statuses = [
    'Todos',
    'Aguardando Emissão',
    'Emitida',
    'Cancelado',
];

$searchSeguradora = isset($_GET['seguradora']) ? $_GET['seguradora'] : '';
$searchTipoSeguro = isset($_GET['tipo_seguro']) ? $_GET['tipo_seguro'] : '';

$sql = "SELECT * FROM clientes WHERE MONTH(inicio_vigencia) = '$month' AND YEAR(inicio_vigencia) = '$year'";

if ($searchName) {
    $searchName = $conn->real_escape_string($searchName);
    $sql .= " AND nome LIKE '%$searchName%'";
}

if ($searchCpf) {
    $searchCpf = $conn->real_escape_string($searchCpf);
    $sql .= " AND cpf LIKE '%$searchCpf%'";
}

if ($searchStatus && $searchStatus !== 'Todos') {
    $searchStatus = $conn->real_escape_string($searchStatus);
    $sql .= " AND status = '$searchStatus'";
}

if ($searchSeguradora && $searchSeguradora !== 'Todas') {
    $searchSeguradora = $conn->real_escape_string($searchSeguradora);
    $sql .= " AND seguradora = '$searchSeguradora'";
}

if ($searchTipoSeguro && $searchTipoSeguro !== 'Todos') {
    $searchTipoSeguro = $conn->real_escape_string($searchTipoSeguro);
    $sql .= " AND tipo_seguro = '$searchTipoSeguro'";
}

$result = $conn->query($sql);



$seguradoras = [
    'Todas',
    'Aliro Seguro',
    'Allianz Seguros',
    'Azul Seguros',
    'HDI Seguros',
    'Liberty Seguros',
    'MAPFRE',
    'Unimed Seguros',
    'Porto Seguro',
    'Sompo Auto',
    'Tokio Marine Seguros',
    'Zurich Brasil Seguros',
    'Sancor Seguros',
    'Suhai',
    'Mitsui',
    'Sura Seguros',
    'EZZE',
    'Capemisa',
    'AKAD',
    'AssistCard',
    'AXA',
    'Ituran',
    'Pottencial',
    'SulAmerica',
    'VitalCard',
    'Bradesco'
];


$tiposSeguro = [
    'Todos',
    'Seguro Auto',
    'Seguro Residencial',
    'Acidentes Pessoais',
    'Seguro Moto',
    'Seguro de Vida',
    'Seguro Empresarial',
    'Consórcio',
    'Seguro Transporte',
    'Seguro Saúde',
    'Seguro Dental',
    'Seguro Frota',
    'Seguro Agronegócio'
];

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Clientes em <?php echo $months[$month]; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../CSS/index.css">
</head>

<body>
<div class="container mt-5">
    <h2 class="text-center">Clientes em <?php echo $months[$month]; ?> <?php echo $year; ?></h2>

    <div class="d-flex justify-content-between mt-3">
        <!-- Botão Voltar -->
        <button onclick="history.back()" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </button>
        
        <!-- Botão Gerar PDF -->
        <form method="GET" action="../PHP_ACTION/gerar_pdf.php" class="d-inline">
            <input type="hidden" name="month" value="<?php echo $month; ?>">
            <input type="hidden" name="year" value="<?php echo $year; ?>">
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-file-pdf"></i> Gerar PDF
            </button>
        </form>
    </div>




        <!-- Formulário de Filtros -->
        <form method="GET" action="clients_by_month.php" class="row g-3 mb-4">
            <input type="hidden" name="month" value="<?php echo $month; ?>">

            <!-- Ano -->
            <div class="col-md-2">
                <select name="year" class="form-select" onchange="this.form.submit()">
                    <option value="">Selecione o ano</option>
                    <?php for ($y = 2020; $y <= date('Y'); $y++): ?>
                        <option value="<?php echo $y; ?>" <?php if ($year == $y) echo 'selected'; ?>><?php echo $y; ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <!-- Nome -->
            <div class="col-md-3">
                <input type="text" name="name" class="form-control" placeholder="Pesquisar por Nome" value="<?php echo htmlspecialchars($searchName); ?>">
            </div>

            <!-- CPF -->
            <div class="col-md-3">
                <input type="text" name="cpf" class="form-control" placeholder="Pesquisar por CPF" value="<?php echo htmlspecialchars($searchCpf); ?>">
            </div>

            <!-- Status -->
            <div class="col-md-2">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Filtrar por status</option>
                    <?php foreach ($statuses as $status): ?>
                        <option value="<?php echo $status; ?>" <?php if ($searchStatus == $status) echo 'selected'; ?>><?php echo $status; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Seguradora -->
            <div class="col-md-2">
                <select name="seguradora" class="form-select" onchange="this.form.submit()">
                    <option value="">Filtrar por seguradora</option>
                    <option value="Todas">Todas</option>
                    <option value="Aliro Seguro">Aliro Seguro</option>
                    <option value="Allianz Seguros">Allianz Seguros</option>
                    <option value="Azul Seguros">Azul Seguros</option>
                    <option value="HDI Seguros">HDI Seguros</option>
                    <option value="Liberty Seguros">Liberty Seguros</option>
                    <option value="MAPFRE">MAPFRE</option>
                    <option value="Unimed Seguros">Unimed Seguros</option>
                    <option value="Porto Seguro">Porto Seguro</option>
                    <option value="Sompo Auto">Sompo Auto</option>
                    <option value="Tokio Marine Seguros">Tokio Marine Seguros</option>
                    <option value="Zurich Brasil Seguros">Zurich Brasil Seguros</option>
                    <option value="Sancor Seguros">Sancor Seguros</option>
                    <option value="Suhai">Suhai</option>
                    <option value="Mitsui">Mitsui</option>
                    <option value="Sura Seguros">Sura Seguros</option>
                    <option value="EZZE">EZZE</option>
                    <option value="Capemisa">Capemisa</option>
                    <option value="AKAD">AKAD</option>
                    <option value="AssistCard">AssistCard</option>
                    <option value="AXA">AXA</option>
                    <option value="Ituran">Ituran</option>
                    <option value="Pottencial">Pottencial</option>
                    <option value="SulAmerica">SulAmerica</option>
                    <option value="VitalCard">VitalCard</option>
                    <option value="Bradesco">Bradesco</option>
                    <option value="ItauSeguros">ItauSeguros</option>
                </select>
            </div>

            <!-- Botão de Busca -->
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary w-100">Buscar</button>
            </div>
        </form>

<!-- Lista de Clientes em Cards -->
<div class="row">
    <?php while ($row = $result->fetch_assoc()): ?>
        <?php
            $inicio_vigencia = new DateTime($row['inicio_vigencia']);
            $inicio_vigencia_formatado = $inicio_vigencia->format('d/m/Y');

            $final_vigencia = new DateTime($row['final_vigencia']);
            $final_vigencia_formatado = $final_vigencia->format('d/m/Y');

            $comissao_calculada = $row['premio_liquido'] * ($row['comissao'] / 100);

            // --- Cálculo de vigência ---
$inicioVigenciaDt = new DateTime($row['inicio_vigencia']);
$finalVigenciaDt  = new DateTime($row['final_vigencia']);
$intervalo        = $inicioVigenciaDt->diff($finalVigenciaDt);
$menorQueUmAno    = ($intervalo->y < 1);

// --- Escolha de classes com borda amarela se <1 ano ---
if ($menorQueUmAno) {
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
            <div class="card <?php echo $cardClass; ?> shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-user"></i> <?php echo htmlspecialchars($row['nome']); ?></h5>
                    <p><i class="fas fa-calendar-day"></i> <strong>Início Vigência:</strong> <?php echo $inicio_vigencia_formatado; ?></p>
                    <p><i class="fas fa-calendar-check"></i> <strong>Final Vigência:</strong> <?php echo $final_vigencia_formatado; ?></p>
                    <p><i class="fas fa-file-alt"></i> <strong>Proposta:</strong> <?php echo htmlspecialchars($row['apolice']); ?></p>
                    <p><i class="fas fa-building"></i> <strong>Seguradora:</strong> <?php echo htmlspecialchars($row['seguradora']); ?></p>
                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal-<?php echo $row['id']; ?>">
                        <i class="fas fa-info-circle"></i> Saiba Mais
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal para Detalhes do Cliente -->
        <div class="modal fade" id="modal-<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="modalLabel-<?php echo $row['id']; ?>" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header <?php echo $cardClass; ?>">
                        <h5 class="modal-title" id="modalLabel-<?php echo $row['id']; ?>"><i class="fas fa-user"></i> <?php echo htmlspecialchars($row['nome']); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p><i class="fas fa-id-card"></i> <strong>CPF:</strong> <?php echo htmlspecialchars($row['cpf']); ?></p>
                        <p><i class="fas fa-phone"></i> <strong>Celular:</strong> <?php echo htmlspecialchars($row['numero']); ?></p>
                        <p><i class="fas fa-envelope"></i> <strong>Email:</strong> <?php echo htmlspecialchars($row['email']); ?></p>
                        <p><i class="fas fa-dollar-sign"></i> <strong>Prêmio Líquido:</strong> <?php echo htmlspecialchars($row['premio_liquido']); ?></p>
                        <p><i class="fas fa-shield-alt"></i> <strong>Tipo de Seguro:</strong> <?php echo htmlspecialchars($row['tipo_seguro']); ?></p>
                        <p><i class="fas fa-percent"></i> <strong>Comissão (%):</strong> <?php echo htmlspecialchars($row['comissao']); ?></p>
                        <p><i class="fas fa-calculator"></i> <strong>Comissão Calculada:</strong> <?php echo htmlspecialchars($comissao_calculada); ?></p>
                        <p><i class="fas fa-calendar-check"></i> <strong>Final Vigência:</strong> <?php echo $final_vigencia_formatado; ?></p>
                        <p><i class="fas fa-tachometer-alt"></i> <strong>Status:</strong> <?php echo htmlspecialchars($row['status']); ?></p>
                        <?php if ($row['pdf_path']): ?>
                            <p><a href="<?php echo htmlspecialchars($row['pdf_path']); ?>" target="_blank"><i class="fas fa-file-pdf"></i> Visualizar PDF</a></p>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i> Fechar</button>
                        <a href="edit.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Editar</a>
                        <a href="../PHP_ACTION/delete.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Deletar</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
