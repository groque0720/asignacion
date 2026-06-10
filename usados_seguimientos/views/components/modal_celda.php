  <!-- ── Modal de celda ────────────────────────────────────────────────────── -->
  <div x-show="modal.open" x-cloak
       class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4"
       @keydown.escape.window="lightbox.open ? (lightbox.open = false) : cerrarCelda()">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden flex flex-col max-h-[90vh]"
         @click.outside="cerrarCelda()">

      <!-- Encabezado -->
      <div class="flex items-start justify-between px-5 py-4 border-b border-gray-200 bg-slate-50">
        <div>
          <h3 class="text-sm font-bold text-slate-900" x-text="modal.titulo"></h3>
          <p class="text-xs text-slate-500" x-text="modal.subtitulo"></p>
        </div>
        <button @click="cerrarCelda()" class="text-slate-400 hover:text-slate-700">
          <i class="fas fa-xmark text-lg"></i>
        </button>
      </div>

      <!-- Cargando -->
      <div x-show="modal.loading" class="p-10 text-center text-slate-400">
        <i class="fas fa-circle-notch fa-spin text-xl"></i>
      </div>

      <!-- ── Formulario (2 columnas: controles | galería) ──────────────────── -->
      <div x-show="!modal.loading && !modal.vistaHistorial"
           class="p-5 grid grid-cols-1 sm:grid-cols-5 gap-5 overflow-y-auto">

        <!-- Columna izquierda: controles -->
        <div class="sm:col-span-3 space-y-4">
          <p x-show="modal.meta" class="text-xs text-slate-400" x-text="modal.meta"></p>

          <!-- Estado -->
          <div>
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Estado</div>
            <div class="grid grid-cols-4 gap-2">
              <template x-for="e in estados" :key="e.estado">
                <button type="button"
                        @click="puedeEditar && (modal.estado = e.estado)"
                        :disabled="!puedeEditar"
                        class="us-estado-opcion" :class="e.class + (modal.estado === e.estado ? ' activo' : '')">
                  <span class="us-estado-icon" x-text="e.icon"></span>
                  <span class="us-estado-texto" x-text="e.label"></span>
                </button>
              </template>
            </div>
          </div>

          <!-- Observación -->
          <div>
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Observación</div>
            <textarea x-model="modal.observacion" rows="3" :disabled="!puedeEditar"
                      placeholder="Opcional…"
                      class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none disabled:bg-gray-100"></textarea>
          </div>

          <!-- Subir -->
          <template x-if="puedeEditar">
            <label class="block border-2 border-dashed border-gray-300 rounded-lg px-3 py-3 text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50/40 transition">
              <i class="fas fa-upload text-slate-400"></i>
              <span class="text-sm text-slate-600 ml-1">Subir archivo(s)</span>
              <span class="block text-xs text-slate-400 mt-0.5">PDF, JPG, PNG — varios — máx. 5 MB c/u</span>
              <span x-show="modal.files.length > 0" class="block text-xs text-blue-600 mt-1"
                    x-text="modal.files.length + ' archivo(s) seleccionado(s)'"></span>
              <input type="file" multiple accept=".pdf,.jpg,.jpeg,.png,.gif,.webp" class="hidden"
                     @change="modal.files = Array.from($event.target.files)">
            </label>
          </template>
        </div>

        <!-- Columna derecha: galería de adjuntos -->
        <div class="sm:col-span-2 flex flex-col min-h-0">
          <div class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">
            Archivos <span x-show="modal.adjuntos.length > 0" x-text="'(' + modal.adjuntos.length + ')'"></span>
          </div>

          <div x-show="modal.adjuntos.length === 0" class="text-sm text-slate-400 flex-1 flex items-center justify-center border border-dashed border-gray-200 rounded-lg py-8">
            Sin archivos adjuntos
          </div>

          <div x-show="modal.adjuntos.length > 0"
               class="grid grid-cols-2 gap-3 overflow-y-auto pr-1" style="max-height: 52vh;">
            <template x-for="a in modal.adjuntos" :key="a.tipo + '-' + a.id">
              <div class="group relative border border-gray-200 rounded-lg overflow-hidden bg-slate-50">
                <!-- Miniatura (imagen) o ícono (otros) -->
                <template x-if="esImagen(a.nombre)">
                  <img :src="a.url" :alt="a.nombre" @click="abrirImagen(a)" loading="lazy"
                       class="w-full h-24 object-cover cursor-pointer hover:opacity-90 transition" title="Ver imagen">
                </template>
                <template x-if="!esImagen(a.nombre)">
                  <a :href="a.url" target="_blank"
                     class="w-full h-24 bg-white text-slate-300 flex items-center justify-center hover:text-blue-600" title="Abrir archivo">
                    <i class="fas text-3xl" :class="iconArchivo(a.nombre)"></i>
                  </a>
                </template>

                <!-- Acciones (esquina superior) -->
                <div class="absolute top-1 right-1 flex gap-1">
                  <a :href="a.url" :download="a.nombre"
                     class="w-6 h-6 rounded-full bg-white/90 hover:bg-white text-slate-600 hover:text-blue-600 inline-flex items-center justify-center shadow text-xs" title="Descargar">
                    <i class="fas fa-download"></i>
                  </a>
                  <button x-show="puedeEditar" @click="eliminarArchivo(a)"
                          class="w-6 h-6 rounded-full bg-white/90 hover:bg-white text-slate-600 hover:text-red-600 inline-flex items-center justify-center shadow text-xs" title="Eliminar">
                    <i class="fas fa-xmark"></i>
                  </button>
                </div>

                <!-- Nombre -->
                <div class="px-2 py-1 bg-white border-t border-gray-100">
                  <p class="text-[11px] text-slate-600 truncate" :title="a.nombre" x-text="a.nombre"></p>
                </div>
              </div>
            </template>
          </div>
        </div>
      </div>

      <!-- ── Vista historial ─────────────────────────────────────────────── -->
      <div x-show="!modal.loading && modal.vistaHistorial" class="p-5 overflow-y-auto">
        <div class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-3">Historial de cambios</div>
        <div x-show="modal.historial.length === 0" class="text-sm text-slate-400">Sin historial registrado.</div>
        <div class="space-y-2">
          <template x-for="(h, idx) in modal.historial" :key="idx">
            <div class="border border-gray-100 rounded-lg p-3 text-sm">
              <div class="flex items-center justify-between mb-1">
                <strong class="text-slate-700" x-text="h.usuario"></strong>
                <span class="text-xs text-slate-400" x-text="h.fecha"></span>
              </div>
              <div class="flex items-center gap-2 flex-wrap">
                <template x-if="h.estado_ant">
                  <span class="us-badge-estado" :class="h.estado_ant.class">
                    <span x-text="h.estado_ant.icon"></span> <span x-text="h.estado_ant.label"></span>
                  </span>
                </template>
                <i x-show="h.estado_ant" class="fas fa-arrow-right text-slate-300 text-xs"></i>
                <span class="us-badge-estado" :class="h.estado_nuevo.class" x-show="h.estado_nuevo">
                  <span x-text="h.estado_nuevo?.icon"></span> <span x-text="h.estado_nuevo?.label"></span>
                </span>
              </div>
              <div x-show="h.observacion" class="text-xs text-slate-500 italic mt-1" x-text="'&quot;' + h.observacion + '&quot;'"></div>
              <a x-show="h.archivo" :href="h.archivo?.url" target="_blank"
                 class="text-xs text-blue-600 hover:underline mt-1 inline-block"><i class="fas fa-paperclip text-slate-400 mr-1"></i><span x-text="h.archivo?.nombre"></span></a>
            </div>
          </template>
        </div>
      </div>

      <!-- Acciones -->
      <div class="flex items-center justify-between gap-2 px-5 py-4 border-t border-gray-200 bg-slate-50">
        <button @click="modal.vistaHistorial ? (modal.vistaHistorial = false) : verHistorial()"
                class="text-sm text-slate-600 border border-gray-300 rounded-lg px-3 py-2 hover:bg-gray-100">
          <i class="fas" :class="modal.vistaHistorial ? 'fa-arrow-left' : 'fa-clock-rotate-left'"></i>
          <span x-text="modal.vistaHistorial ? 'Volver' : 'Historial'"></span>
        </button>
        <button x-show="puedeEditar && !modal.vistaHistorial" @click="guardarCelda()" :disabled="modal.saving"
                class="text-sm text-white bg-blue-600 hover:bg-blue-700 rounded-lg px-4 py-2 disabled:opacity-50 flex items-center gap-2">
          <i class="fas" :class="modal.saving ? 'fa-circle-notch fa-spin' : 'fa-floppy-disk'"></i>
          <span x-text="modal.saving ? 'Guardando…' : 'Guardar'"></span>
        </button>
      </div>
    </div>
  </div>

  <!-- ── Lightbox / carrusel de imágenes ─────────────────────────────────── -->
  <div x-show="lightbox.open" x-cloak
       class="fixed inset-0 z-[60] flex items-center justify-center bg-black/80 p-4"
       @click.stop="lightbox.open = false"
       @keydown.left.window="lightbox.open && lbPrev()"
       @keydown.right.window="lightbox.open && lbNext()">

    <!-- Flecha anterior -->
    <button x-show="lightbox.imagenes.length > 1" @click.stop="lbPrev()"
            class="absolute left-3 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white text-slate-800 rounded-full w-11 h-11 inline-flex items-center justify-center shadow-lg z-10"
            title="Anterior (←)">
      <i class="fas fa-chevron-left"></i>
    </button>

    <div class="relative max-w-full max-h-full" @click.stop>
      <img :src="lbActual().url" :alt="lbActual().nombre"
           class="max-w-full max-h-[85vh] rounded-lg shadow-2xl object-contain bg-white">
      <div class="absolute top-2 right-2 flex gap-2">
        <a :href="lbActual().url" :download="lbActual().nombre"
           class="bg-white/90 hover:bg-white text-slate-800 rounded-full w-9 h-9 inline-flex items-center justify-center shadow-lg" title="Descargar">
          <i class="fas fa-download"></i>
        </a>
        <button @click="lightbox.open = false"
                class="bg-white/90 hover:bg-white text-slate-800 rounded-full w-9 h-9 inline-flex items-center justify-center shadow-lg" title="Cerrar">
          <i class="fas fa-xmark"></i>
        </button>
      </div>
      <p class="text-center text-white text-sm mt-3 px-4">
        <span class="truncate inline-block max-w-full align-bottom" x-text="lbActual().nombre"></span>
        <span x-show="lightbox.imagenes.length > 1" class="text-slate-400 ml-2"
              x-text="'(' + (lightbox.index + 1) + ' / ' + lightbox.imagenes.length + ')'"></span>
      </p>
    </div>

    <!-- Flecha siguiente -->
    <button x-show="lightbox.imagenes.length > 1" @click.stop="lbNext()"
            class="absolute right-3 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white text-slate-800 rounded-full w-11 h-11 inline-flex items-center justify-center shadow-lg z-10"
            title="Siguiente (→)">
      <i class="fas fa-chevron-right"></i>
    </button>
  </div>
