<?php
/**
 * Lógica para determinar a estilização do card do cliente.
 */

$borderColorClass = '';
$textColorClass = '';

$inicioVigencia = new DateTime($cliente['inicio_vigencia']);
$finalVigencia = $cliente['final_vigencia'] ? new DateTime($cliente['final_vigencia']) : (clone $inicioVigencia)->add(new DateInterval('P1Y'));

$intervalo = $inicioVigencia->diff($finalVigencia);
$isVigenciaCurta = $intervalo->y < 1 && ($intervalo->days > 0);

switch ($cliente['status']) {
    case 'Emitida': $textColorClass = 'text-success'; break;
    case 'Cancelado': $textColorClass = 'text-danger'; break;
    default: $textColorClass = 'custom-text-blue'; break;
}

if ($isVigenciaCurta) {
    $borderColorClass = 'border-warning';
} else {
    switch ($cliente['status']) {
        case 'Emitida': $borderColorClass = 'border-success'; break;
        case 'Cancelado': $borderColorClass = 'border-danger'; break;
        default: $borderColorClass = 'custom-border-blue'; break;
    }
}
?>