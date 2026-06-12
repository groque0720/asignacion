    <!-- ── Tabla ─────────────────────────────────────────────────────────── -->
    <div x-show="vista === 'tabla'" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full dv-table">
          <thead>
            <tr>
              <th class="text-left">Plan</th>
              <th class="text-center">Grupo-Orden</th>
              <th class="text-center">Cuotas</th>
              <?php if ($puedeEditar) { ?>
                <th class="text-right">Cuotas $ <sup>(*)</sup></th>
                <th class="text-right">Costo <sup>(*)</sup></th>
                <th class="text-right" style="color:#dc2626">Plus <sup>(*)</sup></th>
              <?php } ?>
              <th class="text-right">Cuota Prom.</th>
              <th class="text-right">Valor Unidad</th>
              <th class="text-right">Venta</th>
              <th class="text-right">Integración</th>
              <th class="text-right">Der. Adjud.</th>
              <th class="text-right" style="color:#dc2626">Total</th>
              <?php if ($puedeEditar) { ?>
                <th class="text-right">Reserva</th>
              <?php } ?>
              <th class="text-left">Situación <span class="font-normal normal-case">(cliente / asesor)</span></th>
            </tr>
          </thead>
          <tbody>
            <!-- Skeleton -->
            <template x-for="i in (loading ? 10 : 0)" :key="'sk'+i">
              <tr>
                <template x-for="n in colCount" :key="'skc'+i+'-'+n">
                  <td class="px-3 py-2.5"><div class="h-3 rounded bg-slate-200 animate-pulse w-4/5"></div></td>
                </template>
              </tr>
            </template>

            <!-- Filas -->
            <template x-for="p in (loading ? [] : filasFiltradas())" :key="p.uuid">
              <tr class="transition-colors hover:bg-blue-50/40" :class="p.estado_id === 1 ? 'bg-green-50/60' : ''">
                <td class="px-3 py-2 whitespace-nowrap leading-tight">
                  <div class="font-semibold text-slate-900" x-text="p.modelo + ' ' + p.version"></div>
                  <div class="text-slate-500" x-text="p.modalidad"></div>
                </td>
                <td class="px-3 py-2 text-center text-blue-600 font-medium">
                  <template x-if="puedeEditar">
                    <button @click="editarPlan(p)" class="underline hover:text-blue-800" x-text="p.grupo_orden"></button>
                  </template>
                  <template x-if="!puedeEditar"><span x-text="p.grupo_orden"></span></template>
                </td>
                <td class="px-3 py-2 text-center num" x-text="p.cuotas_pagadas_cantidad"></td>
                <?php if ($puedeEditar) { ?>
                  <td class="px-3 py-2 text-right num" x-text="money(p.cuotas_pagadas_monto)"></td>
                  <td class="px-3 py-2 text-right num" x-text="money(p.costo)"></td>
                  <td class="px-3 py-2 text-right num text-red-600" x-text="money(p.plus)"></td>
                <?php } ?>
                <td class="px-3 py-2 text-right num" x-text="money(p.cuota_promedio)"></td>
                <td class="px-3 py-2 text-right num" x-text="money(p.valor_unidad)"></td>
                <td class="px-3 py-2 text-right num relative group cursor-help">
                  <span x-text="money(p.venta)"></span>
                  <!-- Popover bonificación -->
                  <div class="absolute bottom-full right-0 mb-2 px-3 py-2 bg-slate-800 text-white text-xs rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-20 whitespace-nowrap text-left font-normal">
                    <div>Valor actual del plan: <span class="num" x-text="money(p.cuota_promedio * p.cuotas_pagadas_cantidad)"></span></div>
                    <div>Venta: <span class="num" x-text="money(p.venta)"></span></div>
                    <div>Bonificación: <span class="num" x-text="money((p.cuota_promedio * p.cuotas_pagadas_cantidad) - p.venta)"></span></div>
                  </div>
                </td>
                <td class="px-3 py-2 text-right num" x-text="money(p.integracion)"></td>
                <td class="px-3 py-2 text-right num" x-text="money(p.derecho_adjudicacion)"></td>
                <td class="px-3 py-2 text-right num font-bold text-red-600" x-text="money(p.precio_final)"></td>
                <?php if ($puedeEditar) { ?>
                  <td class="px-3 py-2 text-right num" x-text="p.monto_reserva === null ? '' : money(p.monto_reserva)"></td>
                <?php } ?>
                <td class="px-3 py-2">
                  <div class="flex items-center gap-3">
                    <span class="w-4 h-4 rounded-full flex-shrink-0" :style="'background:' + estadoColor(p.estado_id)"></span>
                    <template x-if="p.estado_id === 1">
                      <button @click="reservar(p)" class="text-green-600 font-medium hover:underline">reservar</button>
                    </template>
                    <template x-if="p.estado_id !== 1">
                      <div class="min-w-0 leading-tight">
                        <template x-if="p.usuario_venta_id === userId">
                          <button @click="reservar(p)" class="text-left truncate hover:underline block">
                            <span class="block truncate" x-text="(p.cliente || '—')"></span>
                            <span class="block text-blue-600 truncate" x-text="p.usuario_venta"></span>
                          </button>
                        </template>
                        <template x-if="p.usuario_venta_id !== userId">
                          <div class="truncate">
                            <span class="block truncate" x-text="(p.cliente || '—')"></span>
                            <span class="block text-blue-600 truncate" x-text="p.usuario_venta"></span>
                          </div>
                        </template>
                      </div>
                    </template>
                  </div>
                </td>
              </tr>
            </template>

            <tr x-show="!loading && filasFiltradas().length === 0">
              <td :colspan="colCount" class="px-3 py-10 text-center text-slate-400">
                <i class="fas fa-inbox text-2xl mb-2 block"></i> Sin planes para este filtro.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
