<?php
/**
 * Componente: Modal para Adicionar/Editar Usuário.
 * Inclui campos para dados pessoais e permissões de acesso.
 */
?>
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="../PHP_ACTION/handle_users.php" method="POST" id="userForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalLabel">Adicionar Usuário</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="user_id" id="userId">

                    <div class="mb-3">
                        <label for="userName" class="form-label">Nome Completo</label>
                        <input type="text" class="form-control" id="userName" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="userEmail" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="userEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="userPassword" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="userPassword" name="senha">
                        <div id="passwordHelp" class="form-text">Deixe em branco para não alterar a senha existente.</div>
                    </div>

                    <hr>
                    <h6 class="mb-3">Permissões do Usuário</h6>

                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" role="switch" id="podeVerBi" name="pode_ver_bi" value="1">
                        <label class="form-check-label" for="podeVerBi">Pode ver BI / Análise</label>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" role="switch" id="podeVerComissaoTotal" name="pode_ver_comissao_total" value="1">
                        <label class="form-check-label" for="podeVerComissaoTotal">Pode ver Resumo de Comissão Total</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="podeVerComissaoCard" name="pode_ver_comissao_card" value="1">
                        <label class="form-check-label" for="podeVerComissaoCard">Pode ver Comissão no Card do Cliente</label>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>