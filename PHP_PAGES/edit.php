<?php
/**
 * Localização: /PHP_PAGES/edit.php
 * Página para editar um cliente existente (versão refatorada).
 */

$page_title = "Editar Proposta";
include '../db.php';

// Validação do ID
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    die("ID inválido.");
}
$id = $_GET['id'];

// Busca os dados do cliente para preencher o formulário
$stmt_cliente = $conn->prepare("SELECT * FROM clientes WHERE id = ?");
$stmt_cliente->bind_param("i", $id);
$stmt_cliente->execute();
$result_cliente = $stmt_cliente->get_result();
if ($result_cliente->num_rows === 0) {
    die("Cliente não encontrado.");
}
$cliente = $result_cliente->fetch_assoc();
$stmt_cliente->close();

// Busca as listas para os selects
$seguradoras_result = $conn->query("SELECT nome FROM seguradoras ORDER BY nome ASC");
$ramos_result = $conn->query("SELECT nome FROM ramos_seguro ORDER BY nome ASC"); // NOVO: Busca os ramos

include '../INCLUDES/header.php';
?>

<div class="container mt-5 mb-5">
    <div class="card shadow-lg">
        <div class="card-header text-center bg-primary text-white">
            <h2><i class="bi bi-pencil-square"></i> Editar Proposta</h2>
        </div>
        <div class="card-body p-4">

            <?php if (isset($_GET['status']) && $_GET['status'] == 'error'): ?>
                <div class="alert alert-danger" role="alert">
                    <strong>Erro!</strong> Não foi possível atualizar. Detalhes: <?php echo htmlspecialchars($_GET['msg']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_GET['status']) && $_GET['status'] == 'pdf_deleted'): ?>
                <div class="alert alert-success" role="alert">
                    Anexo removido com sucesso!
                </div>
            <?php endif; ?>

            <form method="POST" action="../PHP_ACTION/handle_edit.php" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $cliente['id']; ?>">

                <?php 
                    // Os arquivos include agora usarão a variável $cliente para preencher os campos
                    include '../INCLUDES/form_fields_pessoais.php'; 
                ?>
                <hr class="my-4">
                <?php include '../INCLUDES/form_fields_seguro.php'; ?>
                <hr class="my-4">
                <?php include '../INCLUDES/form_fields_financeiro.php'; ?>
                <hr class="my-4">

                <h5><i class="bi bi-paperclip"></i> Anexos da Proposta</h5>
                <div class="card card-body mb-3">
                    <?php
                    $pdf_data = $cliente['pdf_path'];
                    $pdfs = json_decode($pdf_data, true);
                    $pdf_list = is_array($pdfs) ? $pdfs : (!empty($pdf_data) ? [$pdf_data] : []);

                    if (!empty($pdf_list)):
                    ?>
                        <ul class="list-group list-group-flush">
                        <?php foreach($pdf_list as $pdf_path): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="../<?php echo htmlspecialchars($pdf_path); ?>" target="_blank">
                                    <i class="bi bi-file-earmark-pdf"></i> <?php echo htmlspecialchars(basename($pdf_path)); ?>
                                </a>
                                <a href="../PHP_ACTION/delete_pdf.php?cliente_id=<?php echo $cliente['id']; ?>&pdf_path=<?php echo urlencode($pdf_path); ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir este anexo?');">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </li>
                        <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-center text-muted">Nenhum anexo encontrado.</p>
                    <?php endif; ?>
                </div>

                <div class="row mb-4">
                    <div class="col-md-12">
                        <label for="pdfs" class="form-label"><i class="bi bi-file-earmark-arrow-up"></i> Adicionar Novos Anexos (PDF)</label>
                        <input type="file" class="form-control" id="pdfs" name="pdfs[]" accept=".pdf" multiple>
                    </div>
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-success btn-lg"><i class="bi bi-check-circle"></i> Salvar Alterações</button>
                    <a href="../index.php" class="btn btn-secondary btn-lg"><i class="bi bi-x-circle"></i> Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../INCLUDES/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tipoSeguroSelect = document.getElementById('tipo_seguro');
    const campoPlaca = document.getElementById('campo_placa');
    const inputPlaca = document.getElementById('placa');
    const campoIdentificacao = document.getElementById('campo_identificacao');
    const inputIdentificacao = document.getElementById('identificacao');

    function toggleCampos() {
        const selectedValue = tipoSeguroSelect.value;
        const isVeiculo = selectedValue === 'Seguro Auto' || selectedValue === 'Seguro Moto';
        
        campoPlaca.style.display = isVeiculo ? 'block' : 'none';
        inputPlaca.disabled = !isVeiculo;
        
        campoIdentificacao.style.display = isVeiculo ? 'none' : 'block';
        inputIdentificacao.disabled = isVeiculo;
    }

    tipoSeguroSelect.addEventListener('change', function() {
        if(inputPlaca) inputPlaca.value = '';
        if(inputIdentificacao) inputIdentificacao.value = '';
        toggleCampos();
    });

    toggleCampos();
});
</script>