<?php
// INCLUDES/functions.php

function formatDate($date)
{
    if (empty($date) || $date === '0000-00-00') {
        return '';
    }
    $timestamp = strtotime($date);
    if ($timestamp === false) {
        return '';
    }
    return date('d/m/Y', $timestamp);
}

/**
 * Recarrega as permissões do usuário na sessão
 * @param mysqli $conn Conexão com o banco de dados
 * @param int $user_id ID do usuário
 * @return bool True se as permissões foram carregadas com sucesso
 */
function reloadUserPermissions($conn, $user_id)
{
    $stmt = $conn->prepare("SELECT pode_ver_bi, pode_ver_comissao_total, pode_ver_comissao_card FROM usuarios WHERE id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['pode_ver_bi'] = (bool)$user['pode_ver_bi'];
        $_SESSION['pode_ver_comissao_total'] = (bool)$user['pode_ver_comissao_total'];
        $_SESSION['pode_ver_comissao_card'] = (bool)$user['pode_ver_comissao_card'];
        $stmt->close();
        return true;
    }
    
    $stmt->close();
    return false;
}

/**
 * Verifica se o usuário tem uma permissão específica
 * @param string $permission Nome da permissão (ex: 'pode_ver_bi')
 * @return bool True se o usuário tem a permissão
 */
function hasPermission($permission)
{
    return isset($_SESSION[$permission]) && $_SESSION[$permission] === true;
}

// Outras funções úteis podem ser adicionadas aqui no futuro.