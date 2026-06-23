    <!-- ── Tabla ─────────────────────────────────────────────────────────── -->
    <style>
      .dv-table tbody td { font-size: 13px; }
    </style>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto" style="max-height: calc(100vh - 360px);">
        <table class="w-full text-xs table-fixed dv-table">
          <thead>
            <tr>
              <template x-for="c in columnas" :key="c.key">
                <th class="px-3 py-2.5 font-semibold whitespace-nowrap select-none"
                    :style="c.width ? ('width:' + c.width) : ''"
                    :class="(c.sortable ? 'cursor-pointer hover:text-slate-900 ' : '') + (c.cls || '')"
                    @click="c.sortable && ordenar(c.key)">
                  <div class="flex items-center gap-1"
                       :class="c.align === 'right' ? 'justify-end' : (c.align === 'center' ? 'justify-center' : 'justify-start')">
                    <span x-text="c.label"></span>
                    <i x-show="filtros.sort === c.key" class="fas text-[10px]"
                       :class="filtros.dir === 'asc' ? 'fa-arrow-up' : 'fa-arrow-down'"></i>
                  </div>
                </th>
              </template>
            </tr>
          </thead>
          <tbody>
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

            <!-- Filas reales -->
            <template x-for="r in (loading ? [] : rows)" :key="r.id_unidad">
              <tr class="transition-colors hover:bg-blue-50/40">
                <td class="px-3 py-2 whitespace-nowrap text-slate-600" x-text="fecha(r.fec_entrega)"></td>
                <td class="px-3 py-2">
                  <div class="font-medium text-slate-900 truncate" x-text="r.cliente" :title="r.cliente"></div>
                </td>
                <td class="px-3 py-2">
                  <div class="truncate text-slate-700" x-text="r.vehiculo" :title="r.vehiculo"></div>
                  <div class="text-[11px] text-slate-400">
                    <span x-show="r.anio" x-text="r.anio"></span>
                    <span x-show="r.anio && r.km"> · </span>
                    <span x-show="r.km" x-text="(r.km ? r.km.toLocaleString('es-AR') : '') + ' km'"></span>
                  </div>
                </td>
                <td class="px-3 py-2 whitespace-nowrap">
                  <span x-show="r.dominio" class="inline-block px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-mono text-[11px] uppercase" x-text="r.dominio"></span>
                  <span x-show="!r.dominio" class="text-slate-300">—</span>
                </td>
                <td class="px-3 py-2">
                  <div class="truncate text-slate-600" x-text="r.asesor" :title="r.asesor"></div>
                </td>
                <td class="px-3 py-2 whitespace-nowrap text-slate-600" x-text="sucNombreId(r.id_sucursal)"></td>
                <td class="px-3 py-2 whitespace-nowrap text-center">
                  <span class="inline-block px-2 py-0.5 rounded text-[11px] font-semibold"
                        :class="estadoBadge(r.estado).cls" x-text="estadoBadge(r.estado).label"></span>
                  <span x-show="r.estado === 2 && r.promedio !== null"
                        class="ml-1.5 font-bold text-xs" :style="'color:' + promColor(r.promedio)"
                        x-text="r.promedio !== null ? r.promedio.toFixed(1) : ''"></span>
                </td>
                <td class="px-2 py-2 whitespace-nowrap text-center">
                  <template x-if="r.estado === 2 && r.id_respuesta">
                    <a :href="'detalle.php?id=' + r.id_respuesta" target="_blank"
                       class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-md bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-xs font-medium">
                      <i class="fas fa-chart-simple"></i> Resultado
                    </a>
                  </template>
                  <template x-if="r.estado !== 2">
                    <button @click="abrirToken(r)"
                       class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-md text-xs font-medium"
                       :class="r.estado === 1 ? 'bg-blue-50 text-blue-700 hover:bg-blue-100' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'">
                      <i class="fas" :class="r.estado === 1 ? 'fa-link' : 'fa-qrcode'"></i>
                      <span x-text="r.estado === 1 ? 'Ver link' : 'Generar link'"></span>
                    </button>
                  </template>
                </td>
              </tr>
            </template>

            <tr x-show="!loading && rows.length === 0">
              <td :colspan="columnas.length" class="px-3 py-10 text-center text-slate-400">
                <i class="fas fa-inbox text-2xl mb-2 block"></i> Sin entregas para este filtro.
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
