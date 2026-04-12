<?php

// ============================================================
// Configuración del módulo de Encuesta de Satisfacción
// ============================================================

// URL base para generar los links de encuesta.
// Cambiar en producción al dominio real.
define('BASE_URL_ENCUESTA', 'http://localhost/encuesta/responder.php');

// Fecha mínima desde la que se traen entregas
define('ENCUESTA_FECHA_DESDE', '2023/01/01');

// Perfiles con acceso al panel admin del módulo
define('ENCUESTA_PERFILES', [1, 5, 14]);

?>
