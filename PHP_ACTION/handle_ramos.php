<?php
/**
 * Localização: /PHP_ACTION/handle_ramos.php
 * Processa todas as ações CRUD para os Ramos de Seguro.
 */

require_once __DIR__ . '/../db.php';

// Determina a ação com base no método da requisição
$action = $_POST['action'] ?? $_GET['action'] ?? null;

switch ($action) {
    case 'add':
    case 'update':
        $nome = trim($_POST['nome']);
        $id = isset($_POST['id']) ? intval($_POST['id']) : null;

        if (empty($nome)) {
            header("Location: ../PHP_PAGES/ramos_seguro.php?status=error&msg=empty");
            exit();
        }

        if ($action === 'update' && $id) {
            $stmt = $conn->prepare("UPDATE ramos_seguro SET nome = ? WHERE id = ?");
            $stmt->bind_param("si", $nome, $id);
            $status = $stmt->execute() ? 'update_success' : 'update_error';
        } else {
            $stmt = $conn->prepare("INSERT INTO ramos_seguro (nome) VALUES (?)");
            $stmt->bind_param("s", $nome);
            if (!$stmt->execute() && $conn->errno == 1062) {
                header("Location: ../PHP_PAGES/ramos_seguro.php?status=add_error&msg=duplicate");
                exit();
            }
            $status = 'add_success';
        }
        header("Location: ../PHP_PAGES/ramos_seguro.php?status=$status");
        break;

    case 'delete':
        $id = intval($_GET['id']);
        $stmt = $conn->prepare("DELETE FROM ramos_seguro WHERE id = ?");
        $stmt->bind_param("i", $id);
        $status = $stmt->execute() ? 'delete_success' : 'delete_error';
        header("Location: ../PHP_PAGES/ramos_seguro.php?status=$status");
        break;

    default:
        // Ação desconhecida, redireciona para a página principal
        header("Location: ../PHP_PAGES/ramos_seguro.php");
        break;
}

exit();
?>