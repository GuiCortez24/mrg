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
                            <a href="../PHP_ACTION/handle_ramos.php?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este ramo? Esta ação não pode ser desfeita.');" title="Excluir">
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