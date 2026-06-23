<?php
/*
 * Configuración del módulo Encuesta de Satisfacción · 0km.
 * Versión moderna (sobre /comun/), espejo de encuesta_usados/config.php pero
 * apuntando a la tabla `asignaciones` y a las tablas `enc_*` existentes.
 *
 * Se conservan los NOMBRES de constantes del módulo viejo (BASE_URL_ENCUESTA,
 * ENCUESTA_*) para no romper config.local.php ni links/QR ya emitidos.
 */

// Override local (producción u otro entorno) — nunca commitear config.local.php
if (file_exists(__DIR__ . '/config.local.php')) include __DIR__ . '/config.local.php';

// URL base para generar los links públicos de encuesta.
if (!defined('BASE_URL_ENCUESTA'))
    define('BASE_URL_ENCUESTA', 'http://asignacion.oo/encuesta/publico/responder.php');

// Fecha mínima desde la que se traen entregas (igual que el módulo viejo)
if (!defined('ENCUESTA_FECHA_DESDE')) define('ENCUESTA_FECHA_DESDE', '2023/01/01');

// Perfiles con acceso al panel admin del módulo (entregas + resultados)
if (!defined('ENCUESTA_PERFILES')) define('ENCUESTA_PERFILES', [1, 2, 5, 7, 14]);

// Usuarios (idusuario) con permiso para configurar la encuesta (preguntas, áreas, niveles)
if (!defined('ENCUESTA_USUARIOS_CONFIG')) define('ENCUESTA_USUARIOS_CONFIG', [11]);
