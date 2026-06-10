    <!-- ── Toolbar ───────────────────────────────────────────────────────── -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
      <div class="flex flex-wrap items-end gap-4">
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Sucursal</label>
          <select x-model="filtros.suc" @change="resetLoad()"
                  class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
            <template x-for="s in sucursales" :key="s.id"><option :value="s.id" x-text="s.nombre"></option></template>
          </select>
        </div>
        <div class="flex-1 min-w-[240px]">
          <label class="block text-xs font-medium text-slate-500 mb-1">Buscar</label>
          <div class="relative">
            <i class="fas fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input type="text" x-model="filtros.q" @input.debounce.400ms="resetLoad()"
                   placeholder="Cliente, documento, asesor, unidad, N.R., interno…"
                   class="w-full text-sm border border-gray-300 rounded-lg pl-9 pr-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
          </div>
        </div>
        <div class="text-sm text-slate-500">
          <span class="font-semibold text-slate-700" x-text="total"></span> clientes
        </div>
      </div>
    </div>
