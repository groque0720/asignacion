<?php
/*
 * Controlador del listado de clientes activos (Estado de Cuenta).
 * Arma la vista (componentes) dentro del layout. La carga de filas / filtros /
 * paginación la maneja el componente Alpine (views/js/lista.js) vía lista_data.php.
 */
require __DIR__ . '/config/config_app.php';   // sesión + auth (redirect) + $con

$title        = 'Clientes Activos · Estado de Cuenta';
$fecha_actual = date('d/m/Y');
$idsuc        = (int)($_SESSION['idsuc'] ?? 0);

$bodyData = "lista($idsuc)";
$bodyInit = 'load()';
$jsFile   = 'lista.js';

ob_start();
include __DIR__ . '/views/components/lista/header.php';
?>
  <main class="max-w-[1400px] mx-auto px-6 py-5 space-y-5">
    <?php include __DIR__ . '/views/components/lista/toolbar.php'; ?>
    <?php include __DIR__ . '/views/components/lista/tabla.php'; ?>
  </main>
<?php
$content = ob_get_clean();

include __DIR__ . '/../comun/layout.php';
