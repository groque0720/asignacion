<?php
/*
 * Layout base compartido por los módulos modernos. Lo incluye el controlador
 * del módulo (index.php / cuenta.php / …) al final, después de armar $content.
 *
 * Recibe del controlador:
 *   $title     título de la pestaña
 *   $content   HTML del cuerpo (componentes ya renderizados, vía ob_start/ob_get_clean)
 *   $bodyData  expresión x-data del componente Alpine raíz (ej: "controlPagos(true)")
 *   $bodyInit  expresión x-init (default "load()")
 *   $jsFile    archivo bajo views/js/ del módulo que define el componente Alpine
 *   $extraHead (opcional) HTML extra para el <head> (lo consume comun/head.php)
 *
 * Se incluye desde el controlador con:
 *   include __DIR__ . '/../comun/layout.php';
 */
?>
<!doctype html>
<html lang="es">
<?php include __DIR__ . '/head.php'; ?>
<body class="bg-gray-100 min-h-screen text-slate-800"
      x-data="<?= $bodyData ?>" x-init="<?= $bodyInit ?? 'load()' ?>" x-cloak>

  <?= $content ?? '' ?>

  <script src="views/js/<?= $jsFile ?>"></script>
</body>
</html>
