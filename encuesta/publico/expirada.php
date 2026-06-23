<?php
/* Token inválido o ya respondido. */
$completada = (isset($_GET['tipo']) && $_GET['tipo'] === 'completada');
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $completada ? 'Encuesta ya respondida' : 'Enlace no válido' ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>body{font-family:'Inter',system-ui,sans-serif}</style>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-6 text-slate-800">
  <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8 text-center">
    <div class="w-20 h-20 rounded-full flex items-center justify-center text-4xl mx-auto mb-5"
         style="<?= $completada ? 'background:#d1fae5;color:#059669' : 'background:#fee2e2;color:#dc2626' ?>">
      <i class="fas <?= $completada ? 'fa-circle-check' : 'fa-link-slash' ?>"></i>
    </div>
    <h1 class="text-2xl font-bold text-slate-900 mb-2">
      <?= $completada ? '¡Esta encuesta ya fue respondida!' : 'Enlace no válido' ?>
    </h1>
    <p class="text-slate-600 leading-relaxed">
      <?= $completada
            ? 'Gracias, ya registramos tus respuestas. No es necesario completarla otra vez.'
            : 'El enlace no es válido o ya no está disponible. Verificá el link o contactá al concesionario.' ?>
    </p>
    <p class="text-slate-400 text-sm mt-6"><i class="fas fa-car mr-1"></i> Derka y Vargas S.A.</p>
  </div>
</body>
</html>
