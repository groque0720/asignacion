<?php
/* Configurador · Encuestas (lista + alta/edición + activar). */
require __DIR__ . '/config/config_app.php';
if (!$puedeConfigurar) { header('Location: index.php'); exit(); }

$title    = 'Configurar Encuestas · Usados';
$cfgTitle = 'Encuestas';
$cfgIcon  = 'fa-clipboard-list';
$bodyData = 'cfgEncuestas()';
$bodyInit = 'load()';
$jsFile   = 'cfg_encuestas.js';

ob_start();
include __DIR__ . '/views/components/cfg_header.php';
?>
  <main class="max-w-[1400px] mx-auto px-6 py-5 space-y-5">
    <div class="flex items-center justify-between">
      <p class="text-sm text-slate-500">Sólo puede haber <strong>una encuesta activa</strong>. Es la que reciben los clientes.</p>
      <button @click="abrirNueva()" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
        <i class="fas fa-plus mr-1"></i> Nueva encuesta
      </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <table class="w-full text-sm dv-table">
        <thead><tr>
          <th class="px-4 py-2.5 text-left font-semibold">Encuesta</th>
          <th class="px-4 py-2.5 text-center font-semibold" style="width:120px">Preguntas</th>
          <th class="px-4 py-2.5 text-center font-semibold" style="width:120px">Estado</th>
          <th class="px-4 py-2.5 text-right font-semibold" style="width:340px"></th>
        </tr></thead>
        <tbody>
          <template x-for="e in items" :key="e.id_encuesta">
            <tr class="hover:bg-blue-50/40">
              <td class="px-4 py-3">
                <div class="font-semibold text-slate-900" x-text="e.nombre"></div>
                <div class="text-xs text-slate-400 truncate max-w-md" x-text="e.descripcion"></div>
              </td>
              <td class="px-4 py-3 text-center num" x-text="e.nro_preguntas"></td>
              <td class="px-4 py-3 text-center">
                <span class="inline-block px-2 py-0.5 rounded text-[11px] font-semibold"
                      :class="e.activa ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500'"
                      x-text="e.activa ? 'Activa' : 'Inactiva'"></span>
              </td>
              <td class="px-4 py-3 text-right whitespace-nowrap space-x-1">
                <a :href="'preguntas.php?id_encuesta=' + e.id_encuesta"
                   class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-md bg-slate-100 text-slate-700 hover:bg-slate-200 text-xs font-medium">
                  <i class="fas fa-list-ol"></i> Preguntas
                </a>
                <button @click="abrirEditar(e)" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-md bg-slate-100 text-slate-700 hover:bg-slate-200 text-xs font-medium">
                  <i class="fas fa-pen"></i> Editar
                </button>
                <button x-show="!e.activa" @click="activar(e)" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-md bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-xs font-medium">
                  <i class="fas fa-circle-play"></i> Activar
                </button>
                <button x-show="e.activa" @click="desactivar(e)" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-md bg-amber-50 text-amber-700 hover:bg-amber-100 text-xs font-medium">
                  <i class="fas fa-circle-pause"></i> Desactivar
                </button>
                <button x-show="!e.activa" @click="eliminar(e)" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-md text-slate-400 hover:bg-red-50 hover:text-red-600 text-xs font-medium">
                  <i class="fas fa-trash"></i>
                </button>
              </td>
            </tr>
          </template>
          <tr x-show="!loading && items.length === 0">
            <td colspan="4" class="px-4 py-10 text-center text-slate-400"><i class="fas fa-inbox text-2xl mb-2 block"></i> Sin encuestas. Creá una.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </main>

  <!-- Modal alta/edición -->
  <div x-show="modal.open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4" @keydown.escape.window="modal.open=false">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg" @click.outside="modal.open=false">
      <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
        <h3 class="text-base font-bold text-slate-900" x-text="modal.id ? 'Editar encuesta' : 'Nueva encuesta'"></h3>
        <button @click="modal.open=false" class="text-slate-400 hover:text-slate-700"><i class="fas fa-xmark text-lg"></i></button>
      </div>
      <div class="p-5 space-y-4">
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Nombre *</label>
          <input type="text" x-model="modal.nombre" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Descripción interna</label>
          <input type="text" x-model="modal.descripcion" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Mensaje de bienvenida (lo ve el cliente)</label>
          <textarea x-model="modal.mensaje_bienvenida" rows="3" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none resize-none"></textarea>
        </div>
        <p x-show="modal.error" class="text-red-600 text-sm"><i class="fas fa-circle-exclamation mr-1"></i><span x-text="modal.error"></span></p>
      </div>
      <div class="flex justify-end gap-2 px-5 py-4 border-t border-gray-100">
        <button @click="modal.open=false" class="px-4 py-2 rounded-lg border border-gray-300 text-slate-600 text-sm hover:bg-gray-50">Cancelar</button>
        <button @click="guardar()" :disabled="modal.saving" class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium disabled:opacity-60">
          <i class="fas" :class="modal.saving ? 'fa-spinner fa-spin' : 'fa-check'"></i> Guardar
        </button>
      </div>
    </div>
  </div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../comun/layout.php';
