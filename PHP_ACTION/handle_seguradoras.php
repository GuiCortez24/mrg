<?php
/**
 * Localização: /PHP_ACTION/handle_seguradoras.php
 * Gerencia todas as ações de CRUD para as seguradoras.
 * Versão ajustada para ser compatível com a nova estrutura de componentes.
 */

session_start();
include '../db.php';

// Proteção básica de acesso
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    die("Acesso negado.");
}

// Verifica se a requisição é do tipo POST (para Adicionar e Editar)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    // Ação: Adicionar nova seguradora
    if ($_POST['action'] == 'add') {
        $nome = $_POST['nome'];
        $usuario = $_POST['usuario'];
        $senha = $_POST['senha']; // ATENÇÃO: Risco de segurança! Veja a nota no final.
        $numero_0800 = $_POST['numero_0800'];

        $stmt = $conn->prepare("INSERT INTO seguradoras (nome, usuario, senha, numero_0800) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nome, $usuario, $senha, $numero_0800);
    }

    // Ação: Editar uma seguradora existente
    elseif ($_POST['action'] == 'update') {
        $id = $_POST['id'];
        $nome = $_POST['nome'];
        $usuario = $_POST['usuario'];
        $senha = $_POST['senha'];
        $numero_0800 = $_POST['numero_0800'];

        // Lógica inteligente: só atualiza a senha se uma nova for fornecida.
        if (!empty($senha)) {
            $stmt = $conn->prepare("UPDATE seguradoras SET nome = ?, usuario = ?, senha = ?, numero_0800 = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $nome, $usuario, $senha, $numero_0800, $id);
        } else {
            $stmt = $conn->prepare("UPDATE seguradoras SET nome = ?, usuario = ?, numero_0800 = ? WHERE id = ?");
            $stmt->bind_param("sssi", $nome, $usuario, $numero_0800, $id);
        }
    }

    // Executa a query e redireciona com o status apropriado
    if (isset($stmt) && $stmt->execute()) {
        header('Location: ../PHP_PAGES/info_loja.php?status=success');
    } else {
        header('Location: ../PHP_PAGES/info_loja.php?status=error');
    }
    exit();
}

// Verifica se a requisição é do tipo GET (para Excluir)
elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];

    $stmt = $conn->prepare("DELETE FROM seguradoras WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header('Location: ../PHP_PAGES/info_loja.php?status=success');
    } else {
        header('Location: ../PHP_PAGES/info_loja.php?status=error');
    }
    exit();
}

// Se nenhuma ação válida for encontrada, redireciona por segurança
header('Location: ../PHP_PAGES/info_loja.php');
exit();

?>