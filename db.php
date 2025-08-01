<?php
$host = 'localhost'; // ou o seu host
$user = 'root';      // ou o seu usuário
$pass = '';          // ou a sua senha
$db = 'mrg';  // o nome do seu banco

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// ================================================================
// ADICIONE ESTA LINHA ABAIXO
// ================================================================
$conn->set_charset("utf8mb4");

?>