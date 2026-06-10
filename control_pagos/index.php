<?php
/*
 * Controlador del módulo Control de Pagos.
 * Arma la vista (componentes) dentro del layout. La interacción (carga de
 * filas, filtros, edición, export) la maneja el componente Alpine vía data.php
 * / guardar.php / excel.php / pdf.php.
 */
require __DIR__ . '/config/config_app.php';   // sesión + auth (redirect) + $con + $puedeEditar

$title        = 'Control de Pagos · Derka y Vargas';
$fecha_actual = date('d/m/Y');

$bodyData = "controlPagos(".($puedeEditar ? 'true' : 'false').")";
$bodyInit = 'init()';
$jsFile   = 'control_pagos.js';

ob_start();
include __DIR__ . '/views/components/header.php';
?>
  <main class="max-w-[1800px] mx-auto px-6 py-5 space-y-5">
    <?php include __DIR__ . '/views/components/kpis.php'; ?>
    <?php include __DIR__ . '/views/components/toolbar.php'; ?>
    <?php include __DIR__ . '/views/components/tabla.php'; ?>
  </main>
<?php
include __DIR__ . '/views/components/popover_estados.php';
include __DIR__ . '/views/components/modal_edicion.php';
$content = ob_get_clean();

include __DIR__ . '/../comun/layout.php';
