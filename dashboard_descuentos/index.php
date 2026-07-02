<?php
/*
 * Controlador del Dashboard · Descuentos (0km entregados): arma la vista
 * (componentes) dentro del layout compartido. Los datos / filtros / gráficos
 * los maneja el componente Alpine (views/js/dashboard_descuentos.js) vía data.php.
 */
require __DIR__ . '/config/config_app.php';   // sesión + auth (redirect) + $con

$anioIni = (int)date('Y');

$title     = 'Dashboard · Descuentos';
$bodyData  = "dashboardDescuentos($anioIni)";
$bodyInit  = 'load()';
$jsFile    = 'dashboard_descuentos.js';
$extraHead = '<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>';

ob_start();
include __DIR__ . '/views/components/header.php';
?>
  <main class="max-w-[1500px] mx-auto px-6 py-5 space-y-4">
    <?php include __DIR__ . '/views/components/filtros.php'; ?>
    <?php include __DIR__ . '/views/components/kpis.php'; ?>
    <?php include __DIR__ . '/views/components/charts.php'; ?>
    <?php include __DIR__ . '/views/components/tabla.php'; ?>
  </main>
<?php
$content = ob_get_clean();

include __DIR__ . '/../comun/layout.php';
