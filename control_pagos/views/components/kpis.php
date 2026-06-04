    <!-- ── KPIs ──────────────────────────────────────────────────────────── -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <!-- Sucursal -->
      <div class="rounded-xl shadow-sm border border-blue-100 p-4 flex items-center gap-3 bg-gradient-to-br from-blue-50 to-white">
        <div class="w-11 h-11 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">
          <i class="fas fa-store"></i>
        </div>
        <div class="min-w-0">
          <p class="text-xs text-slate-500 font-medium">Sucursal</p>
          <p class="text-lg font-bold text-slate-900 truncate" x-text="sucNombre()"></p>
        </div>
      </div>
      <!-- Estado -->
      <div class="rounded-xl shadow-sm border border-violet-100 p-4 flex items-center gap-3 bg-gradient-to-br from-violet-50 to-white">
        <div class="w-11 h-11 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center flex-shrink-0">
          <i class="fas fa-filter"></i>
        </div>
        <div class="min-w-0">
          <p class="text-xs text-slate-500 font-medium">Estado</p>
          <p class="text-lg font-bold text-slate-900 truncate" x-text="estNombre()"></p>
        </div>
      </div>
      <!-- Operaciones -->
      <div class="rounded-xl shadow-sm border border-amber-100 p-4 flex items-center gap-3 bg-gradient-to-br from-amber-50 to-white">
        <div class="w-11 h-11 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0">
          <i class="fas fa-list-check"></i>
        </div>
        <div class="min-w-0">
          <p class="text-xs text-slate-500 font-medium">Operaciones</p>
          <p class="text-2xl font-bold text-slate-900 num" x-text="total"></p>
        </div>
      </div>
      <!-- Saldo total -->
      <div class="rounded-xl shadow-sm border border-emerald-100 p-4 flex items-center gap-3 bg-gradient-to-br from-emerald-50 to-white">
        <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
          <i class="fas fa-sack-dollar"></i>
        </div>
        <div class="min-w-0">
          <p class="text-xs text-slate-500 font-medium">Saldo total (filtro)</p>
          <p class="text-2xl font-bold num truncate" :class="saldoTotal < 0 ? 'text-red-600' : 'text-emerald-700'">
            $ <span x-text="money(saldoTotal)"></span>
          </p>
        </div>
      </div>
    </div>
