<?php // INCLUDES/footer.php ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/autonumeric@4.5.4/dist/autoNumeric.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../JS/verificar_proposta.js"></script>
    
    <script>
    $(document).ready(function() {
        // Máscara para CPF/CNPJ
        if ($('#cpf').length) {
            var options = {
                onKeyPress: function (cpf, e, field, options) {
                    var masks = ['000.000.000-000', '00.000.000/0000-00'];
                    var mask = (cpf.length > 14) ? masks[1] : masks[0];
                    $('#cpf').mask(mask, options);
                }
            };
            $('#cpf').mask('000.000.000-000', options);
        }

        // Máscara para Celular
        if ($('#numero').length) {
            $('#numero').mask('(00) 00000-0000');
        }

        // Máscara para Comissão
        if ($('#comissao').length) {
            $('#comissao').mask('00.00', {reverse: true});
        }

        // Formatação de Moeda para Prêmio Líquido
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
    });
    </script>

</body>
</html>