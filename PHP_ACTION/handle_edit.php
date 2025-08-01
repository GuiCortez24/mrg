<?php
/**
 * Localização: /PHP_ACTION/handle_edit.php
 * Processa a atualização de um cliente, com lógica corrigida para preservar anexos e salvar todos os campos.
 */

session_start();
include '../db.php';

// Validação de segurança básica
if (!isset($_SESSION['user_id'])) {
    die("Acesso negado.");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit();
}

// 1. Coleta de todos os dados do formulário
$id = $_POST['id'];
$inicio_vigencia = $_POST['inicio_vigencia'];
$final_vigencia = !empty($_POST['final_vigencia']) ? $_POST['final_vigencia'] : NULL;
$apolice = $_POST['apolice'];
$nome = $_POST['nome'];
$cpf = preg_replace('/[^0-9]/', '', $_POST['cpf']);
$numero = $_POST['numero'];
$email = $_POST['email'];
$status = $_POST['status'];
$seguradora = $_POST['seguradora'];
$tipo_seguro = $_POST['tipo_seguro'];
$item_segurado = $_POST['item_segurado'];
$item_identificacao = !empty($_POST['item_identificacao']) ? $_POST['item_identificacao'] : NULL;
$usuario_id = $_SESSION['user_id'];

// Lógica para zerar valores se o status for "Cancelado"
if ($status === 'Cancelado') {
    $premio_liquido = '0.00';
    $comissao = '0.00';
} else {
    $premio_liquido = $_POST['premio_liquido'];
    $comissao = $_POST['comissao'];
}

// 2. Lógica de Múltiplos PDFs Corrigida para Compatibilidade
// Primeiro, busca a lista atual de PDFs do banco
$stmt_current_pdfs = $conn->prepare("SELECT pdf_path, tipo_operacao FROM clientes WHERE id = ?");
$stmt_current_pdfs->bind_param("i", $id);
$stmt_current_pdfs->execute();
$cliente_data = $stmt_current_pdfs->get_result()->fetch_assoc();
$stmt_current_pdfs->close();

$current_pdfs_data = $cliente_data['pdf_path'];
$tipo_operacao = $cliente_data['tipo_operacao']; // Mantém o tipo de operação original

$current_pdfs_array = [];
$decoded_pdfs = json_decode($current_pdfs_data, true);

if (is_array($decoded_pdfs)) {
    $current_pdfs_array = $decoded_pdfs;
} elseif (!empty($current_pdfs_data)) {
    $current_pdfs_array[] = $current_pdfs_data;
}

// Depois, processa os novos arquivos enviados, se houver
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
                $current_pdfs_array[] = $pdf_path_db;
            }
        }
    }
}
$new_pdfs_json = json_encode(array_values(array_unique($current_pdfs_array)));

// 3. Lógica de Notificação
$stmt_user = $conn->prepare("SELECT nome FROM usuarios WHERE id = ?");
$stmt_user->bind_param("i", $usuario_id);
$stmt_user->execute();
$usuario_nome = $stmt_user->get_result()->fetch_assoc()['nome'];
$stmt_user->close();
date_default_timezone_set('America/Sao_Paulo');
$data_hora = date('Y-m-d H:i:s');
$mensagem = "O usuário $usuario_nome atualizou a proposta #$apolice de $nome.";
$stmt_notif = $conn->prepare("INSERT INTO notificacoes (usuario_id, mensagem, data_hora) VALUES (?, ?, ?)");
$stmt_notif->bind_param("iss", $usuario_id, $mensagem, $data_hora);
$stmt_notif->execute();
$stmt_notif->close();

// 4. SQL para ATUALIZAR o cliente com todos os campos
// ================================================================
// CORREÇÃO: A query e o bind_param agora estão sincronizados
// ================================================================
$sql = "UPDATE clientes SET 
            inicio_vigencia = ?, final_vigencia = ?, apolice = ?, nome = ?, 
            cpf = ?, numero = ?, email = ?, premio_liquido = ?, comissao = ?, 
            status = ?, item_segurado = ?, seguradora = ?, 
            tipo_seguro = ?, item_identificacao = ?, pdf_path = ?
        WHERE id = ?";

$stmt = $conn->prepare($sql);
// A string de tipos precisa ter 15 parâmetros + 'i' para o ID
$stmt->bind_param(
    'sssssssddssssssi',
    $inicio_vigencia, $final_vigencia, $apolice, $nome, $cpf, $numero, $email, 
    $premio_liquido, $comissao, $status, $item_segurado, $seguradora, 
    $tipo_seguro, $item_identificacao, $new_pdfs_json, $id
);

if ($stmt->execute()) {
    header('Location: ../index.php?update=success');
} else {
    header('Location: ../PHP_PAGES/edit.php?id=' . $id . '&status=error&msg=' . urlencode($stmt->error));
}

$stmt->close();
$conn->close();
exit();
?>