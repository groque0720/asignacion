    <!-- ── Toolbar de filtros ────────────────────────────────────────────── -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 space-y-3">
      <!-- Fila 1: situación · vista · buscador -->
      <div class="flex flex-wrap items-center gap-3">
        <!-- Tabs situación -->
        <div class="inline-flex rounded-lg border border-gray-300 overflow-hidden">
          <button @click="setSituacion(1)"
                  :class="filtros.situacion === 1 ? 'bg-slate-700 text-white' : 'bg-white text-slate-500 hover:bg-gray-50'"
                  class="px-5 py-2 text-sm font-medium transition-colors">Avanzados</button>
          <button @click="setSituacion(2)"
                  :class="filtros.situacion === 2 ? 'bg-slate-700 text-white' : 'bg-white text-slate-500 hover:bg-gray-50'"
                  class="px-5 py-2 text-sm font-medium border-l border-gray-300 transition-colors">Adjudicados</button>
        </div>

        <!-- Filtro estado -->
        <select x-model.number="filtros.estado" @change="load()"
                class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
          <option :value="0">Todos los estados</option>
          <option :value="1">Libres</option>
          <option :value="2">Reservados</option>
          <option :value="3">Vendidos</option>
        </select>

        <div class="flex-1 min-w-[200px]">
          <div class="relative">
            <i class="fas fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input type="text" x-model="filtros.q"
                   placeholder="Buscar grupo-orden, cliente, versión…"
                   class="w-full text-sm border border-gray-300 rounded-lg pl-9 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
          </div>
        </div>

        <!-- Toggle vista -->
        <div class="inline-flex rounded-lg border border-gray-300 overflow-hidden">
          <button @click="vista = 'tabla'" :class="vista === 'tabla' ? 'bg-blue-600 text-white' : 'bg-white text-slate-500 hover:bg-gray-50'"
                  class="px-3 py-2 text-sm" title="Vista tabla"><i class="fas fa-table-list"></i></button>
          <button @click="vista = 'cards'" :class="vista === 'cards' ? 'bg-blue-600 text-white' : 'bg-white text-slate-500 hover:bg-gray-50'"
                  class="px-3 py-2 text-sm border-l border-gray-300" title="Vista tarjetas"><i class="fas fa-table-cells-large"></i></button>
        </div>
      </div>

      <!-- Fila 2: botonera de modelos -->
      <div class="flex flex-wrap gap-2">
        <template x-for="m in modelosActivos" :key="m.id">
          <button @click="setModelo(m.id)"
                  :class="filtros.modelo === m.id ? 'bg-blue-500 text-white' : 'bg-blue-50 text-blue-500 hover:bg-blue-100'"
                  class="px-4 py-1.5 rounded text-sm font-medium transition-colors" x-text="m.modelo"></button>
        </template>
      </div>
    </div>
