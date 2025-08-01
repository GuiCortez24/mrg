<?php
/**
 * Localização: /PHP_ACTION/handle_add.php
 * Lógica de adicionar cliente com todas as funcionalidades e correção no bind_param.
 */

session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    die("Acesso negado.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // 1. Coleta de todos os dados do formulário
    $inicio_vigencia = $_POST['inicio_vigencia'];
    $final_vigencia = !empty($_POST['final_vigencia']) ? $_POST['final_vigencia'] : NULL;
    $apolice = $_POST['apolice'];
    $nome = $_POST['nome'];
    $cpf = preg_replace('/[^0-9]/', '', $_POST['cpf']);
    $numero = $_POST['numero'];
    $email = $_POST['email'];
    $premio_liquido = $_POST['premio_liquido'];
    $comissao = $_POST['comissao'];
    $status = $_POST['status'];
    $seguradora = $_POST['seguradora'];
    $tipo_seguro = $_POST['tipo_seguro'];
    $item_segurado = $_POST['item_segurado'];
    $item_identificacao = !empty($_POST['item_identificacao']) ? $_POST['item_identificacao'] : NULL;
    $usuario_id = $_SESSION['user_id'];

    // 2. Lógica para múltiplos uploads de PDF
    $uploaded_files = [];
    $pdf_path_json = null;

    if (isset($_FILES['pdfs']['name']) && !empty($_FILES['pdfs']['name'][0])) {
        $upload_dir = '../uploads/';
        if (!is_dir($upload_dir)) { 
            mkdir($upload_dir, 0755, true); 
        }
        foreach ($_FILES['pdfs']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['pdfs']['error'][$key] == UPLOAD_ERR_OK) {
                $pdf_name = uniqid() . '-' . basename($_FILES['pdfs']['name'][$key]);
                $pdf_path_db = 'uploads/' . $pdf_name;
                $pdf_path_server = '../' . $pdf_path_db;
                if (move_uploaded_file($tmp_name, $pdf_path_server)) {
                    $uploaded_files[] = $pdf_path_db;
                }
            }
        }
    }
    
    if (!empty($uploaded_files)) {
        $pdf_path_json = json_encode($uploaded_files);
    }

    // 3. Lógica de verificação de Renovação vs. Seguro Novo
    $tipo_operacao = 'NOVO';
    $check_sql = "SELECT COUNT(id) as count FROM clientes WHERE cpf = ?";
    $check_params = [$cpf];
    $check_types = "s";
    if (!empty($item_identificacao)) {
        $check_sql .= " OR item_identificacao = ?";
        $check_params[] = $item_identificacao;
        $check_types .= "s";
    }
    $stmt_check = $conn->prepare($check_sql);
    $stmt_check->bind_param($check_types, ...$check_params);
    $stmt_check->execute();
    $check_result = $stmt_check->get_result()->fetch_assoc();
    $stmt_check->close();
    if ($check_result['count'] > 0) {
        $tipo_operacao = 'RENOVAÇÃO';
    }

    // 4. Inserção no banco de dados com todos os campos
    $stmt = $conn->prepare(
        "INSERT INTO clientes (inicio_vigencia, final_vigencia, apolice, nome, cpf, numero, email, pdf_path, premio_liquido, comissao, status, tipo_operacao, item_segurado, seguradora, tipo_seguro, item_identificacao) 
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    
    // ================================================================
    // CORREÇÃO: A string de tipos agora tem 16 caracteres, correspondendo
    // exatamente aos 16 campos que estamos inserindo.
    // ================================================================
    $stmt->bind_param(
        'ssssssssddssssss', 
        $inicio_vigencia, $final_vigencia, $apolice, $nome, $cpf, $numero, $email, 
        $pdf_path_json, $premio_liquido, $comissao, $status, $tipo_operacao, $item_segurado,
        $seguradora, $tipo_seguro, $item_identificacao
    );

    // 5. Tratamento do resultado
    if ($stmt->execute()) {
        // Lógica de notificação
        $stmt_user = $conn->prepare("SELECT nome FROM usuarios WHERE id = ?");
        $stmt_user->bind_param("i", $usuario_id);
        $stmt_user->execute();
        $usuario_nome = $stmt_user->get_result()->fetch_assoc()['nome'];
        
        date_default_timezone_set('America/Sao_Paulo');
        $data_hora = date('Y-m-d H:i:s');
        $mensagem = "Usuário $usuario_nome adicionou ($tipo_operacao) proposta de $nome.";
        
        $stmt_notif = $conn->prepare("INSERT INTO notificacoes (usuario_id, mensagem, data_hora) VALUES (?, ?, ?)");
        $stmt_notif->bind_param("iss", $usuario_id, $mensagem, $data_hora);
        $stmt_notif->execute();
        $stmt_notif->close();

        // Redireciona para o painel principal em caso de sucesso
        header('Location: ../PHP_PAGES/dashboard.php?status=add_success');

    } else {
        // Em caso de erro, volta para o formulário com a mensagem de erro específica
        header('Location: ../PHP_PAGES/add.php?status=error&msg=' . urlencode($stmt->error));
    }

    $stmt->close();
    $conn->close();
}

exit();
?>