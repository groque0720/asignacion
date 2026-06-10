  <!-- ── Modal admin de ítems ──────────────────────────────────────────────── -->
  <div x-show="admin.open" x-cloak
       class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4"
       @keydown.escape.window="cerrarAdmin()">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh]"
         @click.outside="cerrarAdmin()">

      <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 bg-slate-50">
        <h3 class="text-sm font-bold text-slate-900"><i class="fas fa-gear mr-1"></i> Gestión de ítems</h3>
        <button @click="cerrarAdmin()" class="text-slate-400 hover:text-slate-700">
          <i class="fas fa-xmark text-lg"></i>
        </button>
      </div>

      <div class="p-5 overflow-y-auto space-y-5">

        <!-- Lista -->
        <div>
          <div class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Ítems configurados</div>
          <div x-show="admin.loading" class="text-sm text-slate-400"><i class="fas fa-circle-notch fa-spin"></i></div>
          <table x-show="!admin.loading && admin.items.length > 0" class="w-full text-sm">
            <thead class="text-xs text-slate-500 uppercase border-b border-gray-200">
              <tr>
                <th class="text-left py-1.5 px-2">Nombre</th>
                <th class="text-left py-1.5 px-2">Descripción</th>
                <th class="text-center py-1.5 px-2">Pos.</th>
                <th class="text-center py-1.5 px-2">Activo</th>
                <th></th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <template x-for="it in admin.items" :key="it.id_item">
                <tr>
                  <td class="py-1.5 px-2 font-medium text-slate-800" x-text="it.nombre"></td>
                  <td class="py-1.5 px-2 text-slate-500" x-text="it.descripcion || '—'"></td>
                  <td class="py-1.5 px-2 text-center text-slate-500" x-text="it.posicion"></td>
                  <td class="py-1.5 px-2 text-center">
                    <span x-show="it.activo" class="text-emerald-600">&#10003;</span>
                    <span x-show="!it.activo" class="text-red-500">&#10007;</span>
                  </td>
                  <td class="py-1.5 px-2 text-right">
                    <button @click="editarItem(it)" class="text-blue-600 hover:underline text-xs">
                      <i class="fas fa-pen"></i> Editar
                    </button>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
          <p x-show="!admin.loading && admin.items.length === 0" class="text-sm text-slate-400">No hay ítems aún.</p>
        </div>

        <!-- Formulario alta/edición -->
        <div class="border-t border-gray-200 pt-4">
          <div class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2"
               x-text="admin.form.id_item > 0 ? 'Editar ítem' : 'Agregar ítem'"></div>
          <div class="grid grid-cols-12 gap-3">
            <div class="col-span-5">
              <label class="block text-xs text-slate-500 mb-1">Nombre *</label>
              <input type="text" x-model="admin.form.nombre" maxlength="100" placeholder="Ej: Fotos exteriores"
                     class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="col-span-5">
              <label class="block text-xs text-slate-500 mb-1">Descripción</label>
              <input type="text" x-model="admin.form.descripcion" maxlength="255" placeholder="Opcional"
                     class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="col-span-2">
              <label class="block text-xs text-slate-500 mb-1">Posición</label>
              <input type="number" x-model.number="admin.form.posicion" min="1"
                     class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
            </div>
          </div>
          <label x-show="admin.form.id_item > 0" class="flex items-center gap-2 text-sm text-slate-600 mt-3">
            <input type="checkbox" x-model="admin.form.activo"> Activo (visible en la tabla)
          </label>
          <div class="flex items-center gap-2 mt-3">
            <button @click="guardarItem()" :disabled="admin.saving"
                    class="text-sm text-white bg-blue-600 hover:bg-blue-700 rounded-lg px-4 py-2 disabled:opacity-50">
              <i class="fas" :class="admin.saving ? 'fa-circle-notch fa-spin' : (admin.form.id_item > 0 ? 'fa-floppy-disk' : 'fa-plus')"></i>
              <span x-text="admin.form.id_item > 0 ? 'Guardar cambios' : 'Agregar ítem'"></span>
            </button>
            <button x-show="admin.form.id_item > 0" @click="nuevoItem()"
                    class="text-sm text-slate-600 border border-gray-300 rounded-lg px-3 py-2 hover:bg-gray-100">
              Cancelar
            </button>
          </div>
        </div>

        <p class="text-xs text-slate-400">
          &#9432; Al desactivar un ítem deja de aparecer en la tabla, pero sus datos se conservan.
        </p>
      </div>
    </div>
  </div>
