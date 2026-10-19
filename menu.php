<?php
/**
 * Alias de menú → catálogo (URL amigable para demo / checklist).
 */
$qs = isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] !== ''
    ? '?' . $_SERVER['QUERY_STRING']
    : '';
header('Location: catalogo.php' . $qs);
exit;
