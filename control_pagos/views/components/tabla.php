    <!-- ── Tabla ─────────────────────────────────────────────────────────── -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto" style="max-height: calc(100vh - 360px);" @scroll="popState.open = false">
        <table class="w-full text-xs table-fixed table-sticky">
          <thead class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wide border-b border-gray-200">
            <tr>
              <template x-for="c in columnas" :key="c.key">
                <th class="px-3 py-2.5 font-semibold whitespace-nowrap select-none"
                    :style="c.width ? ('width:' + c.width) : ''"
                    :class="(c.sortable ? 'cursor-pointer hover:text-slate-900 ' : '') + (c.cls || '')"
                    @click="c.sortable && ordenar(c.key)">
                  <div class="flex items-center gap-1"
                       :class="c.align === 'right' ? 'justify-end' : (c.align === 'center' ? 'justify-center' : 'justify-start')">
                    <i x-show="c.icon" :class="'fas ' + c.icon" :title="c.label"></i>
                    <span x-show="!c.icon" x-text="c.label"></span>
                    <i x-show="filtros.sort === c.key" class="fas text-[10px]"
                       :class="filtros.dir === 'asc' ? 'fa-arrow-up' : 'fa-arrow-down'"></i>
                  </div>
                </th>
              </template>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <!-- Skeleton mientras carga -->
            <template x-for="i in (loading ? 12 : 0)" :key="'sk'+i">
              <tr>
                <template x-for="c in columnas" :key="'skc'+i+'-'+c.key">
                  <td class="px-3 py-3">
                    <div class="h-3 rounded bg-slate-200 animate-pulse"
                         :class="c.align === 'right' ? 'ml-auto w-16' : 'w-4/5'"></div>
                  </td>
                </template>
              </tr>
            </template>

            <!-- Filas reales (ocultas mientras carga) -->
            <template x-for="r in (loading ? [] : rows)" :key="r.idreserva">
              <tr class="transition-colors"
                  :class="r.anulada == 1 ? 'bg-red-50 hover:bg-red-100' : 'hover:bg-blue-50/40'">
                <td class="px-3 py-2 text-center text-[11px] text-slate-500" x-text="r.idreserva"></td>
                <td class="px-3 py-2 text-center text-[11px] font-medium text-slate-900" x-text="r.nrounidad"></td>
                <td class="px-3 py-2 text-center text-[11px] text-slate-500" x-text="r.interno"></td>
                <td class="px-3 py-2 text-center text-[11px] text-slate-500" x-text="r.nroorden"></td>
                <td class="px-3 py-2">
                  <div class="truncate" x-text="asesorCorto(r.asesor)" :title="r.asesor"></div>
                </td>
                <td class="px-3 py-2">
                  <div class="font-medium truncate" :class="r.anulada == 1 ? 'text-red-700 line-through' : 'text-slate-900'" x-text="r.cliente" :title="r.cliente"></div>
                  <div class="flex items-center gap-1.5">
                    <span class="text-xs text-blue-600" x-text="'(' + r.tipo_venta + ')'"></span>
                    <span x-show="r.anulada == 1"
                          class="text-[10px] font-bold uppercase tracking-wide px-1.5 py-0.5 rounded bg-red-600 text-white">Anulada</span>
                  </div>
                </td>
                <td class="px-3 py-2 text-slate-600">
                  <div class="truncate" x-text="r.modelo" :title="r.modelo"></div>
                </td>
                <td class="px-3 py-2 text-right num font-semibold"
                    :class="r.saldo == 0 ? 'text-emerald-700' : (r.saldo < 0 ? 'text-red-600' : 'text-slate-900')">
                  <span x-text="money(r.saldo)"></span>
                </td>
                <td class="px-3 py-2 whitespace-nowrap text-slate-500" x-text="fecha(r.fecres)"></td>
                <td class="px-3 py-2 whitespace-nowrap">
                  <span x-show="r.llego" x-text="fecha(r.llego)"
                        class="inline-block px-2 py-0.5 rounded text-xs font-medium"
                        :class="arriboDemorado(r.llego) ? 'bg-red-100 text-red-700 font-bold italic' : 'bg-emerald-50 text-emerald-700'"
                        :title="arriboDemorado(r.llego) ? 'Arribó hace más de 10 días' : ''"></span>
                  <span x-show="!r.llego" class="text-slate-300">—</span>
                </td>
                <td class="px-3 py-2 whitespace-nowrap">
                  <span x-show="r.fechacanc" x-text="fecha(r.fechacanc)"
                        class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-amber-50 text-amber-700"></span>
                  <span x-show="!r.fechacanc" class="text-slate-300">—</span>
                </td>
                <td class="px-2 py-2">
                  <div class="flex items-center justify-center gap-1">
                    <button @click.stop="toggleEstados(r, $event)" title="Ver estados"
                            class="w-7 h-7 rounded-md border inline-flex items-center justify-center text-slate-500 hover:text-blue-600 hover:bg-blue-50 transition"
                            :class="popState.open && popState.idreserva == r.idreserva ? 'border-blue-300 bg-blue-50 text-blue-600' : 'border-gray-200 bg-white'">
                      <i class="fas fa-ellipsis-vertical"></i>
                    </button>
                    <template x-if="puedeEditar">
                      <button @click="abrirEdicion(r)" title="Editar"
                              class="w-7 h-7 rounded-md inline-flex items-center justify-center text-slate-400 hover:text-blue-600 hover:bg-blue-50">
                        <i class="fas fa-pen-to-square"></i>
                      </button>
                    </template>
                  </div>
                </td>
              </tr>
            </template>

            <tr x-show="!loading && rows.length === 0">
              <td :colspan="columnas.length" class="px-3 py-10 text-center text-slate-400">
                <i class="fas fa-inbox text-2xl mb-2 block"></i> Sin resultados para este filtro.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- ── Paginación ──────────────────────────────────────────────────── -->
      <div class="flex items-center justify-between px-4 py-3 border-t border-gray-200 bg-slate-50 text-sm">
        <div class="text-slate-500">
          <span x-text="desde()"></span>–<span x-text="hasta()"></span> de <span x-text="total"></span>
        </div>
        <div class="flex items-center gap-1">
          <button @click="irPagina(1)" :disabled="page === 1"
                  class="px-2.5 py-1.5 rounded-lg border border-gray-300 bg-white disabled:opacity-40 hover:bg-gray-50">
            <i class="fas fa-angles-left text-xs"></i>
          </button>
          <button @click="irPagina(page - 1)" :disabled="page === 1"
                  class="px-2.5 py-1.5 rounded-lg border border-gray-300 bg-white disabled:opacity-40 hover:bg-gray-50">
            <i class="fas fa-angle-left text-xs"></i>
          </button>
          <span class="px-3 text-slate-600">Pág. <strong x-text="page"></strong> / <span x-text="pages || 1"></span></span>
          <button @click="irPagina(page + 1)" :disabled="page >= pages"
                  class="px-2.5 py-1.5 rounded-lg border border-gray-300 bg-white disabled:opacity-40 hover:bg-gray-50">
            <i class="fas fa-angle-right text-xs"></i>
          </button>
          <button @click="irPagina(pages)" :disabled="page >= pages"
                  class="px-2.5 py-1.5 rounded-lg border border-gray-300 bg-white disabled:opacity-40 hover:bg-gray-50">
            <i class="fas fa-angles-right text-xs"></i>
          </button>
          <select x-model.number="filtros.per" @change="resetLoad()"
                  class="ml-2 border border-gray-300 rounded-lg px-2 py-1.5 bg-white">
            <option :value="50">50</option>
            <option :value="100">100</option>
            <option :value="200">200</option>
          </select>
        </div>
      </div>
    </div>

    <p class="text-xs text-slate-400 text-center">
      Módulo nuevo. ¿Preferís la planilla clásica?
      <a href="../ventas/web/control_pagos_clientes.php" class="text-blue-600 hover:underline font-medium">Ir a la versión anterior</a>.
    </p>
