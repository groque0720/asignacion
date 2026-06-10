<?php
/*
 * Controlador del detalle de Estado de Cuenta de un cliente.
 * IDrecord = idcliente (compatibilidad con el módulo viejo). También acepta ?idcliente=
 * La carga de datos y el ABM de pagos los maneja el componente Alpine
 * (views/js/cuenta.js) vía data.php / guardar.php / excel.php / pdf.php.
 */
require __DIR__ . '/config/config_app.php';   // sesión + auth (redirect) + $con + $puedeEditar

$title        = 'Estado de Cuenta · Derka y Vargas';
$fecha_actual = date('d/m/Y');
$idcliente    = (int)($_GET['IDrecord'] ?? $_GET['idcliente'] ?? 0);

$bodyData = "estadoCuenta($idcliente, ".($puedeEditar ? 'true' : 'false').")";
$bodyInit = 'load()';
$jsFile   = 'cuenta.js';

ob_start();
include __DIR__ . '/views/components/cuenta/header.php';
?>
  <main class="max-w-[1100px] mx-auto px-6 py-5 space-y-5">

    <!-- Error -->
    <div x-show="error" class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 text-sm">
      <i class="fas fa-triangle-exclamation mr-1"></i> <span x-text="error"></span>
    </div>

    <template x-if="!error">
      <div class="space-y-5">
        <?php include __DIR__ . '/views/components/cuenta/resumen.php'; ?>
        <?php include __DIR__ . '/views/components/cuenta/tabla.php'; ?>

        <p class="text-xs text-slate-400 text-center">
          Módulo nuevo. ¿Preferís la pantalla clásica?
          <a :href="'../ventas/web/pago.php?IDrecord=' + idcliente" class="text-blue-600 hover:underline font-medium">Ir a la versión anterior</a>.
        </p>
      </div>
    </template>
  </main>
<?php
include __DIR__ . '/views/components/cuenta/modal.php';
$content = ob_get_clean();

include __DIR__ . '/../comun/layout.php';
