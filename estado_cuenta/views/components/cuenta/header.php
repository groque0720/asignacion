  <!-- ── Header ──────────────────────────────────────────────────────────── -->
  <header class="bg-slate-900 text-white shadow-lg sticky top-0 z-30">
    <div class="max-w-[1100px] mx-auto px-6 py-3 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 bg-blue-600 rounded-lg flex items-center justify-center">
          <i class="fas fa-file-invoice-dollar text-sm"></i>
        </div>
        <div>
          <h1 class="text-sm font-bold leading-tight">Estado de Cuenta</h1>
          <p class="text-slate-400 text-xs">Derka y Vargas S.A.</p>
        </div>
      </div>
      <div class="flex items-center gap-4">
        <a href="index.php" class="text-xs text-slate-300 hover:text-white">
          <i class="fas fa-users mr-1"></i> Lista de clientes
        </a>
        <a href="javascript:history.back()" class="text-xs text-slate-300 hover:text-white">
          <i class="fas fa-arrow-left mr-1"></i> Volver
        </a>
        <div class="w-px h-7 bg-slate-700"></div>
        <div class="flex items-center gap-2" x-show="!error">
          <a :href="'excel.php?IDrecord=' + idcliente" target="_blank"
             class="flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 px-3 py-1.5 rounded-md text-xs font-medium transition-colors">
            <i class="fas fa-file-excel"></i> Excel
          </a>
          <a :href="'pdf.php?IDrecord=' + idcliente" target="_blank"
             class="flex items-center gap-1.5 bg-red-600 hover:bg-red-700 px-3 py-1.5 rounded-md text-xs font-medium transition-colors">
            <i class="fas fa-file-pdf"></i> PDF / Imprimir
          </a>
        </div>
        <div class="w-px h-7 bg-slate-700"></div>
        <div class="text-right">
          <p class="text-[10px] text-slate-500 uppercase tracking-widest leading-none mb-0.5">Fecha</p>
          <p class="text-sm font-semibold"><?php echo $fecha_actual; ?></p>
        </div>
      </div>
    </div>
  </header>
