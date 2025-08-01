<?php
/**
 * Localização: /PHP_ACTION/delete_notification.php
 *
 * Gerencia a exclusão de notificações.
 * Pode excluir uma notificação individual ou todas as do usuário.
 */

session_start();
include '../db.php';

// Protege o script contra acesso não autorizado
if (!isset($_SESSION['user_id'])) {
    die("Acesso negado.");
}
$usuario_id = $_SESSION['user_id'];

// Verifica se o formulário foi enviado via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // ================================================================
    // NOVO: Lógica para DELETAR TODAS as notificações do usuário
    // ================================================================
    if (isset($_POST['delete_all'])) {
        // Exclui todas as notificações pertencentes AO USUÁRIO LOGADO
        $stmt = $conn->prepare("DELETE FROM notificacoes WHERE usuario_id = ?");
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $stmt->close();
    }
    
    // Lógica para DELETAR UMA notificação específica
    if (isset($_POST['delete_one']) && isset($_POST['notificacao_id'])) {
        $notificacao_id = $_POST['notificacao_id'];

        // Exclui a notificação específica, garantindo que ela pertence ao usuário logado
        // (Boa prática de segurança para evitar que um usuário delete a notificação de outro)
        $stmt = $conn->prepare("DELETE FROM notificacoes WHERE id = ? AND usuario_id = ?");
        $stmt->bind_param("ii", $notificacao_id, $usuario_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Redireciona de volta para o painel principal em qualquer caso
header('Location: ../PHP_PAGES/dashboard.php');
exit();