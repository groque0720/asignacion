  <!-- ── Modal: link + QR de la encuesta ───────────────────────────────────── -->
  <div x-show="modal.open" x-cloak
       class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4"
       @keydown.escape.window="modal.open = false">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" @click.outside="modal.open = false">
      <!-- Cabecera -->
      <div class="flex items-start justify-between px-5 py-4 border-b border-gray-100">
        <div class="min-w-0">
          <h3 class="text-base font-bold text-slate-900">Link de encuesta</h3>
          <p class="text-xs text-slate-500 truncate" x-text="modal.cliente"></p>
          <p class="text-xs text-slate-400 truncate" x-text="modal.vehiculo"></p>
          <p class="text-[11px] text-slate-400 truncate">
            <span x-show="modal.asesor"><i class="fas fa-user-tie mr-1"></i><span x-text="modal.asesor"></span></span>
            <span x-show="modal.entrega"> · <i class="fas fa-calendar-day mr-1"></i><span x-text="modal.entrega"></span></span>
          </p>
        </div>
        <button @click="modal.open = false" class="text-slate-400 hover:text-slate-700">
          <i class="fas fa-xmark text-lg"></i>
        </button>
      </div>

      <div class="p-5 space-y-4">
        <!-- Cargando -->
        <div x-show="modal.loading" class="py-8 text-center text-slate-400">
          <i class="fas fa-spinner fa-spin text-2xl"></i>
          <p class="text-sm mt-2">Generando link…</p>
        </div>

        <!-- Error -->
        <div x-show="!modal.loading && modal.error"
             class="rounded-lg bg-red-50 border border-red-100 text-red-700 text-sm p-3">
          <i class="fas fa-triangle-exclamation mr-1"></i> <span x-text="modal.error"></span>
        </div>

        <!-- OK -->
        <template x-if="!modal.loading && !modal.error">
          <div class="space-y-4">
            <!-- QR -->
            <div class="flex justify-center">
              <div class="p-3 bg-white border border-gray-200 rounded-xl">
                <img :src="qrUrl(modal.link)" alt="QR de la encuesta" width="220" height="220" class="block">
              </div>
            </div>

            <!-- Link -->
            <div>
              <label class="block text-xs font-medium text-slate-500 mb-1">Enlace para el cliente</label>
              <div class="flex items-center gap-2">
                <input type="text" readonly :value="modal.link"
                       class="flex-1 text-xs border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 text-slate-600 truncate">
                <button @click="copiarLink()"
                        class="px-3 py-2 rounded-lg text-xs font-medium transition-colors"
                        :class="modal.copiado ? 'bg-emerald-600 text-white' : 'bg-slate-800 text-white hover:bg-slate-700'">
                  <i class="fas" :class="modal.copiado ? 'fa-check' : 'fa-copy'"></i>
                  <span x-text="modal.copiado ? 'Copiado' : 'Copiar'"></span>
                </button>
              </div>
            </div>

            <!-- Acciones -->
            <div class="flex items-center gap-2 pt-1">
              <a :href="modal.link" target="_blank"
                 class="flex-1 text-center px-3 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium">
                <i class="fas fa-up-right-from-square mr-1"></i> Abrir encuesta
              </a>
              <a :href="qrUrl(modal.link, 600)" target="_blank" download="qr_encuesta.png"
                 class="px-3 py-2 rounded-lg border border-gray-300 text-slate-700 hover:bg-gray-50 text-sm font-medium">
                <i class="fas fa-download mr-1"></i> QR
              </a>
            </div>

            <p class="text-[11px] text-slate-400 text-center">
              Escaneá el QR o compartí el enlace. Al responder, el link queda inutilizable.
            </p>
          </div>
        </template>
      </div>
    </div>
  </div>
