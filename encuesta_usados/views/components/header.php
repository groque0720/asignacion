  <!-- ── Header ──────────────────────────────────────────────────────────── -->
  <header class="bg-slate-900 text-white shadow-lg sticky top-0 z-30">
    <div class="max-w-[1800px] mx-auto px-6 py-3 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 bg-blue-600 rounded-lg flex items-center justify-center">
          <i class="fas fa-car-side text-sm"></i>
        </div>
        <div>
          <h1 class="text-sm font-bold leading-tight">Encuesta de Satisfacción · Usados</h1>
          <p class="text-slate-400 text-xs">Derka y Vargas S.A.</p>
        </div>
      </div>
      <div class="flex items-center gap-5">
        <nav class="flex items-center gap-1">
          <span class="flex items-center gap-1.5 bg-blue-600 px-3 py-1.5 rounded-md text-xs font-medium">
            <i class="fas fa-truck-ramp-box"></i> Entregas
          </span>
          <?php if ($puedeConfigurar): ?>
          <a href="encuestas.php"
             class="flex items-center gap-1.5 bg-slate-700 hover:bg-slate-600 px-3 py-1.5 rounded-md text-xs font-medium transition-colors">
            <i class="fas fa-sliders"></i> Configurar
          </a>
          <?php endif; ?>
          <a href="dashboard.php"
             class="flex items-center gap-1.5 bg-slate-700 hover:bg-slate-600 px-3 py-1.5 rounded-md text-xs font-medium transition-colors">
            <i class="fas fa-chart-line"></i> Resultados
          </a>
        </nav>
        <div class="w-px h-7 bg-slate-700"></div>
        <div class="text-right">
          <p class="text-[10px] text-slate-500 uppercase tracking-widest leading-none mb-0.5">Fecha</p>
          <p class="text-sm font-semibold"><?php echo $fecha_actual; ?></p>
        </div>
      </div>
    </div>
  </header>
