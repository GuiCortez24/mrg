<?php
/**
 * Localização: /PHP_PAGES/relatorio_bi.php
 * Página para exibir o BI incorporado do Looker Studio.
 */

$page_title = "BI - Análise de Dados";
include '../INCLUDES/header.php'; // Inclui o header padrão
include '../INCLUDES/navbar.php'; // Inclui a barra de navegação
?>

<style>
    .iframe-container {
        position: relative;
        overflow: hidden;
        width: 100%;
        /* Proporção da tela, ajuste se necessário (ex: 56.25% para 16:9) */
        padding-top: 65%; 
    }
    .iframe-container iframe {
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        width: 100%;
        height: 100%;
        border: none;
    }
</style>

<div class="container mt-4">
    <h2 class="mb-4">
        <i class="bi bi-bar-chart-line-fill text-success"></i>
        Business Intelligence - Análise de Produção
    </h2>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="iframe-container">

                <iframe src="https://lookerstudio.google.com/embed/reporting/0520e9f9-6176-4490-bdc0-67a6315c2bbf/page/oskSF" frameborder="0" style="border:0" allowfullscreen sandbox="allow-storage-access-by-user-activation allow-scripts allow-same-origin allow-popups allow-popups-to-escape-sandbox"></iframe>

            </div>
        </div>
    </div>
</div>

<?php include '../INCLUDES/footer.php'; // Inclui o rodapé padrão ?>