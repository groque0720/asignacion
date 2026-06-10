<?php
/*
 * <head> compartido por los módulos modernos (lo incluye comun/layout.php).
 * Recibe (opcional): $title, $extraHead (HTML extra: ej. <script> de Chart.js).
 *
 * El href "../comun/base.css" es relativo a la URL del controlador, que vive en
 * asignacion/<modulo>/  ->  resuelve a asignacion/comun/base.css.
 */
?>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $title ?? 'Derka y Vargas S.A.' ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../comun/base.css">
  <?php if (!empty($extraHead)) echo $extraHead; ?>
</head>
