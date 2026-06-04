  <!-- ── Modal de edición ──────────────────────────────────────────────────── -->
  <div x-show="modal.open" x-cloak
       class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4"
       @keydown.escape.window="modal.open = false">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden"
         @click.outside="modal.open = false">
      <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 bg-slate-50">
        <div>
          <h3 class="text-sm font-bold text-slate-900">Editar operación</h3>
          <p class="text-xs text-slate-500" x-text="modal.form.cliente"></p>
        </div>
        <button @click="modal.open = false" class="text-slate-400 hover:text-slate-700">
          <i class="fas fa-xmark text-lg"></i>
        </button>
      </div>

      <div class="p-5 grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Nro Unidad</label>
          <input type="number" x-model="modal.form.nrounidad"
                 class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Interno</label>
          <input type="text" x-model="modal.form.interno"
                 class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Nro Orden</label>
          <input type="text" x-model="modal.form.nroorden"
                 class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Fec. de Arribo</label>
          <input type="date" x-model="modal.form.arribo"
                 class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Fec. Est. Cancelación</label>
          <input type="date" x-model="modal.form.cancela"
                 class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Fecha de Entrega</label>
          <input type="date" x-model="modal.form.entrega"
                 class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
        </div>
        <div class="col-span-2">
          <label class="block text-xs font-medium text-slate-500 mb-1">Observación</label>
          <textarea x-model="modal.form.obs" rows="3"
                    class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"></textarea>
        </div>
      </div>

      <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-gray-200 bg-slate-50">
        <button @click="modal.open = false"
                class="text-sm text-slate-600 border border-gray-300 rounded-lg px-4 py-2 hover:bg-gray-100">
          Cancelar
        </button>
        <button @click="guardar()" :disabled="modal.saving"
                class="text-sm text-white bg-blue-600 hover:bg-blue-700 rounded-lg px-4 py-2 disabled:opacity-50 flex items-center gap-2">
          <i class="fas" :class="modal.saving ? 'fa-circle-notch fa-spin' : 'fa-floppy-disk'"></i>
          <span x-text="modal.saving ? 'Guardando…' : 'Guardar'"></span>
        </button>
      </div>
    </div>
  </div>
