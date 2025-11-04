<?php
/**
 * Localização: /PHP_PAGES/add.php
 * Formulário para adicionar cliente, agora montado com componentes.
 */

$page_title = "Adicionar Novo Cliente";
include '../db.php';

// Busca as seguradoras (já existente)
$seguradoras_result = $conn->query("SELECT nome FROM seguradoras ORDER BY nome ASC");

// --- AJUSTE APLICADO AQUI ---
// Busca os ramos de seguro da nova tabela para alimentar o select dinamicamente
$ramos_result = $conn->query("SELECT nome FROM ramos_seguro ORDER BY nome ASC");
// --- FIM DO AJUSTE ---

include '../INCLUDES/header.php';
?>

<div class="container mt-5 mb-5">
    <div class="card shadow-lg">
        <div class="card-header text-center bg-primary text-white">
            <h2><i class="bi bi-person-plus-fill"></i> Adicionar Proposta de Cliente</h2>
        </div>
        <div class="card-body p-4">

            <?php if (isset($_GET['status'])): ?>
                <div class="alert <?php echo $_GET['status'] == 'success' ? 'alert-success' : 'alert-danger'; ?>" role="alert">
                    <?php
                        if ($_GET['status'] == 'success') {
                            echo '<strong>Sucesso!</strong> A proposta foi adicionada com sucesso!';
                        } else {
                            echo '<strong>Erro!</strong> Não foi possível adicionar a proposta. Detalhes: ' . (isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : 'Ocorreu um problema desconhecido.');
                        }
                    ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="../PHP_ACTION/handle_add.php" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">

                <?php include '../INCLUDES/form_fields_pessoais.php'; ?>
                
                <hr class="my-4">

                <?php include '../INCLUDES/form_fields_seguro.php'; ?>

                <?php include '../INCLUDES/form_fields_financeiro.php'; ?>
                
                <div class="row mb-2">
                    <div class="col-md-12">
                        <label class="form-label"><i class="bi bi-paperclip"></i> Anexar Proposta(s) (PDF)</label>
                        <div id="pdf-container">
                            <div class="input-group mb-2">
                                <input type="file" class="form-control" name="pdfs[]" accept=".pdf" required>
                            </div>
                        </div>
                        <button type="button" id="add-pdf-btn" class="btn btn-sm btn-success mt-2">
                            <i class="bi bi-plus-circle"></i> Adicionar outro anexo
                        </button>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-save"></i> Salvar Proposta</button>
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
        const isVeiculo = selectedValue === 'Seguro Auto' || selectedValue === 'Seguro Moto';
        campoPlaca.style.display = isVeiculo ? 'block' : 'none';
        inputPlaca.disabled = !isVeiculo;
        if (!isVeiculo) inputPlaca.value = '';
        campoIdentificacao.style.display = isVeiculo ? 'none' : 'block';
        inputIdentificacao.disabled = isVeiculo;
        if (isVeiculo) inputIdentificacao.value = '';
    }
    tipoSeguroSelect.addEventListener('change', toggleCampos);
    toggleCampos();

    // SCRIPT PARA MÚLTIPLOS ARQUIVOS
    const addPdfBtn = document.getElementById('add-pdf-btn');
    const pdfContainer = document.getElementById('pdf-container');

    addPdfBtn.addEventListener('click', function() {
        const newInputGroup = document.createElement('div');
        newInputGroup.className = 'input-group mb-2';

        const newFileInput = document.createElement('input');
        newFileInput.type = 'file';
        newFileInput.className = 'form-control';
        newFileInput.name = 'pdfs[]';
        newFileInput.accept = '.pdf';
        
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'btn btn-outline-danger';
        removeBtn.innerHTML = '<i class="bi bi-trash"></i>';
        
        removeBtn.addEventListener('click', function() {
            newInputGroup.remove();
        });

        newInputGroup.appendChild(newFileInput);
        newInputGroup.appendChild(removeBtn);
        pdfContainer.appendChild(newInputGroup);
    });

    // Lógica para verificação de proposta duplicada
    const apoliceInput = document.getElementById('apolice');
    if (apoliceInput) {
        const feedbackEl = document.getElementById('apolice-feedback');
        apoliceInput.addEventListener('blur', function() {
            const apoliceValue = this.value.trim();
            feedbackEl.textContent = '';
            this.classList.remove('is-invalid', 'is-valid');

            if (apoliceValue !== '') {
                feedbackEl.textContent = 'Verificando...';
                feedbackEl.className = 'form-text text-muted';
                fetch('../PHP_ACTION/verificar_proposta.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'apolice=' + encodeURIComponent(apoliceValue)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.exists) {
                        this.classList.add('is-invalid');
                        feedbackEl.textContent = 'Atenção: Este número de proposta já existe!';
                        feedbackEl.className = 'invalid-feedback';
                    } else {
                        this.classList.add('is-valid');
                        feedbackEl.textContent = 'Número de proposta disponível.';
                        feedbackEl.className = 'valid-feedback';
                    }
                })
                .catch(error => console.error('Erro ao verificar proposta:', error));
            }
        });
    }

    // Validação de Final Vigência obrigatório e ano >= Início Vigência
    const formEl = document.querySelector('form');
    const inicioVigenciaEl = document.getElementById('inicio_vigencia');
    const finalVigenciaEl = document.getElementById('final_vigencia');
    const finalFeedbackEl = document.getElementById('final_vigencia_feedback');

    function clearVigenciaValidation() {
        inicioVigenciaEl.classList.remove('is-invalid');
        finalVigenciaEl.classList.remove('is-invalid');
        if (finalFeedbackEl) finalFeedbackEl.textContent = 'O ano de Final Vigência deve ser maior ou igual ao de Início.';
    }

    function validateVigencia() {
        clearVigenciaValidation();
        const inicioVal = inicioVigenciaEl.value;
        const finalVal = finalVigenciaEl.value;

        if (!finalVal) {
            if (finalFeedbackEl) finalFeedbackEl.textContent = 'Final Vigência é obrigatório.';
            finalVigenciaEl.classList.add('is-invalid');
            return false;
        }
        if (inicioVal) {
            const anoInicio = new Date(inicioVal).getFullYear();
            const anoFinal = new Date(finalVal).getFullYear();
            if (anoFinal < anoInicio) {
                if (finalFeedbackEl) finalFeedbackEl.textContent = 'O ano de Final Vigência não pode ser menor que o ano de Início Vigência.';
                finalVigenciaEl.classList.add('is-invalid');
                inicioVigenciaEl.classList.add('is-invalid');
                return false;
            }
        }
        return true;
    }

    if (formEl && inicioVigenciaEl && finalVigenciaEl) {
        formEl.addEventListener('submit', function (e) {
            if (!validateVigencia()) {
                e.preventDefault();
                alert('Verifique as datas: Final Vigência é obrigatório e não pode ter ano menor que o Início.');
            }
        });
        finalVigenciaEl.addEventListener('change', validateVigencia);
        inicioVigenciaEl.addEventListener('change', validateVigencia);
    }
});
</script>