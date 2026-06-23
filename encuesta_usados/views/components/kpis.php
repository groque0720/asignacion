    <!-- ── KPIs ──────────────────────────────────────────────────────────── -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <!-- Total entregas -->
      <div class="rounded-xl shadow-sm border border-blue-100 p-4 flex items-center gap-3 bg-gradient-to-br from-blue-50 to-white">
        <div class="w-11 h-11 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">
          <i class="fas fa-truck-ramp-box"></i>
        </div>
        <div class="min-w-0">
          <p class="text-xs text-slate-500 font-medium">Entregas <span x-text="'(' + sucNombre() + ')'"></span></p>
          <p class="text-2xl font-bold text-slate-900 num" x-text="kpis.total"></p>
        </div>
      </div>
      <!-- Pendientes -->
      <div class="rounded-xl shadow-sm border border-amber-100 p-4 flex items-center gap-3 bg-gradient-to-br from-amber-50 to-white">
        <div class="w-11 h-11 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0">
          <i class="fas fa-hourglass-half"></i>
        </div>
        <div class="min-w-0">
          <p class="text-xs text-slate-500 font-medium">Pendientes de respuesta</p>
          <p class="text-2xl font-bold text-slate-900 num" x-text="kpis.pendientes"></p>
        </div>
      </div>
      <!-- Completadas -->
      <div class="rounded-xl shadow-sm border border-emerald-100 p-4 flex items-center gap-3 bg-gradient-to-br from-emerald-50 to-white">
        <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
          <i class="fas fa-circle-check"></i>
        </div>
        <div class="min-w-0">
          <p class="text-xs text-slate-500 font-medium">Encuestas completadas</p>
          <p class="text-2xl font-bold text-slate-900 num" x-text="kpis.completadas"></p>
        </div>
      </div>
      <!-- Promedio general -->
      <div class="rounded-xl shadow-sm border border-violet-100 p-4 flex items-center gap-3 bg-gradient-to-br from-violet-50 to-white">
        <div class="w-11 h-11 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center flex-shrink-0">
          <i class="fas fa-star-half-stroke"></i>
        </div>
        <div class="min-w-0">
          <p class="text-xs text-slate-500 font-medium">Promedio general</p>
          <p class="text-2xl font-bold num" :class="kpis.prom === null ? 'text-slate-300' : 'text-violet-700'"
             x-text="kpis.prom === null ? '—' : kpis.prom.toFixed(2)"></p>
        </div>
      </div>
    </div>
