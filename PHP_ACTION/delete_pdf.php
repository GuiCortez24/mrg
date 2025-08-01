<?php
/**
 * Localização: /PHP_ACTION/delete_pdf.php
 * Exclui um anexo PDF específico de um cliente.
 */

session_start();
include '../db.php';

// 1. Segurança: Garante que apenas usuários logados possam executar esta ação
if (!isset($_SESSION['user_id'])) {
    die("Acesso negado. Por favor, faça login.");
}

// 2. Validação: Garante que os parâmetros necessários foram enviados
if (isset($_GET['cliente_id']) && isset($_GET['pdf_path'])) {
    $cliente_id = $_GET['cliente_id'];
    $pdf_to_delete = $_GET['pdf_path'];

    // 3. Busca a lista atual de PDFs do cliente no banco
    $stmt = $conn->prepare("SELECT pdf_path FROM clientes WHERE id = ?");
    $stmt->bind_param("i", $cliente_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $cliente = $result->fetch_assoc();
    $stmt->close();

    if ($cliente) {
        $pdfs_json = $cliente['pdf_path'];
        $pdfs_array = json_decode($pdfs_json, true) ?: [];

        // 4. Remove o PDF da lista (array)
        if (($key = array_search($pdf_to_delete, $pdfs_array)) !== false) {
            unset($pdfs_array[$key]);
        }

        // 5. Exclui o arquivo físico do servidor
        $full_path_to_file = '../' . $pdf_to_delete;
        if (file_exists($full_path_to_file)) {
            unlink($full_path_to_file);
        }

        // 6. Salva a nova lista (JSON) de volta no banco
        $new_pdfs_json = json_encode(array_values($pdfs_array)); // Reindexa o array
        $stmt_update = $conn->prepare("UPDATE clientes SET pdf_path = ? WHERE id = ?");
        $stmt_update->bind_param("si", $new_pdfs_json, $cliente_id);
        $stmt_update->execute();
        $stmt_update->close();
    }

    // 7. Redireciona de volta para a página de edição com uma mensagem de sucesso
    header('Location: ../PHP_PAGES/edit.php?id=' . $cliente_id . '&status=pdf_deleted');
    exit();
}