  <!-- ── Modal Crear / editar plan (admin) ───────────────────────────────── -->
  <div x-show="modalPlan.open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4"
       @keydown.escape.window="modalPlan.open = false">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[92vh] flex flex-col" @click.outside="modalPlan.open = false">
      <!-- Header -->
      <div class="bg-slate-50 border-b border-gray-200 px-5 py-3 flex items-center justify-between rounded-t-2xl">
        <h3 class="font-bold text-slate-900">
          <i class="fas fa-table-cells-large text-blue-600 mr-1.5"></i>
          <span x-text="modalPlan.form.planUuId ? 'Editar plan' : 'Nuevo plan'"></span>
        </h3>
        <button @click="modalPlan.open = false" class="text-slate-400 hover:text-slate-700"><i class="fas fa-xmark text-lg"></i></button>
      </div>

      <!-- Body -->
      <div class="p-5 overflow-y-auto space-y-4">
        <!-- Identificación -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
          <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Versión (modelo)</label>
            <select x-model.number="modalPlan.form.version_id" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
              <option :value="0">— Seleccionar —</option>
              <template x-for="v in cat.versiones" :key="v.id"><option :value="v.id" x-text="v.label"></option></template>
            </select>
          </div>
          <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Modalidad</label>
            <select x-model.number="modalPlan.form.modalidad_id" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
              <option :value="0">— Seleccionar —</option>
              <template x-for="m in cat.modalidades" :key="m.id"><option :value="m.id" x-text="m.label"></option></template>
            </select>
          </div>
          <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Grupo-Orden</label>
            <input type="text" x-model="modalPlan.form.grupo_orden" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
          </div>
          <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Situación</label>
            <select x-model.number="modalPlan.form.situacion_id" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
              <template x-for="s in cat.situaciones" :key="s.id"><option :value="s.id" x-text="s.label"></option></template>
            </select>
          </div>
          <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Estado</label>
            <select x-model.number="modalPlan.form.estado_id" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
              <template x-for="e in cat.estados" :key="e.id"><option :value="e.id" x-text="e.label"></option></template>
            </select>
          </div>
          <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Asesor (venta)</label>
            <select x-model="modalPlan.form.usuario_venta_id" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
              <option value="">— Sin asignar —</option>
              <template x-for="a in cat.asesores" :key="a.id"><option :value="a.id" x-text="a.label"></option></template>
            </select>
          </div>
        </div>

        <!-- Cuotas + montos -->
        <div class="border-t border-gray-100 pt-3 grid grid-cols-2 md:grid-cols-4 gap-3">
          <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Cuotas pagadas (cant.)</label>
            <input type="number" x-model.number="modalPlan.form.cuotas_pagadas_cantidad" min="0" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 num focus:ring-2 focus:ring-blue-500 outline-none">
          </div>
          <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Cuotas pagadas ($)</label>
            <input type="text" x-model="modalPlan.form.cuotas_pagadas_monto" inputmode="decimal" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 num focus:ring-2 focus:ring-blue-500 outline-none">
          </div>
          <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Costo</label>
            <input type="text" x-model="modalPlan.form.costo" inputmode="decimal" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 num focus:ring-2 focus:ring-blue-500 outline-none">
          </div>
          <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Cesión</label>
            <input type="text" x-model="modalPlan.form.cesion" inputmode="decimal" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 num focus:ring-2 focus:ring-blue-500 outline-none">
          </div>
          <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Plus</label>
            <input type="text" x-model="modalPlan.form.plus" inputmode="decimal" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 num focus:ring-2 focus:ring-blue-500 outline-none">
          </div>
          <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Cuota promedio</label>
            <input type="text" x-model="modalPlan.form.cuota_promedio" inputmode="decimal" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 num focus:ring-2 focus:ring-blue-500 outline-none">
          </div>
          <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Valor unidad</label>
            <input type="text" x-model="modalPlan.form.valor_unidad" inputmode="decimal" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 num focus:ring-2 focus:ring-blue-500 outline-none">
          </div>
          <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Monto reserva</label>
            <input type="text" x-model="modalPlan.form.monto_reserva" inputmode="decimal" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 num focus:ring-2 focus:ring-blue-500 outline-none">
          </div>
        </div>

        <!-- Venta / total (auto) -->
        <div class="border-t border-gray-100 pt-3 grid grid-cols-2 md:grid-cols-4 gap-3">
          <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Venta</label>
            <input type="text" x-model="modalPlan.form.venta" @input="recalcTotal()" inputmode="decimal" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 num focus:ring-2 focus:ring-blue-500 outline-none">
          </div>
          <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Integración</label>
            <input type="text" x-model="modalPlan.form.integracion" @input="recalcTotal()" inputmode="decimal" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 num focus:ring-2 focus:ring-blue-500 outline-none">
          </div>
          <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Derecho adjudicación</label>
            <input type="text" x-model="modalPlan.form.derecho_adjudicacion" @input="recalcTotal()" inputmode="decimal" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 num focus:ring-2 focus:ring-blue-500 outline-none">
          </div>
          <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Total (precio final)</label>
            <input type="text" x-model="modalPlan.form.precio_final" inputmode="decimal" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 num font-bold text-red-600 focus:ring-2 focus:ring-blue-500 outline-none">
          </div>
        </div>

        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Observaciones</label>
          <textarea x-model="modalPlan.form.observaciones" rows="2" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
        </div>
      </div>

      <!-- Footer -->
      <div class="bg-slate-50 border-t border-gray-200 px-5 py-3 flex items-center justify-end gap-2 rounded-b-2xl">
        <button @click="modalPlan.open = false" class="border border-gray-300 text-slate-600 hover:bg-gray-100 rounded-lg px-4 py-2 text-sm">Cancelar</button>
        <button @click="guardarPlan()" :disabled="modalPlan.saving"
                class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-4 py-2 text-sm disabled:opacity-50">
          <span x-show="!modalPlan.saving"><i class="fas fa-floppy-disk mr-1"></i> Guardar</span>
          <span x-show="modalPlan.saving"><i class="fas fa-spinner fa-spin mr-1"></i> Guardando…</span>
        </button>
      </div>
    </div>
  </div>
