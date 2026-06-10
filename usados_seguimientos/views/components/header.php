  <!-- ── Header ──────────────────────────────────────────────────────────── -->
  <header class="bg-slate-900 text-white shadow-lg sticky top-0 z-30">
    <div class="max-w-[1800px] mx-auto px-6 py-3 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 bg-blue-600 rounded-lg flex items-center justify-center">
          <i class="fas fa-clipboard-check text-sm"></i>
        </div>
        <div>
          <h1 class="text-sm font-bold leading-tight">Seguimiento Documentación — Usados</h1>
          <p class="text-slate-400 text-xs">Derka y Vargas S.A.</p>
        </div>
      </div>
      <div class="flex items-center gap-5">
        <template x-if="esAdmin">
          <button @click="abrirAdmin()"
                  class="flex items-center gap-1.5 bg-slate-700 hover:bg-slate-600 px-3 py-1.5 rounded-md text-xs font-medium transition-colors">
            <i class="fas fa-gear"></i> Gestionar ítems
          </button>
        </template>
        <div class="w-px h-7 bg-slate-700"></div>
        <div class="text-right">
          <p class="text-[10px] text-slate-500 uppercase tracking-widest leading-none mb-0.5">Usuario</p>
          <p class="text-sm font-semibold"><?= htmlspecialchars($userName ?? '') ?></p>
        </div>
      </div>
    </div>
  </header>
