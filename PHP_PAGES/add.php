<?php
include '../db.php';
session_start(); // Inicie a sessão

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $inicio_vigencia = $_POST['inicio_vigencia'];
    $final_vigencia = $_POST['final_vigencia'];
    $apolice = $_POST['apolice'];
    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $numero = $_POST['numero'];
    $email = $_POST['email'];
    $premio_liquido = $_POST['premio_liquido'];
    $comissao = $_POST['comissao'];
    $status = $_POST['status'];
    $seguradora = $_POST['seguradora'];
    $tipo_seguro = $_POST['tipo_seguro'];
    $observacoes = $_POST['observacoes'];
    

    // Registrar a notificação
    $usuario_id = $_SESSION['user_id'];
    date_default_timezone_set('America/Sao_Paulo');
    // Obtenha o nome do usuário
    $stmt = $conn->prepare("SELECT nome FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $user_result = $stmt->get_result();
    $usuario_nome = $user_result->fetch_assoc()['nome'];

    $mensagem = "Usuário $usuario_nome adicionou proposta de $nome - " . date('Y-m-d H:i:s');
    $stmt = $conn->prepare("INSERT INTO notificacoes (usuario_id, mensagem, data_hora) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $usuario_id, $mensagem, $mensagem);
    $stmt->execute();

    $pdf_path = NULL;
    if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] == UPLOAD_ERR_OK) {
        $pdf_name = $_FILES['pdf']['name'];
        $pdf_tmp = $_FILES['pdf']['tmp_name'];
        $pdf_path = '../uploads/' . $pdf_name;
        move_uploaded_file($pdf_tmp, $pdf_path);
    }

    $stmt = $conn->prepare("INSERT INTO clientes (inicio_vigencia, final_vigencia, apolice, nome, cpf, numero, email, premio_liquido, comissao, status, seguradora, tipo_seguro, pdf_path, observacoes) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param('ssssssssssssss', $inicio_vigencia, $final_vigencia, $apolice, $nome, $cpf, $numero, $email, $premio_liquido, $comissao, $status, $seguradora, $tipo_seguro, $pdf_path, $observacoes);


    if ($stmt->execute()) {
        header('Location: ../index.php');
    } else {
        echo "Erro: " . $stmt->error;
    }

    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/add.css">
</head>

<body>
    <div class="container mt-5">
        <div class="card shadow-lg">
            <div class="card-header text-center bg-primary text-white">
                <h2><i class="bi bi-person-plus"></i> Adicionar Cliente</h2>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="inicio_vigencia" class="form-label"><i class="bi bi-calendar-day"></i> Início Vigência</label>
                            <input type="date" class="form-control" id="inicio_vigencia" name="inicio_vigencia" required>
                        </div>
                        <div class="col-md-6">
    <label for="final_vigencia" class="form-label"><i class="bi bi-calendar-check"></i> Final Vigência</label>
    <input type="date" class="form-control" id="final_vigencia" name="final_vigencia" required>
</div>
                        <div class="col-md-6">
                            <label for="apolice" class="form-label"><i class="bi bi-file-earmark-text"></i> Proposta</label>
                            <input type="text" class="form-control" id="apolice" name="apolice" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="nome" class="form-label"><i class="bi bi-person"></i> Nome</label>
                            <input type="text" class="form-control" id="nome" name="nome" required>
                        </div>
                        <div class="col-md-6">
                            <label for="cpf" class="form-label"><i class="bi bi-card-text"></i> CPF/CNPJ</label>
                            <input type="text" class="form-control" id="cpf" name="cpf" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="numero" class="form-label"><i class="bi bi-telephone"></i> Celular</label>
                            <input type="text" class="form-control" id="numero" name="numero" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label"><i class="bi bi-envelope"></i> Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="premio_liquido" class="form-label"><i class="bi bi-currency-dollar"></i> Prêmio Líquido</label>
                            <input type="text" class="form-control" id="premio_liquido" name="premio_liquido" required>
                        </div>
                        <div class="col-md-6">
                            <label for="comissao" class="form-label"><i class="bi bi-percent"></i> Comissão (%)</label>
                            <input type="text" class="form-control" id="comissao" name="comissao" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="status" class="form-label"><i class="bi bi-tags"></i> Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="Aguardando Emissão">Aguardando Emissão</option>
                                <option value="Emitida">Emitida</option>
                                <option value="Emitida">Cancelado</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="seguradora" class="form-label"><i class="bi bi-building"></i> Seguradora</label>
                            <select class="form-select" id="seguradora" name="seguradora">
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
                                <option value="Bradesco Agência">Bradesco Agência</option>
                                <option value="ItauSeguros">ItauSeguros</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="tipo_seguro" class="form-label"><i class="bi bi-shield"></i> Tipo de Seguro</label>
                        <select class="form-select" id="tipo_seguro" name="tipo_seguro">
                            <option value="Seguro Auto">Seguro Auto</option>
                            <option value="Seguro Residencial">Seguro Residencial</option>
                            <option value="Acidentes Pessoais">Acidentes Pessoais</option>
                            <option value="Seguro Moto">Seguro Moto</option>
                            <option value="Seguro de Vida">Seguro de Vida</option>
                            <option value="Seguro Empresarial">Seguro Empresarial</option>
                            <option value="Consórcio">Consórcio</option>
                            <option value="Seguro Transporte">Seguro Transporte</option>
                            <option value="Seguro Saúde">Seguro Saúde</option>
                            <option value="Seguro Dental">Seguro Dental</option>
                            <option value="Seguro Frota">Seguro Frota</option>
                            <option value="Seguro Agronegócio">Seguro Agronegócio</option>
                            <option value="Seguro Viagem">Seguro Viagem</option>
                            <option value="Seguro Sura">Seguro Franquia Sura</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label for="pdf" class="form-label"><i class="bi bi-file-earmark-arrow-up"></i> Proposta PDF</label>
                        <input type="file" class="form-control" id="pdf" name="pdf" required>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Adicionar Cliente</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/autonumeric@4.5.4/dist/autoNumeric.min.js"></script>
    <script src="../JS/verificar_proposta.js"></script>
    <script>
    $(document).ready(function() {
        // Inicializar autoNumeric no campo de Prêmio Líquido
        new AutoNumeric('#premio_liquido', {
            digitGroupSeparator: '.',
            decimalCharacter: ',',
            decimalPlaces: 2,
            currencySymbol: 'R$ ',
            currencySymbolPlacement: 'p',
            unformatOnSubmit: true
        });

        // Máscaras para celular e comissão
        $('#numero').mask('(00) 00000-0000');
        $('#comissao').mask('##0,00%', {reverse: true});
    });
</script>

</body>
</html>