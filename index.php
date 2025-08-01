<?php
// index.php (na raiz)

session_start();

// Se o usuário já estiver logado, redireciona para o painel.
// Caso contrário, redireciona para a página de login.
if (isset($_SESSION['user_id'])) {
    header('Location: PHP_PAGES/dashboard.php');
} else {
    header('Location: login.php');
}
exit();