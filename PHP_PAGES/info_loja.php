<?php
include '../db.php'; // Conexão com o banco de dados

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Adicionar nova seguradora
    if (isset($_POST['add_seguradora'])) {
        $nome = $_POST['nome'];
        $usuario = $_POST['usuario'];
        $senha = $_POST['senha'];
        $numero_0800 = $_POST['numero_0800'];

        $stmt = $conn->prepare("INSERT INTO seguradoras (nome, usuario, senha, numero_0800) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nome, $usuario, $senha, $numero_0800);
        $stmt->execute();
        $stmt->close();
    }

    // Atualizar informações da seguradora
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $usuario = $_POST['usuario'];
        $senha = $_POST['senha'];
        $numero_0800 = $_POST['numero_0800'];

        $stmt = $conn->prepare("UPDATE seguradoras SET usuario = ?, senha = ?, numero_0800 = ? WHERE id = ?");
        $stmt->bind_param("sssi", $usuario, $senha, $numero_0800, $id);
        $stmt->execute();
        $stmt->close();
    }

    if (isset($_POST['delete_seguradora'])) {
        $id = $_POST['id'];

        $stmt = $conn->prepare("DELETE FROM seguradoras WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }

    header('Location: info_loja.php');
    exit();
}

// Obter as informações das seguradoras
$result = $conn->query("SELECT * FROM seguradoras");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credenciais MRG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="../../CSS/info.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Informações das Seguradoras</h2>
    <a href="../index.php" class="btn btn-outline-secondary mb-4">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>

    <!-- Botão para abrir o modal de adicionar seguradora -->
    <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="bi bi-plus-circle"></i> Adicionar Seguradora
    </button>

    <!-- Modal para adicionar seguradora -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel"><i class="bi bi-plus-circle"></i> Adicionar Nova Seguradora</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="info_loja.php">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome</label>
                            <input type="text" class="form-control" id="nome" name="nome" required>
                        </div>
                        <div class="mb-3">
                            <label for="usuario" class="form-label">Usuário</label>
                            <input type="text" class="form-control" id="usuario" name="usuario" required>
                        </div>
                        <div class="mb-3">
                            <label for="senha" class="form-label">Senha</label>
                            <input type="text" class="form-control" id="senha" name="senha" required>
                        </div>
                        <div class="mb-3">
                            <label for="numero_0800" class="form-label">Número 0800</label>
                            <input type="number" class="form-control" id="numero_0800" name="numero_0800" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="submit" name="add_seguradora" class="btn btn-primary">Adicionar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Exibição das seguradoras como cards -->
    <div class="row">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-building me-2"></i><?php echo htmlspecialchars($row['nome']); ?></h5>
                        <p class="card-text"><i class="bi bi-person-fill me-2"></i><strong>Usuário:</strong> <?php echo htmlspecialchars($row['usuario']); ?></p>
                        <p class="card-text"><i class="bi bi-key-fill me-2"></i><strong>Senha:</strong> <?php echo htmlspecialchars($row['senha']); ?></p>
                        <p class="card-text"><i class="bi bi-telephone-fill me-2"></i><strong>Número 0800:</strong> <?php echo htmlspecialchars($row['numero_0800']); ?></p>
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['id']; ?>">
                                <i class="bi bi-pencil-square"></i> Editar
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $row['id']; ?>">
                                <i class="bi bi-trash"></i> Excluir
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal de Exclusão -->
            <div class="modal fade" id="deleteModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="bi bi-trash"></i> Confirmar Exclusão</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="POST" action="info_loja.php">
                            <div class="modal-body">
                                <p>Você tem certeza que deseja excluir esta seguradora?</p>
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" name="delete_seguradora" class="btn btn-danger">Excluir</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal de Edição -->
            <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Editar Informações</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="POST" action="info_loja.php">
                            <div class="modal-body">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <div class="mb-3">
                                    <label for="usuario<?php echo $row['id']; ?>" class="form-label">Usuário</label>
                                    <input type="text" class="form-control" id="usuario<?php echo $row['id']; ?>" name="usuario" value="<?php echo htmlspecialchars($row['usuario']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="senha<?php echo $row['id']; ?>" class="form-label">Senha</label>
                                    <input type="text" class="form-control" id="senha<?php echo $row['id']; ?>" name="senha" placeholder="Deixe em branco para manter a senha atual">
                                </div>
                                <div class="mb-3">
                                    <label for="numero_0800<?php echo $row['id']; ?>" class="form-label">Número 0800</label>
                                    <input type="number" class="form-control" id="numero_0800<?php echo $row['id']; ?>" name="numero_0800" value="<?php echo htmlspecialchars($row['numero_0800']); ?>" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
                                <button type="submit" name="edit_seguradora" class="btn btn-primary">Salvar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
