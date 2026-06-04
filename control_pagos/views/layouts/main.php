<?php
/*
 * Layout del módulo Control de Pagos.
 * Recibe del controlador (index.php): $title, $content, $puedeEditar.
 * El x-data del <body> es la raíz Alpine del módulo; el componente se define
 * en views/js/control_pagos.js (cargado al final, antes del Alpine diferido).
 */
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $title ?? 'Control de Pagos · Derka y Vargas' ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="views/css/main.css">
</head>
<body class="bg-gray-100 min-h-screen text-slate-800"
      x-data="controlPagos(<?php echo !empty($puedeEditar) ? 'true' : 'false'; ?>)" x-init="init()" x-cloak>

  <?= $content ?? '' ?>

  <script src="views/js/control_pagos.js"></script>
</body>
</html>
