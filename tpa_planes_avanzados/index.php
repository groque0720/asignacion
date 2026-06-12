<?php
/*
 * Controlador del módulo TPA Planes Avanzados.
 * Arma la vista (componentes) dentro del layout compartido. La interacción
 * (carga, filtros, reserva, edición, export) la maneja el componente Alpine
 * (views/js/tpa_planes_avanzados.js) vía data.php / opciones.php / reservar.php /
 * guardar.php / exportar_*.php.
 */
require __DIR__ . '/config/config_app.php';   // sesión + auth (redirect) + $con + $userId + $puedeEditar + $esEFV

$title        = 'Planes Avanzados · Derka y Vargas';
$fecha_actual = date('d/m/Y');

$bodyData = "tpaPlanes(" . ($puedeEditar ? 'true' : 'false') . ", " . ($esEFV ? 'true' : 'false') . ", " . (int)$userId . ")";
$bodyInit = 'init()';
$jsFile   = 'tpa_planes_avanzados.js';

ob_start();
include __DIR__ . '/views/components/header.php';
?>
  <main class="max-w-[1800px] mx-auto px-6 py-5 space-y-5">
    <?php include __DIR__ . '/views/components/toolbar.php'; ?>
    <?php include __DIR__ . '/views/components/leyenda.php'; ?>
    <?php include __DIR__ . '/views/components/tabla.php'; ?>
    <?php include __DIR__ . '/views/components/cards.php'; ?>
  </main>
<?php
include __DIR__ . '/views/components/modal_reservar.php';
if ($puedeEditar) include __DIR__ . '/views/components/modal_plan.php';
$content = ob_get_clean();

include __DIR__ . '/../comun/layout.php';
