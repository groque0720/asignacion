<?php
/*
 * Configuración del módulo Encuesta de Satisfacción · USADOS.
 * Espejo de encuesta/config.php pero independiente (tablas encu_*, vista de usados).
 */

// Override local (producción u otro entorno) — nunca commitear config.local.php
if (file_exists(__DIR__ . '/config.local.php')) include __DIR__ . '/config.local.php';

// URL base para generar los links públicos de encuesta de usados.
if (!defined('EU_BASE_URL'))
    define('EU_BASE_URL', 'http://asignacion.oo/encuesta_usados/publico/responder.php');

// Fecha mínima desde la que se traen entregas de usados
if (!defined('EU_FECHA_DESDE')) define('EU_FECHA_DESDE', '2023-01-01');

// Perfiles con acceso al panel admin del módulo (entregas + resultados)
if (!defined('EU_PERFILES')) define('EU_PERFILES', [1, 2, 5, 7, 14]);

// Usuarios (idusuario) con permiso para configurar la encuesta (preguntas, áreas, niveles)
if (!defined('EU_USUARIOS_CONFIG')) define('EU_USUARIOS_CONFIG', [11, 14]);
