<?php
/**
 * Localização: INCLUDES/cliente_card.php
 *
 * Este arquivo funciona como um "montador" para o componente completo do card do cliente.
 * Ele garante que as dependências, como as funções de permissão, estejam carregadas
 * e inclui os sub-componentes de forma ordenada.
 */

// Garante que a função hasPermission() e outras estejam disponíveis
require_once __DIR__ . '/functions.php';

// 1. Processa a lógica de cores e define as variáveis de classe
require __DIR__ . '/card_components/cliente_card_logic.php';

// 2. Renderiza a parte visual do card
require __DIR__ . '/card_components/cliente_card_view.php';

// 3. Renderiza o HTML do modal de detalhes (que fica oculto)
require __DIR__ . '/card_components/cliente_modal_detalhes.php';

// 4. Renderiza o HTML do modal de anotações (que também fica oculto)
require __DIR__ . '/card_components/cliente_modal_anotacoes.php';
?>