<?php
/**
 * Localização: /PHP_LOGIC/ramos_logic.php
 * Lógica de preparação de dados para a página de Gerenciamento de Ramos.
 */

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../INCLUDES/functions.php';

// Variáveis de controle
$ramo_to_edit = null;
$edit_mode = false;
$alert_message = '';
$alert_class = '';

// Verificar se estamos em modo de edição
if (isset($_GET['edit_id'])) {
    $edit_mode = true;
    $id = intval($_GET['edit_id']);
    $result = $conn->query("SELECT * FROM ramos_seguro WHERE id = $id");
    if ($result->num_rows > 0) {
        $ramo_to_edit = $result->fetch_assoc();
    }
}

// Buscar todos os ramos para listar na tabela
$ramos_result = $conn->query("SELECT * FROM ramos_seguro ORDER BY nome ASC");

// Processar mensagens de status para exibir alertas
if (isset($_GET['status'])) {
    $status_messages = [
        'add_success' => ['class' => 'alert-success', 'msg' => '<strong>Sucesso!</strong> Novo ramo adicionado.'],
        'update_success' => ['class' => 'alert-success', 'msg' => '<strong>Sucesso!</strong> Ramo atualizado.'],
        'delete_success' => ['class' => 'alert-success', 'msg' => '<strong>Sucesso!</strong> Ramo excluído.'],
        'add_error_duplicate' => ['class' => 'alert-danger', 'msg' => '<strong>Erro!</strong> Este ramo de seguro já existe.'],
        'error' => ['class' => 'alert-danger', 'msg' => '<strong>Erro!</strong> Ocorreu um problema ao processar a solicitação.']
    ];
    
    $status_key = $_GET['status'];
    if ($status_key == 'add_error' && isset($_GET['msg']) && $_GET['msg'] == 'duplicate') {
        $status_key = 'add_error_duplicate';
    } elseif (str_contains($status_key, 'error')) {
        $status_key = 'error';
    }

    if (array_key_exists($status_key, $status_messages)) {
        $alert_message = $status_messages[$status_key]['msg'];
        $alert_class = $status_messages[$status_key]['class'];
    }
}
?>