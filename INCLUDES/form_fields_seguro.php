<?php // INCLUDES/form_fields_seguro.php ?>

<div class="row mb-3">
    <div class="col-md-6">
        <label for="seguradora" class="form-label"><i class="bi bi-building"></i> Seguradora</label>
        <select class="form-select" id="seguradora" name="seguradora" required>
            <option value="" disabled>-- Selecione a Seguradora --</option>
            <?php 
            if ($seguradoras_result->num_rows > 0) {
                $seguradoras_result->data_seek(0);
                while($row = $seguradoras_result->fetch_assoc()) {
                    $nome_seguradora = htmlspecialchars($row['nome']);
                    // ALTERADO: Adiciona 'selected' se a seguradora corresponder à do cliente
                    $selected = (isset($cliente) && $cliente['seguradora'] == $nome_seguradora) ? 'selected' : '';
                    echo "<option value=\"{$nome_seguradora}\" {$selected}>{$nome_seguradora}</option>";
                }
            }
            ?>
        </select>
    </div>
    <div class="col-md-6">
        <div class="d-flex justify-content-between align-items-center">
            <label for="tipo_seguro" class="form-label mb-0"><i class="bi bi-shield-check"></i> Ramo do Seguro</label>
            <a href="../PHP_PAGES/ramos_seguro.php" class="text-secondary" title="Gerenciar Ramos do Seguro">
                <i class="bi bi-gear-fill"></i>
            </a>
        </div>
        <select class="form-select" id="tipo_seguro" name="tipo_seguro" required>
            <option value="" disabled>-- Selecione o Ramo --</option>
            <?php 
            // ALTERADO: Carrega dinamicamente e seleciona a opção correta
            if ($ramos_result && $ramos_result->num_rows > 0) {
                $ramos_result->data_seek(0);
                while($ramo = $ramos_result->fetch_assoc()) {
                    $nome_ramo = htmlspecialchars($ramo['nome']);
                    $selected = (isset($cliente) && $cliente['tipo_seguro'] == $nome_ramo) ? 'selected' : '';
                    echo "<option value=\"{$nome_ramo}\" {$selected}>{$nome_ramo}</option>";
                }
            }
            ?>
        </select>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-12">
        <label for="item_segurado" class="form-label"><i class="bi bi-box-seam"></i> Item Segurado</label>
        <input type="text" class="form-control" id="item_segurado" name="item_segurado" placeholder="Ex: Veículo Toyota Corolla, Residência na Rua X, etc." value="<?php echo isset($cliente) ? htmlspecialchars($cliente['item_segurado']) : ''; ?>">
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-6" id="campo_placa" style="display: none;">
        <label for="placa" class="form-label"><i class="bi bi-car-front-fill"></i> Placa do Veículo</label>
        <input type="text" class="form-control" id="placa" name="item_identificacao" placeholder="ABC-1234 ou ABC1D23" value="<?php echo isset($cliente) ? htmlspecialchars($cliente['item_identificacao']) : ''; ?>">
    </div>
    <div class="col-md-6" id="campo_identificacao" style="display: none;">
        <label for="identificacao" class="form-label"><i class="bi bi-hash"></i> Número de Identificação</label>
        <input type="text" class="form-control" id="identificacao" name="item_identificacao" placeholder="Nº do Chassi, Aparelho, etc." value="<?php echo isset($cliente) ? htmlspecialchars($cliente['item_identificacao']) : ''; ?>">
    </div>
</div>