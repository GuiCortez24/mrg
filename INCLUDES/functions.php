<?php
// INCLUDES/functions.php

function formatDate($date)
{
    if (empty($date) || $date === '0000-00-00') {
        return '';
    }
    $timestamp = strtotime($date);
    if ($timestamp === false) {
        return '';
    }
    return date('d/m/Y', $timestamp);
}

// Outras funções úteis podem ser adicionadas aqui no futuro.