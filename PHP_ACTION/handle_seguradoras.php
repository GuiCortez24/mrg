<?php
/**
 * Localização: /PHP_ACTION/handle_seguradoras.php
 * Gerencia todas as ações de CRUD (Criar, Ler, Atualizar, Deletar) para as seguradoras.
 */

session_start();
include '../db.php';

// Proteção básica
if (!isset($_SESSION['user_id'])) {
    die("Acesso negado.");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../PHP_PAGES/info_loja.php');
    exit();
}

// Ação: Adicionar nova seguradora
if (isset($_POST['add_seguradora'])) {
    $nome = $_POST['nome'];
    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];
    $numero_0800 = $_POST['numero_0800'];

    $stmt = $conn->prepare("INSERT INTO seguradoras (nome, usuario, senha, numero_0800) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nome, $usuario, $senha, $numero_0800);
    $stmt->execute();
    $stmt->close();
}

// Ação: Editar uma seguradora existente
if (isset($_POST['edit_seguradora'])) {
    $id = $_POST['id'];
    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];
    $numero_0800 = $_POST['numero_0800'];

    // Lógica inteligente: só atualiza a senha se uma nova for fornecida.
    if (!empty($senha)) {
        // Se uma nova senha foi digitada, atualiza todos os campos.
        $stmt = $conn->prepare("UPDATE seguradoras SET usuario = ?, senha = ?, numero_0800 = ? WHERE id = ?");
        $stmt->bind_param("sssi", $usuario, $senha, $numero_0800, $id);
    } else {
        // Se o campo senha estiver vazio, mantém a senha atual.
        $stmt = $conn->prepare("UPDATE seguradoras SET usuario = ?, numero_0800 = ? WHERE id = ?");
        $stmt->bind_param("ssi", $usuario, $numero_0800, $id);
    }
    $stmt->execute();
    $stmt->close();
}

// Ação: Excluir uma seguradora
if (isset($_POST['delete_seguradora'])) {
    $id = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM seguradoras WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Redireciona de volta para a página de seguradoras com uma mensagem de sucesso
header('Location: ../PHP_PAGES/info_loja.php?status=success');
exit();