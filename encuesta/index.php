<?php
/*
 * Controlador del módulo Encuesta de Satisfacción · 0km.
 * Tab "Entregas": grilla de unidades 0km entregadas + generación de link/QR.
 * La interacción (carga, filtros, token) la maneja el componente Alpine vía data.php / token.php.
 */
require __DIR__ . '/config/config_app.php';   // sesión + auth + $con + $puedeEditar + $puedeConfigurar
require __DIR__ . '/funciones/consulta.php';  // helpers enc_* (enc_utf8)

$title        = 'Encuesta 0km · Derka y Vargas';
$fecha_actual = date('d/m/Y');

// Sucursales para el filtro (desde la base, no hardcodeadas).
$sucursales = [['id' => 0, 'nombre' => 'Todas']];
$rs = mysqli_query($con, "SELECT idsucursal, sucursal FROM sucursales WHERE activo = 1 ORDER BY posicion, sucursal");
while ($s = mysqli_fetch_assoc($rs)) {
    $sucursales[] = ['id' => (int)$s['idsucursal'], 'nombre' => enc_utf8($s['sucursal'])];
}
$sucursalesJson = htmlspecialchars(json_encode($sucursales, JSON_UNESCAPED_UNICODE), ENT_QUOTES);

$bodyData = "encuestaCero(" . ($puedeConfigurar ? 'true' : 'false') . ", " . $sucursalesJson . ")";
$bodyInit = 'init()';
$jsFile   = 'encuesta.js';

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
