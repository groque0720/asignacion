        <!-- ── Tabla de pagos ────────────────────────────────────────────── -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
          <div class="px-5 py-3 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-800">
              <i class="fas fa-receipt text-slate-400 mr-1"></i> Detalle de pagos
              <span class="text-slate-400 font-normal" x-text="'(' + d.pagos.length + ')'"></span>
            </h2>
            <button x-show="puedeEditar" @click="abrirAlta()"
                    class="flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium px-3 py-1.5 rounded-lg">
              <i class="fas fa-plus"></i> Registrar Pago
            </button>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wide border-b border-gray-200">
                <tr>
                  <th class="px-3 py-2.5 text-left font-semibold">Nro</th>
                  <th class="px-3 py-2.5 text-left font-semibold">Fecha</th>
                  <th class="px-3 py-2.5 text-left font-semibold">Tipo</th>
                  <th class="px-3 py-2.5 text-left font-semibold">Modo</th>
                  <th class="px-3 py-2.5 text-left font-semibold">Financiera</th>
                  <th class="px-3 py-2.5 text-left font-semibold">Nro Rec.</th>
                  <th class="px-3 py-2.5 text-right font-semibold">Monto</th>
                  <th class="px-3 py-2.5 text-left font-semibold">Observación</th>
                  <th x-show="puedeEditar" class="px-3 py-2.5 text-center font-semibold">Acciones</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <template x-for="p in (loading ? [] : d.pagos)" :key="p.idpago">
                  <tr class="hover:bg-blue-50/40">
                    <td class="px-3 py-2 text-slate-400" x-text="p.idpago"></td>
                    <td class="px-3 py-2 whitespace-nowrap" x-text="fecha(p.fecha)"></td>
                    <td class="px-3 py-2" x-text="p.tipo"></td>
                    <td class="px-3 py-2" x-text="p.modo"></td>
                    <td class="px-3 py-2 text-slate-600" x-text="p.financiera"></td>
                    <td class="px-3 py-2 text-slate-600" x-text="p.nrorecibo"></td>
                    <td class="px-3 py-2 text-right num font-semibold" :class="p.monto < 0 ? 'text-red-600' : 'text-slate-900'" x-text="money(p.monto)"></td>
                    <td class="px-3 py-2 text-slate-600" x-text="p.obs"></td>
                    <td x-show="puedeEditar" class="px-3 py-2 whitespace-nowrap text-center">
                      <button @click="abrirEdicion(p)" title="Editar"
                              class="w-7 h-7 rounded-md text-slate-400 hover:text-blue-600 hover:bg-blue-50">
                        <i class="fas fa-pen-to-square"></i>
                      </button>
                      <button @click="eliminar(p)" title="Eliminar"
                              class="w-7 h-7 rounded-md text-slate-400 hover:text-red-600 hover:bg-red-50">
                        <i class="fas fa-trash-can"></i>
                      </button>
                    </td>
                  </tr>
                </template>

                <tr x-show="loading">
                  <td colspan="9" class="px-3 py-8 text-center text-slate-400">
                    <i class="fas fa-circle-notch fa-spin"></i> Cargando…
                  </td>
                </tr>
                <tr x-show="!loading && d.pagos.length === 0">
                  <td colspan="9" class="px-3 py-8 text-center text-slate-400">
                    <i class="fas fa-inbox mr-1"></i> Sin pagos registrados.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
