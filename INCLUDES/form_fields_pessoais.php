<?php // INCLUDES/form_fields_pessoais.php ?>

<div class="row mb-3">
    <div class="col-md-6">
        <label for="inicio_vigencia" class="form-label"><i class="bi bi-calendar-day"></i> Início Vigência</label>
        <input type="date" class="form-control" id="inicio_vigencia" name="inicio_vigencia" value="<?php echo isset($cliente) ? htmlspecialchars($cliente['inicio_vigencia']) : ''; ?>" required>
    </div>
    <div class="col-md-6">
        <label for="final_vigencia" class="form-label"><i class="bi bi-calendar-check"></i> Final Vigência (Opcional)</label>
        <input type="date" class="form-control" id="final_vigencia" name="final_vigencia" value="<?php echo isset($cliente) ? htmlspecialchars($cliente['final_vigencia']) : ''; ?>">
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-6">
        <label for="nome" class="form-label"><i class="bi bi-person"></i> Nome Completo do Cliente</label>
        <input type="text" class="form-control" id="nome" name="nome" placeholder="Ex: João da Silva" value="<?php echo isset($cliente) ? htmlspecialchars($cliente['nome']) : ''; ?>" required>
    </div>
    <div class="col-md-6">
        <label for="cpf" class="form-label"><i class="bi bi-card-text"></i> CPF/CNPJ</label>
        <input type="text" class="form-control" id="cpf" name="cpf" placeholder="Apenas números" value="<?php echo isset($cliente) ? htmlspecialchars($cliente['cpf']) : ''; ?>" required>
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-6">
        <label for="numero" class="form-label"><i class="bi bi-telephone"></i> Celular</label>
        <input type="text" class="form-control" id="numero" name="numero" placeholder="(00) 00000-0000" value="<?php echo isset($cliente) ? htmlspecialchars($cliente['numero']) : ''; ?>" required>
    </div>
    <div class="col-md-6">
        <label for="email" class="form-label"><i class="bi bi-envelope"></i> Email</label>
        <input type="email" class="form-control" id="email" name="email" placeholder="email@exemplo.com" value="<?php echo isset($cliente) ? htmlspecialchars($cliente['email']) : ''; ?>" required>
    </div>
</div>