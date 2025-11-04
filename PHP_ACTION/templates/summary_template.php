<?php
/**
 * Localização: /PHP_ACTION/templates/summary_template.php
 * Template HTML para o corpo do modal de resumo mensal.
 * Espera que as variáveis $data_agrupada, $summary_totals, $status_counts e $pode_ver_comissao estejam definidas.
 */
?>
<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="table-light">
            <tr>
                <th>Seguradora</th>
                <th>Tipo de Seguro</th>
                <th>Total Prêmio Líquido</th>
                <?php if ($pode_ver_comissao): ?>
                    <th>Total Comissão</th>
                <?php endif; ?>
                <th>Total Clientes</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($data_agrupada) > 0): ?>
                <?php foreach ($data_agrupada as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['seguradora']); ?></td>
                        <td><?php echo htmlspecialchars($row['tipo_seguro']); ?></td>
                        <td>R$ <?php echo number_format($row['total_premio'], 2, ',', '.'); ?></td>
                        <?php if ($pode_ver_comissao): ?>
                            <td>R$ <?php echo number_format($row['total_comissao'], 2, ',', '.'); ?></td>
                        <?php endif; ?>
                        <td><?php echo $row['total_clientes']; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="<?php echo $pode_ver_comissao ? 5 : 4; ?>" class="text-center">Nenhum dado de produção encontrado para este mês.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<hr>

<div class="row mt-4">
    <div class="col-md-6">
        <h6><strong>Resumo Geral do Mês:</strong></h6>
        <ul class="list-group list-group-flush">
            <li class="list-group-item"><strong>Total Prêmio Líquido:</strong> R$ <?php echo number_format($summary_totals['total_premio_mes'], 2, ',', '.'); ?></li>
            <?php if ($pode_ver_comissao): ?>
                <li class="list-group-item"><strong>Total Comissão:</strong> R$ <?php echo number_format($summary_totals['total_comissao_mes'], 2, ',', '.'); ?></li>
            <?php endif; ?>
            <li class="list-group-item"><strong>Total de Seguradoras:</strong> <?php echo count($summary_totals['seguradoras_unicas']); ?></li>
            <li class="list-group-item"><strong>Total de Tipos de Seguro:</strong> <?php echo count($summary_totals['tipos_seguro_unicos']); ?></li>
            <li class="list-group-item"><strong>Apólices Emitidas:</strong> <?php echo $status_counts['emitidas']; ?></li>
            <li class="list-group-item"><strong>Apólices Canceladas:</strong> <?php echo $status_counts['canceladas']; ?></li>
        </ul>
    </div>
</div>

<hr>

<h5 class="text-center mt-4 mb-3">Análise Gráfica</h5>
<div class="row">
    <div class="col-md-6"><canvas id="chartPremioPorSeguradora"></canvas></div>
    <div class="col-md-6"><canvas id="chartClientesPorSeguradora"></canvas></div>
</div>
<div class="row mt-4">
    <div class="col-md-6"><canvas id="chartPremioPorTipo"></canvas></div>
    <div class="col-md-6"><canvas id="chartClientesPorTipo"></canvas></div>
</div>