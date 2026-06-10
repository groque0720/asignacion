<?php
/*
 * Controlador del módulo Seguimiento Documentación Usados (moderno).
 * Arma la vista (componentes) dentro de comun/layout.php. La interacción
 * (grid, modal de celda, adjuntos, historial, admin de ítems) la maneja el
 * componente Alpine vía data.php / celda.php / guardar_celda.php / etc.
 */
require __DIR__ . '/config/config_app.php';   // sesión + auth (redirect) + $con + $puedeEditar + $esAdmin
require __DIR__ . '/funciones/consulta.php';  // catálogos us_*

$title        = 'Seguimiento Documentación — Usados · Derka y Vargas';
$fecha_actual = date('d/m/Y');

// Config inicial para el componente Alpine (catálogos desde la DB).
$cfg = [
    'puedeEditar' => (bool)$puedeEditar,
    'esAdmin'     => (bool)$esAdmin,
    'sucursales'  => us_sucursales($con),
    'estadosUsado'=> us_estados_usado($con),
    'estados'     => us_estados_lista(),
    'uploadsUrl'  => $UPLOADS_URL,
];

// La config va como objeto JS dentro de x-data="usadosSeguimientos({...})".
// htmlspecialchars(ENT_QUOTES) escapa las comillas del JSON para el atributo HTML;
// el navegador las decodifica y Alpine recibe un objeto JS válido.
$bodyData = "usadosSeguimientos(" . htmlspecialchars(json_encode($cfg, JSON_UNESCAPED_UNICODE), ENT_QUOTES) . ")";
$bodyInit = 'init()';
$jsFile   = 'usados_seguimientos.js';
$extraHead = '<link rel="stylesheet" href="css/usados_seguimientos.css">';

ob_start();
include __DIR__ . '/views/components/header.php';
?>
  <main class="max-w-[1900px] mx-auto px-6 py-5 space-y-5">
    <?php include __DIR__ . '/views/components/toolbar.php'; ?>
    <?php include __DIR__ . '/views/components/tabla.php'; ?>
  </main>
<?php
include __DIR__ . '/views/components/modal_celda.php';
if ($esAdmin) include __DIR__ . '/views/components/modal_admin.php';
$content = ob_get_clean();

include __DIR__ . '/../comun/layout.php';
