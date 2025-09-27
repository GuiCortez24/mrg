<?php
$page_title = "Gerenciar Seguradoras";
include '../db.php';
include '../INCLUDES/header.php';
include '../INCLUDES/navbar.php';

// --- CONFIGURAÇÕES DE PAGINAÇÃO E BUSCA ---
$items_per_page = 8; // Quantos cards por página
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';

// --- CONSTRUÇÃO DA QUERY SQL ---
$sql_where = '';
$params = [];
$param_types = '';

if (!empty($search_term)) {
    $sql_where = " WHERE nome LIKE ?";
    $params[] = "%" . $search_term . "%";
    $param_types .= 's';
}

// 1. Query para contar o total de itens
$sql_count = "SELECT COUNT(id) as total FROM seguradoras" . $sql_where;
$stmt_count = $conn->prepare($sql_count);
if (!empty($params)) {
    $stmt_count->bind_param($param_types, ...$params);
}
$stmt_count->execute();
$total_items = $stmt_count->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_items / $items_per_page);

// 2. Query para buscar os itens da página atual
$offset = ($current_page - 1) * $items_per_page;
$sql_data = "SELECT * FROM seguradoras" . $sql_where . " ORDER BY nome ASC LIMIT ? OFFSET ?";
$param_types .= 'ii';
$params[] = $items_per_page;
$params[] = $offset;

$stmt_data = $conn->prepare($sql_data);
$stmt_data->bind_param($param_types, ...$params);
$stmt_data->execute();
$result = $stmt_data->get_result();
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <h2 class="mb-0"><i class="bi bi-building text-success"></i> Informações das Seguradoras</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="bi bi-plus-circle"></i> Adicionar Nova
        </button>
    </div>
    
    <?php include '../INCLUDES/seguradoras/seguradora_search_form.php'; ?>

    <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Ação realizada com sucesso!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($seguradora = $result->fetch_assoc()): ?>
                <?php include '../INCLUDES/seguradora_card.php'; ?>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <p class="text-center text-muted mt-4">Nenhuma seguradora encontrada.</p>
            </div>
        <?php endif; ?>
    </div>

    <?php include '../INCLUDES/seguradoras/pagination.php'; ?>
</div>

<?php include '../INCLUDES/seguradoras/seguradora_add_modal.php'; ?>
<?php include '../INCLUDES/seguradoras/seguradora_edit_modal.php'; ?>

<?php include '../INCLUDES/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- LÓGICA PARA PREENCHER O MODAL DE EDIÇÃO ---
    const editModal = document.getElementById('editModal');
    if (editModal) {
        editModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget; // Botão que acionou o modal

            // Extrai os dados dos atributos data-* do botão
            const id = button.getAttribute('data-id');
            const nome = button.getAttribute('data-nome');
            const usuario = button.getAttribute('data-usuario');
            const numero_0800 = button.getAttribute('data-numero0800');

            // Seleciona os campos do formulário do modal
            const form = editModal.querySelector('form');
            const modalTitle = editModal.querySelector('.modal-title');
            const inputId = form.querySelector('input[name="id"]');
            const inputNome = form.querySelector('input[name="nome"]');
            const inputUsuario = form.querySelector('input[name="usuario"]');
            const inputSenha = form.querySelector('input[name="senha"]');
            const inputNumero0800 = form.querySelector('input[name="numero_0800"]');

            // Preenche os campos do formulário com os dados extraídos
            modalTitle.textContent = 'Editar ' + nome;
            inputId.value = id;
            inputNome.value = nome;
            inputUsuario.value = usuario;
            inputSenha.value = ''; // Limpa o campo senha por segurança
            inputNumero0800.value = numero_0800;
        });
    }

    // --- LÓGICA PARA O "OLHO" DA SENHA NOS CARDS ---
    document.body.addEventListener('click', function(event) {
        const toggleBtn = event.target.closest('.toggle-password-btn');
        if (!toggleBtn) return; // Se o clique não foi no botão, ignora

        const passwordContainer = toggleBtn.closest('.d-flex');
        const passwordSpan = passwordContainer.querySelector('.password-text');
        const icon = toggleBtn.querySelector('i');
        const isHidden = icon.classList.contains('bi-eye-fill');

        if (isHidden) {
            // Mostra a senha
            passwordSpan.textContent = passwordSpan.dataset.password;
            icon.classList.remove('bi-eye-fill');
            icon.classList.add('bi-eye-slash-fill');
        } else {
            // Oculta a senha
            passwordSpan.textContent = '••••••••';
            icon.classList.remove('bi-eye-slash-fill');
            icon.classList.add('bi-eye-fill');
        }
    });
});
</script>