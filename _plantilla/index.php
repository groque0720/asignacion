<?php
/*
 * Controlador del módulo: arma la vista (componentes) dentro del layout compartido.
 * La carga de datos / filtros / paginación la maneja el componente Alpine
 * (views/js/plantilla.js) vía data.php.
 */
require __DIR__ . '/config/config_app.php';   // sesión + auth (redirect) + $con + $puedeEditar

$title        = 'Plantilla · Derka y Vargas';
$fecha_actual = date('d/m/Y');

$bodyData = "plantilla(".($puedeEditar ? 'true' : 'false').")";
$bodyInit = 'load()';
$jsFile   = 'plantilla.js';

ob_start();
include __DIR__ . '/views/components/header.php';
?>
  <main class="max-w-[1400px] mx-auto px-6 py-5 space-y-5">
    <?php include __DIR__ . '/views/components/toolbar.php'; ?>
    <?php include __DIR__ . '/views/components/tabla.php'; ?>
  </main>
<?php
$content = ob_get_clean();

include __DIR__ . '/../comun/layout.php';
