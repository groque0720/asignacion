    <!-- ── Tabla ─────────────────────────────────────────────────────────── -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto" style="max-height: calc(100vh - 290px);">
        <table class="w-full text-sm table-sticky">
          <thead class="text-slate-600 text-xs uppercase tracking-wide">
            <tr>
              <th class="px-3 py-2.5 text-left font-semibold">Nro</th>
              <th class="px-3 py-2.5 text-left font-semibold">Unidad</th>
              <th class="px-3 py-2.5 text-left font-semibold">Asesor</th>
              <th class="px-3 py-2.5 text-left font-semibold">Cliente</th>
              <th class="px-3 py-2.5 text-center font-semibold">Fecha</th>
              <th class="px-3 py-2.5 text-left font-semibold">Modelo</th>
              <th class="px-3 py-2.5 text-center font-semibold">Estado</th>
              <th class="px-3 py-2.5 text-center font-semibold">Pago</th>
              <th class="px-3 py-2.5 text-center font-semibold">Control</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <template x-for="r in (loading ? [] : rows)" :key="r.idreserva">
              <tr class="hover:bg-blue-50/40" :class="r.anulada ? 'bg-red-50/60 text-slate-400 line-through' : ''">
                <td class="px-3 py-2 text-slate-400" x-text="r.idreserva"></td>
                <td class="px-3 py-2 text-slate-600" x-text="r.compra"></td>
                <td class="px-3 py-2" x-text="r.asesor"></td>
                <td class="px-3 py-2 font-medium" :class="r.anulada ? '' : 'text-slate-900'" x-text="r.cliente"></td>
                <td class="px-3 py-2 text-center whitespace-nowrap" x-text="fecha(r.fecres)"></td>
                <td class="px-3 py-2 text-slate-600" x-text="r.modelo"></td>

                <!-- Estado de envío -->
                <td class="px-3 py-2 text-center">
                  <span class="inline-flex items-center justify-center w-6 h-6 rounded-md text-xs"
                        :title="estado(r).t" :style="`background:${estado(r).bg};color:${estado(r).fg}`">
                    <i :class="estado(r).icon"></i>
                  </span>
                </td>

                <!-- Estado de pago (link a estado de cuenta) -->
                <td class="px-3 py-2 text-center">
                  <a :href="pagoUrl(r)" :title="'Ver pagos · ' + pago(r).t"
                     class="inline-block px-2 py-0.5 rounded text-xs font-medium hover:ring-2 hover:ring-offset-1 hover:ring-blue-300"
                     :style="`background:${pago(r).bg};color:${pago(r).fg}`" x-text="pago(r).t"></a>
                </td>

                <!-- Control: editar + (facturar / anular sólo habilitados) -->
                <td class="px-3 py-2 whitespace-nowrap text-center">
                  <div class="inline-flex items-center gap-1">
                    <a :href="editarUrl(r)" title="Editar / ver reserva"
                       class="w-7 h-7 inline-flex items-center justify-center rounded-md text-slate-400 hover:text-blue-600 hover:bg-blue-50">
                      <i class="fas fa-pen-to-square"></i>
                    </a>

                    <template x-if="puedeControlar && !r.anulada">
                      <span class="inline-flex items-center gap-1">
                        <button @click="facturar(r)" :title="'Facturar · ' + factura(r).t"
                                class="w-7 h-7 inline-flex items-center justify-center rounded-md hover:bg-slate-100"
                                :style="`color:${factura(r).fg}`">
                          <i class="fas fa-cash-register"></i>
                        </button>
                        <button @click="anular(r)" title="Anular reserva"
                                class="w-7 h-7 inline-flex items-center justify-center rounded-md text-slate-400 hover:text-red-600 hover:bg-red-50">
                          <i class="fas fa-ban"></i>
                        </button>
                      </span>
                    </template>

                    <span x-show="r.anulada" class="text-[10px] text-red-500 font-semibold uppercase no-underline">Anulada</span>
                  </div>
                </td>
              </tr>
            </template>

            <!-- skeleton -->
            <template x-for="i in (loading ? 10 : 0)" :key="'sk'+i">
              <tr><template x-for="n in 9" :key="n"><td class="px-3 py-3"><div class="h-3 rounded bg-slate-200 animate-pulse w-4/5"></div></td></template></tr>
            </template>

            <tr x-show="!loading && rows.length === 0">
              <td colspan="9" class="px-3 py-10 text-center text-slate-400">
                <i class="fas fa-inbox text-2xl mb-2 block"></i> Sin reservas para este filtro.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Paginación -->
      <div class="flex items-center justify-between px-4 py-3 border-t border-gray-200 bg-slate-50 text-sm">
        <div class="text-slate-500"><span x-text="desde()"></span>–<span x-text="hasta()"></span> de <span x-text="total"></span></div>
        <div class="flex items-center gap-1">
          <button @click="irPagina(1)" :disabled="page===1" class="px-2.5 py-1.5 rounded-lg border border-gray-300 bg-white disabled:opacity-40 hover:bg-gray-50"><i class="fas fa-angles-left text-xs"></i></button>
          <button @click="irPagina(page-1)" :disabled="page===1" class="px-2.5 py-1.5 rounded-lg border border-gray-300 bg-white disabled:opacity-40 hover:bg-gray-50"><i class="fas fa-angle-left text-xs"></i></button>
          <span class="px-3 text-slate-600">Pág. <strong x-text="page"></strong> / <span x-text="pages||1"></span></span>
          <button @click="irPagina(page+1)" :disabled="page>=pages" class="px-2.5 py-1.5 rounded-lg border border-gray-300 bg-white disabled:opacity-40 hover:bg-gray-50"><i class="fas fa-angle-right text-xs"></i></button>
          <button @click="irPagina(pages)" :disabled="page>=pages" class="px-2.5 py-1.5 rounded-lg border border-gray-300 bg-white disabled:opacity-40 hover:bg-gray-50"><i class="fas fa-angles-right text-xs"></i></button>
          <select x-model.number="filtros.per" @change="resetLoad()" class="ml-2 border border-gray-300 rounded-lg px-2 py-1.5 bg-white">
            <option :value="20">20</option><option :value="50">50</option><option :value="100">100</option>
          </select>
        </div>
      </div>
    </div>

    <p class="text-xs text-slate-400 text-center">
      Módulo nuevo. ¿Preferís la pantalla clásica?
      <a href="../ventas/web/control_reservas.php" class="text-blue-600 hover:underline font-medium">Ir a la versión anterior</a>.
    </p>
