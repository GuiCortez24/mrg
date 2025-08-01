<?php // INCLUDES/footer.php ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/autonumeric@4.5.4/dist/autoNumeric.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../JS/verificar_proposta.js"></script>
    <script>
    $(document).ready(function() {
        // ================================================================
        // CORREÇÃO: Verifica se os elementos existem antes de iniciar os scripts
        // Isso elimina o erro "Uncaught Error" nas páginas que não têm esses campos.
        // ================================================================
        
        // Só inicia o AutoNumeric se o campo #premio_liquido existir
        if ($('#premio_liquido').length) {
            new AutoNumeric('#premio_liquido', {
                digitGroupSeparator: '.',
                decimalCharacter: ',',
                decimalPlaces: 2,
                currencySymbol: 'R$ ',
                currencySymbolPlacement: 'p',
                unformatOnSubmit: true
            });
        }

        // Só aplica a máscara de celular se o campo #numero existir
        if ($('#numero').length) {
            $('#numero').mask('(00) 00000-0000');
        }

        // Só aplica a máscara de comissão se o campo #comissao existir
        if ($('#comissao').length) {
            $('#comissao').mask('00.00', {reverse: true});
        }
    });
    </script>

</body>
</html>