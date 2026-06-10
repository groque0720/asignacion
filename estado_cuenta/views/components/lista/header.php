  <!-- ── Header ──────────────────────────────────────────────────────────── -->
  <header class="bg-slate-900 text-white shadow-lg sticky top-0 z-30">
    <div class="max-w-[1400px] mx-auto px-6 py-3 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 bg-blue-600 rounded-lg flex items-center justify-center">
          <i class="fas fa-users text-sm"></i>
        </div>
        <div>
          <h1 class="text-sm font-bold leading-tight">Clientes Activos · Estado de Cuenta</h1>
          <p class="text-slate-400 text-xs">Derka y Vargas S.A.</p>
        </div>
      </div>
      <div class="text-right">
        <p class="text-[10px] text-slate-500 uppercase tracking-widest leading-none mb-0.5">Fecha</p>
        <p class="text-sm font-semibold"><?php echo $fecha_actual; ?></p>
      </div>
    </div>
  </header>
