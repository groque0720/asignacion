<?php
/* Header de las páginas del configurador. Recibe $cfgTitle, $cfgIcon. */
?>
  <header class="bg-slate-900 text-white shadow-lg sticky top-0 z-30">
    <div class="max-w-[1400px] mx-auto px-6 py-3 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <a href="index.php" class="w-9 h-9 bg-slate-700 hover:bg-slate-600 rounded-lg flex items-center justify-center" title="Volver a Entregas">
          <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div class="w-9 h-9 bg-blue-600 rounded-lg flex items-center justify-center">
          <i class="fas <?= htmlspecialchars($cfgIcon ?? 'fa-sliders') ?> text-sm"></i>
        </div>
        <div>
          <h1 class="text-sm font-bold leading-tight"><?= htmlspecialchars($cfgTitle ?? 'Configurar') ?></h1>
          <p class="text-slate-400 text-xs">Encuesta de Satisfacción · 0km</p>
        </div>
      </div>
      <nav class="flex items-center gap-1 text-xs font-medium">
        <a href="encuestas.php" class="px-3 py-1.5 rounded-md hover:bg-slate-700"><i class="fas fa-clipboard-list mr-1"></i> Encuestas</a>
        <a href="areas.php"     class="px-3 py-1.5 rounded-md hover:bg-slate-700"><i class="fas fa-tags mr-1"></i> Áreas</a>
        <a href="niveles.php"   class="px-3 py-1.5 rounded-md hover:bg-slate-700"><i class="fas fa-layer-group mr-1"></i> Niveles</a>
      </nav>
    </div>
  </header>
