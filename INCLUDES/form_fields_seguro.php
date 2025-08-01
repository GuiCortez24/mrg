<?php // INCLUDES/form_fields_seguro.php ?>
<div class="row mb-3">
    <div class="col-md-6">
        <label for="seguradora" class="form-label"><i class="bi bi-building"></i> Seguradora</label>
        <select class="form-select" id="seguradora" name="seguradora" required>
            <option value="" disabled selected>-- Selecione a Seguradora --</option>
            <?php 
            // Garante que o ponteiro do resultado seja reiniciado
            if ($seguradoras_result->num_rows > 0) {
                $seguradoras_result->data_seek(0);
                while($row = $seguradoras_result->fetch_assoc()) { 
                    echo '<option value="' . htmlspecialchars($row['nome']) . '">' . htmlspecialchars($row['nome']) . '</option>'; 
                } 
            }
            ?>
        </select>
    </div>
    <div class="col-md-6">
        <label for="tipo_seguro" class="form-label"><i class="bi bi-shield-check"></i> Ramo do Seguro</label>
        <select class="form-select" id="tipo_seguro" name="tipo_seguro" required>
            <option value="Seguro Auto">Seguro Auto</option>
            <option value="Seguro Moto">Seguro Moto</option>
            <option value="Seguro Residencial">Seguro Residencial</option>
            <option value="Seguro Empresarial">Seguro Empresarial</option>
            <option value="Seguro de Vida">Seguro de Vida</option>
            <option value="Acidentes Pessoais">Acidentes Pessoais</option>
            <option value="Consórcio">Consórcio</option>
        </select>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-12">
        <label for="item_segurado" class="form-label"><i class="bi bi-box-seam"></i> Item Segurado</label>
        <input type="text" class="form-control" id="item_segurado" name="item_segurado" placeholder="Ex: Veículo Toyota Corolla, Residência na Rua X, etc.">
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-6" id="campo_placa" style="display: none;">
        <label for="placa" class="form-label"><i class="bi bi-car-front-fill"></i> Placa do Veículo</label>
        <input type="text" class="form-control" id="placa" name="item_identificacao" placeholder="ABC-1234 ou ABC1D23">
    </div>
    <div class="col-md-6" id="campo_identificacao" style="display: none;">
        <label for="identificacao" class="form-label"><i class="bi bi-hash"></i> Número de Identificação</label>
        <input type="text" class="form-control" id="identificacao" name="item_identificacao" placeholder="Nº do Chassi, Aparelho, etc.">
    </div>
</div>