    <!-- ── Toolbar de filtros ────────────────────────────────────────────── -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
      <div class="flex flex-wrap items-end gap-4">
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Sucursal</label>
          <select x-model.number="filtros.sucursal" @change="load()"
                  class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
            <template x-for="s in sucursales" :key="s.id">
              <option :value="s.id" x-text="s.nombre"></option>
            </template>
          </select>
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Estado del usado</label>
          <select x-model.number="filtros.estado_usado" @change="load()"
                  class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
            <template x-for="e in estadosUsado" :key="e.id">
              <option :value="e.id" x-text="e.nombre"></option>
            </template>
          </select>
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Estado doc.</label>
          <select x-model="filtros.estado" @change="load()"
                  class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
            <option value="">Todos</option>
            <option value="0">○ Pendiente</option>
            <option value="3">◑ En proceso</option>
            <option value="1">✓ Completo</option>
          </select>
        </div>
        <div class="flex-1 min-w-[200px]">
          <label class="block text-xs font-medium text-slate-500 mb-1">Buscar (interno, vehículo, dominio, asesor)</label>
          <div class="relative">
            <i class="fas fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input type="text" x-model="busqueda" placeholder="Filtrar en pantalla…"
                   class="w-full text-sm border border-gray-300 rounded-lg pl-9 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
          </div>
        </div>
        <div class="text-sm text-slate-500 pb-2">
          <span x-text="usadosFiltrados().length"></span> usado<span x-show="usadosFiltrados().length !== 1">s</span>
        </div>
        <button @click="resetFiltros()"
                class="text-sm text-slate-600 hover:text-slate-900 border border-gray-300 rounded-lg px-3 py-2 hover:bg-gray-50">
          <i class="fas fa-rotate-left mr-1"></i> Limpiar
        </button>
      </div>
    </div>
