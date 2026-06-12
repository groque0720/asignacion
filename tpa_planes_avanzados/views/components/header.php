  <!-- ── Header ──────────────────────────────────────────────────────────── -->
  <header class="bg-slate-900 text-white shadow-lg sticky top-0 z-30">
    <div class="max-w-[1800px] mx-auto px-6 py-3 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 bg-blue-600 rounded-lg flex items-center justify-center">
          <i class="fas fa-table-cells-large text-sm"></i>
        </div>
        <div>
          <h1 class="text-sm font-bold leading-tight">Planes Avanzados</h1>
          <p class="text-slate-400 text-xs">Derka y Vargas S.A.</p>
        </div>
      </div>

      <div class="flex items-center gap-5">
        <?php if ($puedeEditar) { ?>
          <!-- Exportar (menú) -->
          <div class="relative" @click.outside="exportMenu = false">
            <button @click="exportMenu = !exportMenu"
                    class="flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 px-3 py-1.5 rounded-md text-xs font-medium transition-colors">
              <i class="fas fa-file-excel"></i> Exportar
              <i class="fas fa-chevron-down text-[10px]"></i>
            </button>
            <div x-show="exportMenu" x-cloak x-transition
                 class="absolute right-0 mt-2 w-64 bg-white text-slate-700 rounded-lg shadow-xl border border-gray-200 py-1 z-40">
              <a :href="exportUrl('exportar_lista')" target="_blank" class="block px-4 py-2 text-xs hover:bg-slate-50"><i class="fas fa-list w-4 mr-1.5 text-slate-400"></i> Lista (filtro actual)</a>
              <a :href="exportUrl('exportar_lista', 1)" target="_blank" class="block px-4 py-2 text-xs hover:bg-slate-50"><i class="fas fa-circle w-4 mr-1.5 text-green-500"></i> Solo Libres</a>
              <a :href="exportUrl('exportar_lista', 2)" target="_blank" class="block px-4 py-2 text-xs hover:bg-slate-50"><i class="fas fa-circle w-4 mr-1.5 text-yellow-400"></i> Solo Reservados</a>
              <a :href="exportUrl('exportar_lista', 3)" target="_blank" class="block px-4 py-2 text-xs hover:bg-slate-50"><i class="fas fa-circle w-4 mr-1.5 text-red-500"></i> Solo Vendidos</a>
              <div class="border-t border-gray-100 my-1"></div>
              <a :href="exportTodoUrl('exportar_todo')" target="_blank" class="block px-4 py-2 text-xs hover:bg-slate-50"><i class="fas fa-layer-group w-4 mr-1.5 text-purple-500"></i> Todo (todos los modelos)</a>
              <?php if ($esEFV || $puedeEditar) { ?>
                <a :href="exportTodoUrl('exportar_todo_efv')" target="_blank" class="block px-4 py-2 text-xs hover:bg-slate-50"><i class="fas fa-layer-group w-4 mr-1.5 text-indigo-500"></i> Todo · EFV</a>
              <?php } ?>
            </div>
          </div>

          <button @click="nuevoPlan()"
                  class="flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 px-3 py-1.5 rounded-md text-xs font-medium transition-colors">
            <i class="fas fa-plus"></i> Nuevo plan
          </button>
          <div class="w-px h-7 bg-slate-700"></div>
        <?php } ?>

        <a href="../planes_avanzados/index.php"
           class="flex items-center gap-1.5 bg-slate-700 hover:bg-slate-600 px-3 py-1.5 rounded-md text-xs font-medium transition-colors"
           title="Abrir la versión anterior">
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
