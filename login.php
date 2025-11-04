<?php
// Garante que a sessão seja iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Se o usuário já estiver logado, redireciona para o painel principal
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

include 'db.php';
$error = ''; // Inicializa a variável de erro

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // AJUSTE 1: Adicionado 'email' e permissões na consulta SQL para que possamos salvá-los na sessão
    $stmt = $conn->prepare("SELECT id, nome, email, senha, pode_ver_bi, pode_ver_comissao_total, pode_ver_comissao_card FROM usuarios WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();

        // Verifica a senha usando o hash do banco
        if (password_verify($senha, $usuario['senha'])) {
            // Regenera o ID da sessão para segurança
            session_regenerate_id(true);

            // Armazena os dados do usuário na sessão
            $_SESSION['user_id'] = $usuario['id'];
            $_SESSION['user_nome'] = $usuario['nome'];

            // AJUSTE 2: Salvo o email do usuário na sessão para verificação de permissões
            $_SESSION['user_email'] = $usuario['email'];
            
            // AJUSTE 3: Carrega as permissões do usuário na sessão
            $_SESSION['pode_ver_bi'] = (bool)$usuario['pode_ver_bi'];
            $_SESSION['pode_ver_comissao_total'] = (bool)$usuario['pode_ver_comissao_total'];
            $_SESSION['pode_ver_comissao_card'] = (bool)$usuario['pode_ver_comissao_card'];

            header('Location: index.php');
            exit();
        } else {
            $error = "Senha incorreta. Por favor, tente novamente.";
        }
    } else {
        $error = "Nenhuma conta encontrada com este email.";
    }
    $stmt->close();
}
$anoAtual = date("Y");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gerenciador de Seguros</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f0f2f5; /* Um cinza claro para o fundo */
        }
        .login-container {
            min-height: 100vh;
        }
        .login-card {
            max-width: 450px;
            width: 100%;
            border: none;
            border-radius: 1rem;
        }
        .logo {
            max-width: 200px;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid d-flex justify-content-center align-items-center login-container">
        
        <div class="card shadow-lg p-4 login-card">
            <div class="card-body text-center">
                
                <img src="IMG/logo.png" alt="Logo da Empresa" class="logo">
                <h3 class="card-title mb-4">Acesso ao Sistema</h3>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger text-start d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> 
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="login.php" class="text-start">
                    <div class="mb-3">
                        <label for="email" class="form-label"><i class="bi bi-envelope-fill"></i> Email</label>
                        <input type="email" class="form-control" id="email" name="email" required autocomplete="email" autofocus>
                    </div>
                    <div class="mb-4">
                        <label for="senha" class="form-label"><i class="bi bi-lock-fill"></i> Senha</label>
                        <input type="password" class="form-control" id="senha" name="senha" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100 btn-lg">
                        <i class="bi bi-box-arrow-in-right"></i> Entrar
                    </button>
                </form>

            </div>
        </div>

    </div>

    <footer class="text-center text-muted py-4 fixed-bottom">
        <p>&copy; <?php echo $anoAtual; ?> MRG Seguros. Todos os direitos reservados.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>