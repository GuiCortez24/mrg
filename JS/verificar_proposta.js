// JS/verificar_proposta.js

document.addEventListener('DOMContentLoaded', function() {
    
    // Tenta encontrar o campo da apólice/proposta na página
    const apoliceInput = document.getElementById('apolice');

    // ESTA LINHA É A MAIS IMPORTANTE:
    // Ela garante que o código abaixo só será executado se o campo 'apolice' for encontrado.
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
                .catch(error => {
                    console.error('Erro ao verificar proposta:', error);
                    feedbackEl.textContent = 'Não foi possível verificar a proposta.';
                    feedbackEl.className = 'invalid-feedback';
                });
            }
        });
    }
});