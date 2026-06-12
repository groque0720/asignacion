  <!-- ── Modal Reservar / editar reserva ─────────────────────────────────── -->
  <div x-show="modalRes.open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4"
       @keydown.escape.window="modalRes.open = false">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[92vh] flex flex-col" @click.outside="modalRes.open = false">
      <!-- Header -->
      <div class="bg-slate-50 border-b border-gray-200 px-5 py-3 flex items-center justify-between rounded-t-2xl">
        <div>
          <h3 class="font-bold text-slate-900"><i class="fas fa-hand-holding-dollar text-blue-600 mr-1.5"></i> Reservar plan</h3>
          <p class="text-xs text-slate-500" x-text="modalRes.form.titulo"></p>
        </div>
        <button @click="modalRes.open = false" class="text-slate-400 hover:text-slate-700"><i class="fas fa-xmark text-lg"></i></button>
      </div>

      <!-- Body -->
      <div class="p-5 overflow-y-auto grid grid-cols-1 md:grid-cols-2 gap-3">
        <div class="md:col-span-2">
          <label class="block text-xs font-medium text-slate-500 mb-1">Cliente</label>
          <input type="text" x-model="modalRes.form.cliente" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Sexo</label>
          <select x-model="modalRes.form.sexo" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
            <option value="">—</option><option value="M">Masculino</option><option value="F">Femenino</option><option value="X">Otro</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Fecha nacimiento</label>
          <input type="date" x-model="modalRes.form.fecha_nacimiento" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Edad</label>
          <input type="text" x-model="modalRes.form.edad" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">DNI</label>
          <input type="text" x-model="modalRes.form.dni" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">CUIL</label>
          <input type="text" x-model="modalRes.form.cuil" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        <div class="md:col-span-2">
          <label class="block text-xs font-medium text-slate-500 mb-1">Dirección</label>
          <input type="text" x-model="modalRes.form.direccion" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Localidad</label>
          <input type="text" x-model="modalRes.form.localidad" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Provincia</label>
          <input type="text" x-model="modalRes.form.provincia" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Email</label>
          <input type="email" x-model="modalRes.form.email" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Celular</label>
          <input type="text" x-model="modalRes.form.celular" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
        </div>

        <div class="md:col-span-2 border-t border-gray-100 pt-3 mt-1 grid grid-cols-1 md:grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Fecha reserva</label>
            <input type="date" x-model="modalRes.form.fecha_reserva" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
          </div>
          <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Hora reserva</label>
            <input type="time" x-model="modalRes.form.hora_reserva" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
          </div>
          <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Modelo/versión a retirar</label>
            <input type="text" x-model="modalRes.form.modelo_version_retirar" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
          </div>
          <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Monto reserva</label>
            <input type="text" x-model="modalRes.form.monto_reserva" @input="fmtMoney(modalRes.form, 'monto_reserva')" inputmode="decimal" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none num text-right">
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="bg-slate-50 border-t border-gray-200 px-5 py-3 flex items-center justify-end gap-2 rounded-b-2xl">
        <button @click="modalRes.open = false" class="border border-gray-300 text-slate-600 hover:bg-gray-100 rounded-lg px-4 py-2 text-sm">Cancelar</button>
        <button @click="guardarReserva()" :disabled="modalRes.saving"
                class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-4 py-2 text-sm disabled:opacity-50">
          <span x-show="!modalRes.saving"><i class="fas fa-check mr-1"></i> Confirmar reserva</span>
          <span x-show="modalRes.saving"><i class="fas fa-spinner fa-spin mr-1"></i> Guardando…</span>
        </button>
      </div>
    </div>
  </div>
