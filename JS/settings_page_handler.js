/**
 * Localização: /JS/settings_page_handler.js
 * Gerencia a interatividade do modal de usuários na página de configurações.
 */
document.addEventListener('DOMContentLoaded', function () {
    const userModal = document.getElementById('userModal');
    if (!userModal) return; // Sai se o modal não estiver na página

    const modalTitle = userModal.querySelector('.modal-title');
    const formAction = userModal.querySelector('#formAction');
    const userId = userModal.querySelector('#userId');
    const userName = userModal.querySelector('#userName');
    const userEmail = userModal.querySelector('#userEmail');
    const userPassword = userModal.querySelector('#userPassword');
    const passwordHelpText = document.getElementById('passwordHelp');

    // Captura os checkboxes de permissão
    const podeVerBi = userModal.querySelector('#podeVerBi');
    const podeVerComissaoTotal = userModal.querySelector('#podeVerComissaoTotal');
    const podeVerComissaoCard = userModal.querySelector('#podeVerComissaoCard');

    // Limpa o modal para Adicionar Usuário
    const addUserBtn = document.getElementById('addUserBtn');
    if (addUserBtn) {
        addUserBtn.addEventListener('click', function () {
            modalTitle.textContent = 'Adicionar Usuário';
            formAction.value = 'add';
            userId.value = '';
            userName.value = '';
            userEmail.value = '';
            userPassword.value = '';
            userPassword.setAttribute('required', 'required');
            passwordHelpText.style.display = 'none';

            // Garante que as permissões comecem desmarcadas
            podeVerBi.checked = false;
            podeVerComissaoTotal.checked = false;
            podeVerComissaoCard.checked = false;
        });
    }

    // Preenche o modal para Editar Usuário
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function () {
            modalTitle.textContent = 'Editar Usuário';
            formAction.value = 'edit';
            userId.value = this.dataset.id;
            userName.value = this.dataset.nome;
            userEmail.value = this.dataset.email;
            userPassword.value = ''; // Limpa o campo de senha
            userPassword.removeAttribute('required');
            passwordHelpText.style.display = 'block';

            // Lê os data-attributes e define o estado dos checkboxes
            // A comparação '== 1' converte a string '1' ou '0' para um booleano true/false
            podeVerBi.checked = this.dataset.podeVerBi == '1';
            podeVerComissaoTotal.checked = this.dataset.podeVerComissaoTotal == '1';
            podeVerComissaoCard.checked = this.dataset.podeVerComissaoCard == '1';
        });
    });
});