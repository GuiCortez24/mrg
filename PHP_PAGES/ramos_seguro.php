<?php
// Inicia a sessão e inclui arquivos essenciais
// Supondo que você tenha um arquivo de autenticação para proteger a página
include '../auth.php'; 
include '../db.php';

// Variável para armazenar o ramo que está sendo editado
$ramo_to_edit = null;
$edit_mode = false;

// --- LÓGICA DE PROCESSAMENTO DE AÇÕES (POST E GET) ---

// Processar exclusão (via GET)
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM ramos_seguro WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header("Location: ramos_seguro.php?status=delete_success");
    } else {
        header("Location: ramos_seguro.php?status=delete_error");
    }
    exit();
}

// Processar adição ou atualização (via POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // --- ATUALIZAÇÃO ---
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("UPDATE ramos_seguro SET nome = ? WHERE id = ?");
        $stmt->bind_param("si", $nome, $id);
        if ($stmt->execute()) {
            header("Location: ramos_seguro.php?status=update_success");
        } else {
            header("Location: ramos_seguro.php?status=update_error");
        }
    } else {
        // --- ADIÇÃO ---
        $stmt = $conn->prepare("INSERT INTO ramos_seguro (nome) VALUES (?)");
        $stmt->bind_param("s", $nome);
        if ($stmt->execute()) {
            header("Location: ramos_seguro.php?status=add_success");
        } else {
            // Verifica se o erro é de duplicidade
            if ($conn->errno == 1062) { 
                 header("Location: ramos_seguro.php?status=add_error&msg=duplicate");
            } else {
                 header("Location: ramos_seguro.php?status=add_error");
            }
        }
    }
    exit();
}

// Verificar se estamos em modo de edição (via GET)
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

// Incluir o header da página
$page_title = "Gerenciar Ramos de Seguro";
include '../INCLUDES/header.php';
?>

<div class="container mt-5 mb-5">
    <div class="card shadow-lg">
        <div class="card-header text-center bg-primary text-white">
            <h2><i class="bi bi-shield-check"></i> Gerenciar Ramos de Seguro</h2>
        </div>
        <div class="card-body p-4">

            <?php if (isset($_GET['status'])): ?>
                <?php
                    $status = $_GET['status'];
                    $alert_class = 'alert-danger';
                    $message = '';

                    if ($status == 'add_success') {
                        $alert_class = 'alert-success';
                        $message = '<strong>Sucesso!</strong> Novo ramo adicionado.';
                    } elseif ($status == 'update_success') {
                        $alert_class = 'alert-success';
                        $message = '<strong>Sucesso!</strong> Ramo atualizado.';
                    } elseif ($status == 'delete_success') {
                        $alert_class = 'alert-success';
                        $message = '<strong>Sucesso!</strong> Ramo excluído.';
                    } elseif ($status == 'add_error' && isset($_GET['msg']) && $_GET['msg'] == 'duplicate') {
                        $message = '<strong>Erro!</strong> Este ramo de seguro já existe.';
                    } elseif (str_contains($status, 'error')) {
                        $message = '<strong>Erro!</strong> Ocorreu um problema ao processar a solicitação.';
                    }
                ?>
                <div class="alert <?php echo $alert_class; ?>" role="alert">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <div class="card mb-4">
                <div class="card-header">
                    <h5><?php echo $edit_mode ? 'Editar Ramo de Seguro' : 'Adicionar Novo Ramo'; ?></h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="ramos_seguro.php">
                        <?php if ($edit_mode): ?>
                            <input type="hidden" name="id" value="<?php echo $ramo_to_edit['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="input-group">
                            <input type="text" class="form-control" name="nome" placeholder="Nome do Ramo de Seguro" value="<?php echo $edit_mode ? htmlspecialchars($ramo_to_edit['nome']) : ''; ?>" required>
                            <button type="submit" class="btn <?php echo $edit_mode ? 'btn-success' : 'btn-primary'; ?>">
                                <i class="bi <?php echo $edit_mode ? 'bi-check-circle' : 'bi-plus-circle'; ?>"></i>
                                <?php echo $edit_mode ? ' Salvar Alterações' : ' Adicionar Ramo'; ?>
                            </button>
                            <?php if ($edit_mode): ?>
                                <a href="ramos_seguro.php" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Cancelar Edição
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <h5 class="mt-4">Ramos Cadastrados</h5>
            <hr>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Nome do Ramo</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($ramos_result->num_rows > 0): ?>
                            <?php while($row = $ramos_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['nome']); ?></td>
                                    <td class="text-end">
                                        <a href="ramos_seguro.php?edit_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning me-2" title="Editar">
                                            <i class="bi bi-pencil-fill"></i> Editar
                                        </a>
                                        <a href="ramos_seguro.php?delete_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este ramo? Esta ação não pode ser desfeita.');" title="Excluir">
                                            <i class="bi bi-trash-fill"></i> Excluir
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2" class="text-center">Nenhum ramo de seguro cadastrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="text-center mt-4">
                 <a href="add.php" class="btn btn-secondary"><i class="bi bi-arrow-left-circle"></i> Voltar para Adicionar Cliente</a>
            </div>

        </div>
    </div>
</div>

<?php include '../INCLUDES/footer.php'; ?>