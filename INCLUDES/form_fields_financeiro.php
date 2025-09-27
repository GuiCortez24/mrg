<?php // INCLUDES/form_fields_financeiro.php ?>

<div class="row mb-3">
    <div class="col-md-6">
        <label for="apolice" class="form-label"><i class="bi bi-file-earmark-text"></i> Nº da Proposta</label>
        <input type="text" class="form-control" id="apolice" name="apolice" placeholder="Número da Proposta / Apólice" value="<?php echo isset($cliente) ? htmlspecialchars($cliente['apolice']) : ''; ?>" required>
        <div id="apolice-feedback" class="form-text"></div>
    </div>
    <div class="col-md-6">
        <label for="status" class="form-label"><i class="bi bi-tags"></i> Status da Proposta</label>
        <select class="form-select" id="status" name="status">
            <option value="Aguardando Emissão" <?php echo (isset($cliente) && $cliente['status'] == 'Aguardando Emissão') ? 'selected' : ''; ?>>Aguardando Emissão</option>
            <option value="Emitida" <?php echo (isset($cliente) && $cliente['status'] == 'Emitida') ? 'selected' : ''; ?>>Emitida</option>
            <option value="Cancelado" <?php echo (isset($cliente) && $cliente['status'] == 'Cancelado') ? 'selected' : ''; ?>>Cancelado</option>
        </select>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-6">
        <label for="premio_liquido" class="form-label"><i class="bi bi-currency-dollar"></i> Prêmio Líquido</label>
        <input type="text" class="form-control" id="premio_liquido" name="premio_liquido" placeholder="Ex: 1250.50" value="<?php echo isset($cliente) ? htmlspecialchars($cliente['premio_liquido']) : ''; ?>" required>
    </div>
    <div class="col-md-6">
        <label for="comissao" class="form-label"><i class="bi bi-percent"></i> Comissão (%)</label>
        <input type="text" class="form-control" id="comissao" name="comissao" placeholder="Ex: 20.00" value="<?php echo isset($cliente) ? htmlspecialchars($cliente['comissao']) : ''; ?>" required>
    </div>
</div>