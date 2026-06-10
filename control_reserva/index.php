<?php
/*
 * Controlador del módulo Control de Reservas (versión moderna de
 * ventas/web/control_reservas.php). Arma la vista dentro del layout compartido.
 * La carga / búsqueda / paginación y las acciones (facturar, anular, notis) las
 * maneja el componente Alpine (views/js/control_reserva.js) vía data.php /
 * facturar.php / anular.php / noti.php.
 */
require __DIR__ . '/config/config_app.php';   // sesión + auth (redirect) + $con + $puedeControlar

$title        = 'Control de Reservas · Derka y Vargas';
$fecha_actual = date('d/m/Y');

$bodyData = "controlReserva(".($puedeControlar ? 'true' : 'false').")";
$bodyInit = 'init()';
$jsFile   = 'control_reserva.js';

ob_start();
include __DIR__ . '/views/components/header.php';
?>
  <main class="max-w-[1500px] mx-auto px-6 py-5 space-y-5">
    <?php include __DIR__ . '/views/components/toolbar.php'; ?>
    <?php include __DIR__ . '/views/components/tabla.php'; ?>
  </main>
<?php
$content = ob_get_clean();

include __DIR__ . '/../comun/layout.php';
