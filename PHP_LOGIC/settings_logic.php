<?php
/**
 * Localização: /PHP_LOGIC/settings_logic.php
 *
 * Responsável pela lógica da página de configurações:
 * - Busca todos os usuários cadastrados no sistema, incluindo suas permissões.
 */

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Dependências
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../INCLUDES/functions.php';

// Busca todos os usuários para listar na página, incluindo as novas colunas de permissão.
// Evita selecionar a senha para não trafegar esse dado sem necessidade.
$users_result = $conn->query("SELECT id, nome, email, pode_ver_bi, pode_ver_comissao_total, pode_ver_comissao_card FROM usuarios ORDER BY nome ASC");

?>