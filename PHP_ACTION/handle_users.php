<?php
/**
 * Localização: /PHP_ACTION/handle_users.php
 * Processa todas as requisições CRUD para a entidade de usuários,
 * incluindo o gerenciamento de suas permissões.
 */

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../db.php';

$action = $_REQUEST['action'] ?? null;

switch ($action) {
    case 'add':
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $senha = $_POST['senha'];

        // --- NOVO: Captura os valores das permissões ---
        $pode_ver_bi = isset($_POST['pode_ver_bi']) ? 1 : 0;
        $pode_ver_comissao_total = isset($_POST['pode_ver_comissao_total']) ? 1 : 0;
        $pode_ver_comissao_card = isset($_POST['pode_ver_comissao_card']) ? 1 : 0;

        if (empty($nome) || empty($email) || empty($senha)) {
            header('Location: ../PHP_PAGES/settings.php');
            exit;
        }

        $hashed_password = password_hash($senha, PASSWORD_DEFAULT);

        // --- AJUSTADO: Query de inserção com as novas colunas ---
        $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, pode_ver_bi, pode_ver_comissao_total, pode_ver_comissao_card) VALUES (?, ?, ?, ?, ?, ?)");
        // --- AJUSTADO: bind_param com os novos tipos (i para integer) ---
        $stmt->bind_param("sssiii", $nome, $email, $hashed_password, $pode_ver_bi, $pode_ver_comissao_total, $pode_ver_comissao_card);
        $stmt->execute();
        $stmt->close();
        break;

    case 'edit':
        $id = $_POST['user_id'];
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $senha = $_POST['senha'];

        // --- NOVO: Captura os valores das permissões ---
        $pode_ver_bi = isset($_POST['pode_ver_bi']) ? 1 : 0;
        $pode_ver_comissao_total = isset($_POST['pode_ver_comissao_total']) ? 1 : 0;
        $pode_ver_comissao_card = isset($_POST['pode_ver_comissao_card']) ? 1 : 0;

        if (empty($nome) || empty($email) || empty($id)) {
            header('Location: ../PHP_PAGES/settings.php');
            exit;
        }

        if (!empty($senha)) {
            // Atualiza a senha e as permissões se uma nova senha for fornecida
            $hashed_password = password_hash($senha, PASSWORD_DEFAULT);
            // --- AJUSTADO: Query de update com as novas colunas ---
            $stmt = $conn->prepare("UPDATE usuarios SET nome = ?, email = ?, senha = ?, pode_ver_bi = ?, pode_ver_comissao_total = ?, pode_ver_comissao_card = ? WHERE id = ?");
            // --- AJUSTADO: bind_param com os novos tipos ---
            $stmt->bind_param("sssiiii", $nome, $email, $hashed_password, $pode_ver_bi, $pode_ver_comissao_total, $pode_ver_comissao_card, $id);
        } else {
            // Não atualiza a senha, mas atualiza as permissões
            // --- AJUSTADO: Query de update com as novas colunas ---
            $stmt = $conn->prepare("UPDATE usuarios SET nome = ?, email = ?, pode_ver_bi = ?, pode_ver_comissao_total = ?, pode_ver_comissao_card = ? WHERE id = ?");
            // --- AJUSTADO: bind_param com os novos tipos ---
            $stmt->bind_param("ssiiii", $nome, $email, $pode_ver_bi, $pode_ver_comissao_total, $pode_ver_comissao_card, $id);
        }
        $stmt->execute();
        $stmt->close();
        
        // Recarrega as permissões do usuário atual se ele estiver editando a si mesmo
        if ($id == $_SESSION['user_id']) {
            require_once __DIR__ . '/../INCLUDES/functions.php';
            reloadUserPermissions($conn, $id);
        }
        break;

    case 'delete':
        $id = $_GET['id'];
        if (empty($id)) {
            header('Location: ../PHP_PAGES/settings.php');
            exit;
        }
        $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        break;
}

// Redireciona de volta para a página de configurações após a ação
header('Location: ../PHP_PAGES/settings.php');
exit;
?>