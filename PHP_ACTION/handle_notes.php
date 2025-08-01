<?php
/**
 * Localização: /PHP_ACTION/handle_notes.php
 * Salva as anotações de um cliente específico no banco de dados.
 */

session_start();
include '../db.php';

// Segurança: Garante que o usuário está logado e que os dados foram enviados
if (!isset($_SESSION['user_id'])) {
    die("Acesso negado.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cliente_id'])) {
    
    $cliente_id = $_POST['cliente_id'];
    $anotacoes = $_POST['anotacoes'];

    // Prepara e executa a atualização de forma segura
    $stmt = $conn->prepare("UPDATE clientes SET anotacoes = ? WHERE id = ?");
    $stmt->bind_param("si", $anotacoes, $cliente_id);
    
    if ($stmt->execute()) {
        // Redireciona para o painel com uma mensagem de sucesso
        header('Location: ../PHP_PAGES/dashboard.php?status=notes_success');
    } else {
        // Redireciona com uma mensagem de erro
        header('Location: ../PHP_PAGES/dashboard.php?status=notes_error');
    }

    $stmt->close();
    $conn->close();
    exit();
}