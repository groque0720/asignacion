  <!-- ── Modal Registrar / Editar pago ─────────────────────────────────────── -->
  <div x-show="modal.open" x-cloak
       class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4"
       @keydown.escape.window="modal.open = false">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden" @click.outside="modal.open = false">
      <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 bg-slate-50">
        <h3 class="text-sm font-bold text-slate-900" x-text="modal.form.idpago ? 'Editar pago' : 'Registrar pago'"></h3>
        <button @click="modal.open = false" class="text-slate-400 hover:text-slate-700"><i class="fas fa-xmark text-lg"></i></button>
      </div>

      <div class="p-5 grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Fecha *</label>
          <input type="date" x-model="modal.form.fecha"
                 class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Monto *</label>
          <div class="relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">$</span>
            <input type="text" inputmode="decimal" :value="modal.montoDisplay" @input="formatearMonto($event)"
                   placeholder="0,00"
                   class="w-full text-sm border border-gray-300 rounded-lg pl-7 pr-3 py-2 text-right focus:ring-2 focus:ring-blue-500 outline-none">
          </div>
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Tipo de pago *</label>
          <select x-model.number="modal.form.tipo" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
            <option value="0"></option>
            <template x-for="t in d.lookups.tipos" :key="t.id"><option :value="t.id" x-text="t.nombre"></option></template>
          </select>
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Modo de pago *</label>
          <select x-model.number="modal.form.modo" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
            <option value="0"></option>
            <template x-for="m in d.lookups.modos" :key="m.id"><option :value="m.id" x-text="m.nombre"></option></template>
          </select>
        </div>
        <div class="col-span-2" x-show="modal.form.modo == 3 || modal.form.modo == 4">
          <label class="block text-xs font-medium text-slate-500 mb-1">Financiera * <span class="text-amber-600">(requerida para Crédito/Leasing)</span></label>
          <select x-model.number="modal.form.finan" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
            <option value="0"></option>
            <template x-for="f in d.lookups.financieras" :key="f.id"><option :value="f.id" x-text="f.nombre"></option></template>
          </select>
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Nro Recibo</label>
          <input type="text" x-model="modal.form.nrorecibo" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        <div class="flex items-end">
          <p x-show="modal.form.tipo == 3" class="text-xs text-red-600"><i class="fas fa-triangle-exclamation"></i> Tipo "Cancelación": marcará la unidad como cancelada.</p>
        </div>
        <div class="col-span-2">
          <label class="block text-xs font-medium text-slate-500 mb-1">Observación</label>
          <textarea x-model="modal.form.obs" rows="3" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
        </div>
      </div>

      <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-gray-200 bg-slate-50">
        <button @click="modal.open = false" class="text-sm text-slate-600 border border-gray-300 rounded-lg px-4 py-2 hover:bg-gray-100">Cancelar</button>
        <button @click="guardar()" :disabled="modal.saving"
                class="text-sm text-white bg-blue-600 hover:bg-blue-700 rounded-lg px-4 py-2 disabled:opacity-50 flex items-center gap-2">
          <i class="fas" :class="modal.saving ? 'fa-circle-notch fa-spin' : 'fa-floppy-disk'"></i>
          <span x-text="modal.saving ? 'Guardando…' : 'Guardar'"></span>
        </button>
      </div>
    </div>
  </div>
