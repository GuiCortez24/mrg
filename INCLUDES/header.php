<?php // INCLUDES/header.php

// CORREÇÃO APLICADA AQUI:
// Inicia a sessão somente se nenhuma já estiver ativa.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$page_title = isset($page_title) ? $page_title : "Sistema de Seguros";
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        /* Define o tema verde principal do site */
        :root {
            --bs-primary: #198754;
            --bs-primary-rgb: 25, 135, 84;
        }
        .btn-primary { background-color: var(--bs-primary); border-color: var(--bs-primary); }
        .btn-primary:hover { background-color: #157347; border-color: #146c43; }
        .btn-outline-primary { color: var(--bs-primary); border-color: var(--bs-primary); }
        .btn-outline-primary:hover { color: #fff; background-color: var(--bs-primary); border-color: var(--bs-primary); }
        .text-primary { color: var(--bs-primary) !important; }
        .bg-primary { background-color: var(--bs-primary) !important; }
        .border-primary { border-color: var(--bs-primary) !important; }
        .pagination .page-link { color: var(--bs-primary); }
        .pagination .page-item.active .page-link { background-color: var(--bs-primary); border-color: var(--bs-primary); color: #fff; }

        /* Classe customizada para o azul original do Bootstrap */
        .custom-border-blue { border-color: #0d6efd !important; }
        
        /* ================================================================ */
        /* NOVO: Classe customizada para o TEXTO azul                     */
        /* ================================================================ */
        .custom-text-blue { color: #0d6efd !important; }

        /* Animação para os cards ao passar o mouse */
        .card.shadow-sm {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card.shadow-sm:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15) !important;
        }
    </style>
</head>
<body>