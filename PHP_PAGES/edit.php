<?php
include '../db.php';
session_start(); // Certifique-se de que a sessão está iniciada

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $inicio_vigencia = $_POST['inicio_vigencia'];
    $apolice = $_POST['apolice'];
    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $numero = $_POST['numero'];
    $email = $_POST['email'];
    $premio_liquido = $_POST['premio_liquido'];
    $comissao = $_POST['comissao'];
    $status = $_POST['status'];
    $observacoes = $_POST['observacoes'];
    $seguradora = isset($_POST['seguradora']) ? $_POST['seguradora'] : ''; // Valor padrão
    $tipo_seguro = isset($_POST['tipo_seguro']) ? $_POST['tipo_seguro'] : ''; // Valor padrão

    // Registrar a notificação
    $usuario_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT nome FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $user_result = $stmt->get_result();
    $usuario_nome = $user_result->fetch_assoc()['nome'];
    date_default_timezone_set('America/Sao_Paulo');

    $stmt = $conn->prepare("SELECT nome, pdf_path FROM clientes WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $cliente_result = $stmt->get_result();
    $cliente_data = $cliente_result->fetch_assoc();
    $nome_cliente = $cliente_data['nome'];
    $pdf_path = $cliente_data['pdf_path'];

    $mensagem = "Usuário $usuario_nome atualizou proposta de $nome_cliente - " . date('Y-m-d H:i:s');
    $stmt = $conn->prepare("INSERT INTO notificacoes (usuario_id, mensagem, data_hora) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $usuario_id, $mensagem, date('Y-m-d H:i:s'));
    $stmt->execute();

    if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] == UPLOAD_ERR_OK) {
        $pdf_name = $_FILES['pdf']['name'];
        $pdf_tmp = $_FILES['pdf']['tmp_name'];
        $pdf_path = '../uploads/' . basename($pdf_name);
        move_uploaded_file($pdf_tmp, $pdf_path);
    }

    // Atualize a instrução SQL para a atualização
    $stmt = $conn->prepare("UPDATE clientes 
                            SET inicio_vigencia = ?, apolice = ?, nome = ?, cpf = ?, numero = ?, email = ?, premio_liquido = ?, comissao = ?, status = ?, seguradora = ?, tipo_seguro = ?, pdf_path = ?, observacoes = ?
                            WHERE id = ?");

    // Bind os parâmetros
    $stmt->bind_param('sssssssssssssi', $inicio_vigencia, $apolice, $nome, $cpf, $numero, $email, $premio_liquido, $comissao, $status, $seguradora, $tipo_seguro, $pdf_path, $observacoes, $id);

    if ($stmt->execute()) {
        header('Location: ../index.php');
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    $stmt = $conn->prepare("SELECT * FROM clientes WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cliente</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Custom CSS -->
</head>

<body>
    <div class="container mt-5">
        <div class="card shadow-lg">
            <div class="card-header text-center bg-primary text-white">
                <h2><i class="bi bi-person"></i> Editar Cliente</h2>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="inicio_vigencia" class="form-label"><i class="bi bi-calendar-day"></i> Início Vigência</label>
                            <input type="date" class="form-control" id="inicio_vigencia" name="inicio_vigencia" value="<?php echo htmlspecialchars($row['inicio_vigencia']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="apolice" class="form-label"><i class="bi bi-file-earmark-text"></i> Proposta</label>
                            <input type="text" class="form-control" id="apolice" name="apolice" value="<?php echo htmlspecialchars($row['apolice']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="nome" class="form-label"><i class="bi bi-person"></i> Nome</label>
                            <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($row['nome']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="cpf" class="form-label"><i class="bi bi-card-text"></i> CPF/CNPJ</label>
                            <input type="text" class="form-control" id="cpf" name="cpf" value="<?php echo htmlspecialchars($row['cpf']); ?>" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="numero" class="form-label"><i class="bi bi-telephone"></i> Número</label>
                            <input type="text" class="form-control" id="numero" name="numero" value="<?php echo htmlspecialchars($row['numero']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label"><i class="bi bi-envelope"></i> Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="premio_liquido" class="form-label"><i class="bi bi-currency-dollar"></i> Prêmio Líquido</label>
                            <input type="text" class="form-control" id="premio_liquido" name="premio_liquido" value="<?php echo htmlspecialchars($row['premio_liquido']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="comissao" class="form-label"><i class="bi bi-percent"></i> Comissão (%)</label>
                            <input type="text" class="form-control" id="comissao" name="comissao" value="<?php echo htmlspecialchars($row['comissao']); ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="observacoes" class="form-label"><i class="bi bi-sticky"></i> Observações</label>
                        <textarea class="form-control" id="observacoes" name="observacoes" rows="4"><?php echo htmlspecialchars($row['observacoes']); ?></textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="status" class="form-label"><i class="bi bi-tags"></i> Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="Aguardando Emissão" <?php if ($row['status'] == 'Aguardando Emissão') echo 'selected'; ?>>Aguardando Emissão</option>
                                <option value="Emitida" <?php if ($row['status'] == 'Emitida') echo 'selected'; ?>>Emitida</option>
                                <option value="Cancelado" <?php if ($row['status'] == 'Cancelado') echo 'selected'; ?>>Cancelado</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="seguradora" class="form-label"><i class="bi bi-building"></i> Seguradora</label>
                            <select class="form-select" id="seguradora" name="seguradora" required>
                            <option value="Aliro Seguro" <?php if ($row['seguradora'] == 'Aliro Seguro')
                            echo 'selected'; ?>>
                            Aliro Seguro</option>
                        <option value="Allianz Seguros" <?php if ($row['seguradora'] == 'Allianz Seguros')
                            echo 'selected'; ?>>Allianz Seguros</option>
                        <option value="Azul Seguros" <?php if ($row['seguradora'] == 'Azul Seguros')
                            echo 'selected'; ?>>
                            Azul Seguros</option>
                        <option value="HDI Seguros" <?php if ($row['seguradora'] == 'HDI Seguros')
                            echo 'selected'; ?>>HDI
                            Seguros</option>
                        <option value="Liberty Seguros" <?php if ($row['seguradora'] == 'Liberty Seguros')
                            echo 'selected'; ?>>Liberty Seguros</option>
                        <option value="MAPFRE" <?php if ($row['seguradora'] == 'MAPFRE')
                            echo 'selected'; ?>>MAPFRE
                        </option>
                        <option value="Porto Seguro" <?php if ($row['seguradora'] == 'Porto Seguro')
                            echo 'selected'; ?>>
                            Porto Seguro</option>
                        <option value="Sompo Auto" <?php if ($row['seguradora'] == 'Sompo Auto')
                            echo 'selected'; ?>>Sompo
                            Auto</option>
                        <option value="Tokio Marine Seguros" <?php if ($row['seguradora'] == 'Tokio Marine Seguros')
                            echo 'selected'; ?>>Tokio Marine Seguros</option>
                        <option value="Zurich Brasil Seguros" <?php if ($row['seguradora'] == 'Zurich Brasil Seguros')
                            echo 'selected'; ?>>Zurich Brasil Seguros</option>
                        <option value="Sancor Seguros" <?php if ($row['seguradora'] == 'Sancor Seguros')
                            echo 'selected'; ?>>Sancor Seguros</option>
                        <option value="Suhai" <?php if ($row['seguradora'] == 'Suhai')
                            echo 'selected'; ?>>Suhai</option>
                        <option value="Mitsui" <?php if ($row['seguradora'] == 'Mitsui')
                            echo 'selected'; ?>>Mitsui
                        </option>
                        <option value="Sura Seguros" <?php if ($row['seguradora'] == 'Sura Seguros')
                            echo 'selected'; ?>>
                            Sura Seguros</option>
                        <option value="EZZE" <?php if ($row['seguradora'] == 'EZZE')
                            echo 'selected'; ?>>EZZE</option>
                        <option value="EZZE" <?php if ($row['seguradora'] == 'EZZE')
                            echo 'selected'; ?>>EZZE</option>
                        <option value="Capemisa" <?php if ($row['seguradora'] == 'Capemisa')
                            echo 'selected'; ?>>Capemisa
                        </option>
                        <option value="AKAD" <?php if ($row['seguradora'] == 'AKAD')
                            echo 'selected'; ?>>AKAD</option>
                        <option value="AssistCard" <?php if ($row['seguradora'] == 'AssistCard')
                            echo 'selected'; ?>>
                            AssistCard</option>
                        <option value="AXA" <?php if ($row['seguradora'] == 'AXA')
                            echo 'selected'; ?>>AXA</option>
                        <option value="Ituran" <?php if ($row['seguradora'] == 'Ituran')
                            echo 'selected'; ?>>Ituran
                        </option>
                        <option value="Pottencial" <?php if ($row['seguradora'] == 'Pottencial')
                            echo 'selected'; ?>>
                            Pottencial</option>
                        <option value="SulAmerica" <?php if ($row['seguradora'] == 'SulAmerica')
                            echo 'selected'; ?>>
                            SulAmerica</option>
                        <option value="VitalCard" <?php if ($row['seguradora'] == 'VitalCard')
                            echo 'selected'; ?>>
                            VitalCard</option>
                        <option value="Bradesco" <?php if ($row['seguradora'] == 'Bradesco')
                            echo 'selected'; ?>>Bradesco
                        </option>
                        <option value="ItauSeguros" <?php if ($row['seguradora'] == 'ItauSeguros') echo 'selected'; ?>>ItauSeguros</option>
                        <option value="Unimed Seguros" <?php if ($row['seguradora'] == 'Unimed Seguros')
                            echo 'selected'; ?>>Unimed Seguros
                        </option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="tipo_seguro" class="form-label"><i class="bi bi-shield"></i> Tipo de Seguro</label>
                        <select class="form-select" id="tipo_seguro" name="tipo_seguro" required>
                            <option value="Seguro Auto" <?php if ($row['tipo_seguro'] == 'Seguro Auto') echo 'selected'; ?>>Seguro Auto</option>
                            <option value="Seguro Viagem" <?php if ($row['tipo_seguro'] == 'Seguro Viagem') echo 'selected'; ?>>Seguro Viagem</option>
                            <option value="Seguro Franquia Sura" <?php if ($row['tipo_seguro'] == 'Seguro Franquia Sura') echo 'selected'; ?>>Seguro Franquia Sura</option>
                            <option value="Seguro Moto" <?php if ($row['tipo_seguro'] == 'Seguro Moto')
                            echo 'selected'; ?>>
                            Seguro Moto</option>
                        <option value="Seguro de Vida" <?php if ($row['tipo_seguro'] == 'Seguro de Vida')
                            echo 'selected'; ?>>Seguro de Vida</option>
                        <option value="Seguro Empresarial" <?php if ($row['tipo_seguro'] == 'Seguro Empresarial')
                            echo 'selected'; ?>>Seguro Empresarial</option>
                        <option value="Consórcio" <?php if ($row['tipo_seguro'] == 'Consórcio')
                            echo 'selected'; ?>>
                            Consórcio</option>
                        <option value="Seguro Transporte" <?php if ($row['tipo_seguro'] == 'Seguro Transporte')
                            echo 'selected'; ?>>Seguro Transporte</option>
                        <option value="Seguro Saúde" <?php if ($row['tipo_seguro'] == 'Seguro Saúde')
                            echo 'selected'; ?>>
                            Seguro Saúde</option>
                        <option value="Seguro Dental" <?php if ($row['tipo_seguro'] == 'Seguro Dental')
                            echo 'selected'; ?>>Seguro Dental</option>
                        <option value="Seguro Frota" <?php if ($row['tipo_seguro'] == 'Seguro Frota')
                            echo 'selected'; ?>>
                            Seguro Frota</option>
                        <option value="Seguro Agronegócio" <?php if ($row['tipo_seguro'] == 'Seguro Agronegócio')
                            echo 'selected'; ?>>Seguro Agronegócio</option>
                        <option value="Acidenes Pessoais" <?php if ($row['tipo_seguro'] == 'Acidenes Pessoais')
                            echo 'selected'; ?>>Acidenes Pessoais</option>
                        <option value="Seguro Residencial" <?php if ($row['seguradora'] == 'Seguro Residencial')
                            echo 'selected'; ?>>Seguro Residencial</option>
                            <!-- Continue com as demais opções de seguro -->
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="pdf" class="form-label"><i class="bi bi-file-earmark-arrow-up"></i> Proposta PDF</label>
                        <input type="file" class="form-control" id="pdf" name="pdf">
                        <?php if (!empty($row['pdf_path'])): ?>
                            <small class="form-text text-muted">Arquivo atual: <?php echo htmlspecialchars($row['pdf_path']); ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Atualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap e jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/autonumeric@4.5.4/dist/autoNumeric.min.js"></script>

    <script>
        $(document).ready(function(){
            // Formatação AutoNumeric para Prêmio Líquido
            new AutoNumeric('#premio_liquido', {
                digitGroupSeparator: '.',
                decimalCharacter: ',',
                decimalPlaces: 2,
                currencySymbol: 'R$ ',
                currencySymbolPlacement: 'p',
                unformatOnSubmit: true
            });
            // Máscara para Número de Celular
            $('#numero').mask('(00) 00000-0000');
            // Máscara para Comissão
            $('#comissao').mask('##0,00%', {reverse: true});
        });
    </script>
</body>
</html>
