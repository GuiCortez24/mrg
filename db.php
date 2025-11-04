<?php
// Permite configuração via variáveis de ambiente
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$db   = getenv('DB_NAME') ?: 'mrg';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// ================================================================
// ADICIONE ESTA LINHA ABAIXO
// ================================================================
$conn->set_charset("utf8mb4");

?>