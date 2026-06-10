        <!-- ── Datos del cliente / financiación ──────────────────────────── -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-3">
            <div class="flex justify-between border-b border-gray-100 pb-2">
              <span class="text-xs text-slate-500 font-medium">Cliente</span>
              <span class="text-sm font-semibold text-slate-900" x-text="d.cliente"></span>
            </div>
            <div class="flex justify-between border-b border-gray-100 pb-2">
              <span class="text-xs text-slate-500 font-medium">Asesor</span>
              <span class="text-sm font-semibold text-slate-900" x-text="d.asesor"></span>
            </div>
            <div class="flex justify-between border-b border-gray-100 pb-2">
              <span class="text-xs text-slate-500 font-medium">Tipo de Crédito</span>
              <span class="text-sm text-slate-700" x-text="d.credito || '—'"></span>
            </div>
            <div class="flex justify-between border-b border-gray-100 pb-2">
              <span class="text-xs text-slate-500 font-medium">Financiera</span>
              <span class="text-sm text-slate-700" x-text="d.financiera_cred || '—'"></span>
            </div>
            <div class="flex justify-between">
              <span class="text-xs text-slate-500 font-medium">Monto financiación</span>
              <span class="text-sm font-semibold text-slate-900 num">$ <span x-text="money(d.monto_cred)"></span></span>
            </div>
          </div>
        </div>

        <!-- ── KPIs montos ───────────────────────────────────────────────── -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="rounded-xl shadow-sm border border-slate-200 p-4 flex items-center gap-3 bg-gradient-to-br from-slate-50 to-white">
            <div class="w-11 h-11 rounded-xl bg-slate-200 text-slate-600 flex items-center justify-center flex-shrink-0">
              <i class="fas fa-file-invoice"></i>
            </div>
            <div class="min-w-0">
              <p class="text-xs text-slate-500 font-medium">Monto Operación</p>
              <p class="text-xl font-bold text-slate-900 num">$ <span x-text="money(d.monto_operacion)"></span></p>
            </div>
          </div>
          <div class="rounded-xl shadow-sm border border-emerald-100 p-4 flex items-center gap-3 bg-gradient-to-br from-emerald-50 to-white">
            <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
              <i class="fas fa-circle-check"></i>
            </div>
            <div class="min-w-0">
              <p class="text-xs text-slate-500 font-medium">Pagado</p>
              <p class="text-xl font-bold text-emerald-700 num">$ <span x-text="money(d.pagado)"></span></p>
            </div>
          </div>
          <div class="rounded-xl shadow-sm border p-4 flex items-center gap-3"
               :class="d.a_cancelar > 0 ? 'border-amber-100 bg-gradient-to-br from-amber-50 to-white' : 'border-emerald-100 bg-gradient-to-br from-emerald-50 to-white'">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                 :class="d.a_cancelar > 0 ? 'bg-amber-100 text-amber-600' : 'bg-emerald-100 text-emerald-600'">
              <i class="fas fa-hourglass-half"></i>
            </div>
            <div class="min-w-0">
              <p class="text-xs text-slate-500 font-medium">A cancelar</p>
              <p class="text-xl font-bold num" :class="d.a_cancelar > 0 ? 'text-amber-700' : 'text-emerald-700'">
                $ <span x-text="money(d.a_cancelar)"></span>
              </p>
            </div>
          </div>
        </div>

        <!-- Barra de progreso pagado/operación -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
          <div class="flex justify-between text-xs text-slate-500 mb-1.5">
            <span>Avance de pago</span>
            <span class="font-semibold text-slate-700" x-text="pct() + '%'"></span>
          </div>
          <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden">
            <div class="h-full rounded-full transition-all"
                 :class="pct() >= 100 ? 'bg-emerald-500' : 'bg-blue-500'"
                 :style="`width:${Math.min(pct(),100)}%`"></div>
          </div>
        </div>
