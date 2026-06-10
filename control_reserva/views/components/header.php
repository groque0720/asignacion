  <!-- ── Header ──────────────────────────────────────────────────────────── -->
  <header class="bg-slate-900 text-white shadow-lg sticky top-0 z-30">
    <div class="max-w-[1500px] mx-auto px-6 py-3 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 bg-blue-600 rounded-lg flex items-center justify-center">
          <i class="fas fa-clipboard-check text-sm"></i>
        </div>
        <div>
          <h1 class="text-sm font-bold leading-tight">Control de Reservas</h1>
          <p class="text-slate-400 text-xs">Derka y Vargas S.A.</p>
        </div>
      </div>
      <div class="flex items-center gap-4">
        <a href="../ventas/web/noticias.php" target="_blank"
           class="flex items-center gap-1.5 bg-lime-200 hover:bg-lime-300 text-red-600 px-3 py-1.5 rounded-md text-xs font-semibold transition-colors"
           title="Ver notificaciones">
          <i class="fas fa-bell"></i> Notificaciones:
          <span class="inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 rounded-full bg-red-600 text-white text-[10px]" x-text="noti"></span>
        </a>
        <a href="../ventas/web/control_reservas.php"
           class="flex items-center gap-1.5 bg-slate-700 hover:bg-slate-600 px-3 py-1.5 rounded-md text-xs font-medium transition-colors"
           title="Abrir la pantalla clásica">
          <i class="fas fa-table-list"></i> Versión anterior
        </a>
        <div class="w-px h-7 bg-slate-700"></div>
        <div class="text-right">
          <p class="text-[10px] text-slate-500 uppercase tracking-widest leading-none mb-0.5">Fecha</p>
          <p class="text-sm font-semibold"><?php echo $fecha_actual; ?></p>
        </div>
      </div>
    </div>
  </header>
