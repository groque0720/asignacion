<?php
/*
 * Controlador del módulo Encuesta de Satisfacción · USADOS.
 * Tab "Entregas": grilla de unidades usadas entregadas + generación de link/QR.
 * La interacción (carga, filtros, token) la maneja el componente Alpine vía data.php / token.php.
 */
require __DIR__ . '/config/config_app.php';   // sesión + auth + $con + $puedeEditar + $puedeConfigurar

$title        = 'Encuesta Usados · Derka y Vargas';
$fecha_actual = date('d/m/Y');

$bodyData = "encuestaUsados(" . ($puedeConfigurar ? 'true' : 'false') . ")";
$bodyInit = 'init()';
$jsFile   = 'encuesta_usados.js';

ob_start();
include __DIR__ . '/views/components/header.php';
?>
  <main class="max-w-[1800px] mx-auto px-6 py-5 space-y-5">
    <?php include __DIR__ . '/views/components/toolbar.php'; ?>
    <?php include __DIR__ . '/views/components/tabla.php'; ?>
  </main>
<?php
include __DIR__ . '/views/components/modal_token.php';
$content = ob_get_clean();

include __DIR__ . '/../comun/layout.php';
