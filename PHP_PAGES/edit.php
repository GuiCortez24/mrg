<?php
/**
 * Localização: /PHP_PAGES/edit.php
 * Página para editar um cliente existente, com suporte a múltiplos PDFs e item segurado.
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

// Busca a lista de seguradoras
$seguradoras_result = $conn->query("SELECT nome FROM seguradoras ORDER BY nome ASC");

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

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="inicio_vigencia" class="form-label"><i class="bi bi-calendar-day"></i> Início Vigência</label>
                        <input type="date" class="form-control" id="inicio_vigencia" name="inicio_vigencia" value="<?php echo htmlspecialchars($cliente['inicio_vigencia']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="final_vigencia" class="form-label"><i class="bi bi-calendar-check"></i> Final Vigência</label>
                        <input type="date" class="form-control" id="final_vigencia" name="final_vigencia" value="<?php echo htmlspecialchars($cliente['final_vigencia']); ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nome" class="form-label"><i class="bi bi-person"></i> Nome</label>
                        <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($cliente['nome']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="cpf" class="form-label"><i class="bi bi-card-text"></i> CPF/CNPJ</label>
                        <input type="text" class="form-control" id="cpf" name="cpf" value="<?php echo htmlspecialchars($cliente['cpf']); ?>" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="numero" class="form-label"><i class="bi bi-telephone"></i> Celular</label>
                        <input type="text" class="form-control" id="numero" name="numero" value="<?php echo htmlspecialchars($cliente['numero']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label"><i class="bi bi-envelope"></i> Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($cliente['email']); ?>" required>
                    </div>
                </div>

                <hr class="my-4">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="seguradora" class="form-label"><i class="bi bi-building"></i> Seguradora</label>
                        <select class="form-select" id="seguradora" name="seguradora" required>
                            <?php while($row_seg = $seguradoras_result->fetch_assoc()): ?>
                                <option value="<?php echo htmlspecialchars($row_seg['nome']); ?>" <?php if ($cliente['seguradora'] == $row_seg['nome']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($row_seg['nome']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="tipo_seguro" class="form-label"><i class="bi bi-shield-check"></i> Ramo do Seguro</label>
                        <select class="form-select" id="tipo_seguro" name="tipo_seguro" required>
                            <option value="Seguro Auto" <?php if ($cliente['tipo_seguro'] == 'Seguro Auto') echo 'selected'; ?>>Seguro Auto</option>
                            <option value="Seguro Moto" <?php if ($cliente['tipo_seguro'] == 'Seguro Moto') echo 'selected'; ?>>Seguro Moto</option>
                            <option value="Seguro Residencial" <?php if ($cliente['tipo_seguro'] == 'Seguro Residencial') echo 'selected'; ?>>Seguro Residencial</option>
                            <option value="Seguro Empresarial" <?php if ($cliente['tipo_seguro'] == 'Seguro Empresarial') echo 'selected'; ?>>Seguro Empresarial</option>
                            <option value="Seguro de Vida" <?php if ($cliente['tipo_seguro'] == 'Seguro de Vida') echo 'selected'; ?>>Seguro de Vida</option>
                            <option value="Acidentes Pessoais" <?php if ($cliente['tipo_seguro'] == 'Acidentes Pessoais') echo 'selected'; ?>>Acidentes Pessoais</option>
                            <option value="Consórcio" <?php if ($cliente['tipo_seguro'] == 'Consórcio') echo 'selected'; ?>>Consórcio</option>
                            <option value="Seguro Transporte" <?php if ($cliente['tipo_seguro'] == 'Seguro Transporte') echo 'selected'; ?>>Seguro Transporte</option>
                            <option value="Seguro Saúde" <?php if ($cliente['tipo_seguro'] == 'Seguro Saúde') echo 'selected'; ?>>Seguro Saúde</option>
                            <option value="Seguro Dental" <?php if ($cliente['tipo_seguro'] == 'Seguro Dental') echo 'selected'; ?>>Seguro Dental</option>
                            <option value="Seguro Frota" <?php if ($cliente['tipo_seguro'] == 'Seguro Frota') echo 'selected'; ?>>Seguro Frota</option>
                            <option value="Seguro Agronegócio" <?php if ($cliente['tipo_seguro'] == 'Seguro Agronegócio') echo 'selected'; ?>>Seguro Agronegócio</option>
                            <option value="Seguro Viagem" <?php if ($cliente['tipo_seguro'] == 'Seguro Viagem') echo 'selected'; ?>>Seguro Viagem</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="item_segurado" class="form-label"><i class="bi bi-box-seam"></i> Item Segurado</label>
                        <input type="text" class="form-control" id="item_segurado" name="item_segurado" value="<?php echo htmlspecialchars($cliente['item_segurado'] ?? ''); ?>" placeholder="Ex: Veículo Toyota Corolla, Residência na Rua X, etc.">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6" id="campo_placa" style="display: none;">
                        <label for="placa" class="form-label"><i class="bi bi-car-front-fill"></i> Placa do Veículo</label>
                        <input type="text" class="form-control" id="placa" name="item_identificacao" value="<?php echo htmlspecialchars($cliente['item_identificacao']); ?>">
                    </div>
                    <div class="col-md-6" id="campo_identificacao" style="display: none;">
                        <label for="identificacao" class="form-label"><i class="bi bi-hash"></i> Número de Identificação</label>
                        <input type="text" class="form-control" id="identificacao" name="item_identificacao" value="<?php echo htmlspecialchars($cliente['item_identificacao']); ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="apolice" class="form-label"><i class="bi bi-file-earmark-text"></i> Nº da Proposta</label>
                        <input type="text" class="form-control" id="apolice" name="apolice" value="<?php echo htmlspecialchars($cliente['apolice']); ?>" required>
                        <div id="apolice-feedback" class="form-text"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="status" class="form-label"><i class="bi bi-tags"></i> Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="Aguardando Emissão" <?php if ($cliente['status'] == 'Aguardando Emissão') echo 'selected'; ?>>Aguardando Emissão</option>
                            <option value="Emitida" <?php if ($cliente['status'] == 'Emitida') echo 'selected'; ?>>Emitida</option>
                            <option value="Cancelado" <?php if ($cliente['status'] == 'Cancelado') echo 'selected'; ?>>Cancelado</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                     <div class="col-md-6">
                        <label for="premio_liquido" class="form-label"><i class="bi bi-currency-dollar"></i> Prêmio Líquido</label>
                        <input type="text" class="form-control" id="premio_liquido" name="premio_liquido" value="<?php echo htmlspecialchars($cliente['premio_liquido']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="comissao" class="form-label"><i class="bi bi-percent"></i> Comissão (%)</label>
                        <input type="text" class="form-control" id="comissao" name="comissao" value="<?php echo htmlspecialchars($cliente['comissao']); ?>" required>
                    </div>
                </div>

                <hr class="my-4">
                <h5><i class="bi bi-paperclip"></i> Anexos da Proposta</h5>
                <div class="card card-body mb-3">
                    <?php
                    $pdf_data = $cliente['pdf_path'];
                    $pdfs = json_decode($pdf_data, true);

                    // Lógica para lidar com os dois formatos de anexo (novo e antigo)
                    if (is_array($pdfs) && !empty($pdfs)) {
                        $pdf_list = $pdfs;
                    } elseif (!is_array($pdfs) && !empty($pdf_data)) {
                        $pdf_list = [$pdf_data]; // Trata o formato antigo como uma lista de um item
                    } else {
                        $pdf_list = [];
                    }

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
    // Lógica para os campos dinâmicos de Placa/ID
    const tipoSeguroSelect = document.getElementById('tipo_seguro');
    const campoPlaca = document.getElementById('campo_placa');
    const inputPlaca = document.getElementById('placa');
    const campoIdentificacao = document.getElementById('campo_identificacao');
    const inputIdentificacao = document.getElementById('identificacao');

    function toggleCampos() {
        const selectedValue = tipoSeguroSelect.value;
        inputPlaca.disabled = true;
        inputIdentificacao.disabled = true;

        if (selectedValue === 'Seguro Auto' || selectedValue === 'Seguro Moto') {
            campoPlaca.style.display = 'block';
            inputPlaca.disabled = false;
            campoIdentificacao.style.display = 'none';
        } else {
            campoIdentificacao.style.display = 'block';
            inputIdentificacao.disabled = false;
            campoPlaca.style.display = 'none';
        }
    }

    tipoSeguroSelect.addEventListener('change', function() {
        document.getElementById('placa').value = '';
        document.getElementById('identificacao').value = '';
        toggleCampos();
    });

    toggleCampos();
});
</script>