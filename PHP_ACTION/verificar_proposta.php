<?php
/**
 * Localização: /PHP_ACTION/verificar_proposta.php
 * Verifica no banco se um número de apólice/proposta já existe.
 */

// Define o cabeçalho da resposta como JSON
header('Content-Type: application/json');
session_start();
include '../db.php';

// Garante que apenas usuários logados possam usar este endpoint
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['exists' => false, 'error' => 'Acesso não autorizado']);
    exit();
}

// Garante que o número da apólice foi enviado
if (!isset($_POST['apolice']) || empty(trim($_POST['apolice']))) {
    echo json_encode(['exists' => false]);
    exit();
}

$apolice = $_POST['apolice'];

$stmt = $conn->prepare("SELECT COUNT(*) AS count FROM clientes WHERE apolice = ?");
$stmt->bind_param('s', $apolice);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

// Retorna true se a contagem for maior que 0, senão false
echo json_encode(['exists' => $row['count'] > 0]);
?>