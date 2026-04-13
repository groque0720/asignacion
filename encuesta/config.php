<?php

// ============================================================
// Configuración del módulo de Encuesta de Satisfacción
// ============================================================

// Override local (producción u otro entorno) — nunca commitear config.local.php
if (file_exists(__DIR__ . '/config.local.php')) include __DIR__ . '/config.local.php';

// URL base para generar los links de encuesta.
// En producción, definir el valor real en config.local.php (no commitear ese archivo).
if (!defined('BASE_URL_ENCUESTA'))
    define('BASE_URL_ENCUESTA', 'http://asignacion.oo/encuesta/publico/responder.php');

// Fecha mínima desde la que se traen entregas
define('ENCUESTA_FECHA_DESDE', '2023/01/01');

// Perfiles con acceso al panel admin del módulo
define('ENCUESTA_PERFILES', [1, 5, 14]);

?>
