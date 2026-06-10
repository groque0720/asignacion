    <!-- ── Tabla ─────────────────────────────────────────────────────────── -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto" style="max-height: calc(100vh - 290px);">
        <table class="w-full text-sm table-sticky">
          <thead class="text-slate-600 text-xs uppercase tracking-wide">
            <tr>
              <th class="px-3 py-2.5 text-left font-semibold">ID</th>
              <th class="px-3 py-2.5 text-left font-semibold">Nombre</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <template x-for="r in (loading ? [] : rows)" :key="r.id">
              <tr class="hover:bg-blue-50/40">
                <td class="px-3 py-2 text-slate-400" x-text="r.id"></td>
                <td class="px-3 py-2 font-medium text-slate-900" x-text="r.nombre"></td>
              </tr>
            </template>

            <!-- skeleton -->
            <template x-for="i in (loading ? 10 : 0)" :key="'sk'+i">
              <tr><template x-for="n in 2" :key="n"><td class="px-3 py-3"><div class="h-3 rounded bg-slate-200 animate-pulse w-4/5"></div></td></template></tr>
            </template>

            <tr x-show="!loading && rows.length === 0">
              <td colspan="2" class="px-3 py-10 text-center text-slate-400">
                <i class="fas fa-inbox text-2xl mb-2 block"></i> Sin registros para este filtro.
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
            <option :value="25">25</option><option :value="50">50</option><option :value="100">100</option>
          </select>
        </div>
      </div>
    </div>
