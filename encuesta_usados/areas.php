<?php
/* Configurador · Áreas responsables. */
require __DIR__ . '/config/config_app.php';
if (!$puedeConfigurar) { header('Location: index.php'); exit(); }

$title    = 'Áreas · Usados';
$cfgTitle = 'Áreas';
$cfgIcon  = 'fa-tags';
$bodyData = 'cfgAreas()';
$bodyInit = 'load()';
$jsFile   = 'cfg_areas.js';

ob_start();
include __DIR__ . '/views/components/cfg_header.php';
?>
  <main class="max-w-[900px] mx-auto px-6 py-5 space-y-5">
    <div class="flex items-center justify-between">
      <p class="text-sm text-slate-500">Áreas para clasificar preguntas y agrupar resultados.</p>
      <button @click="abrirNueva()" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg"><i class="fas fa-plus mr-1"></i> Nueva área</button>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <table class="w-full text-sm dv-table">
        <thead><tr>
          <th class="px-4 py-2.5 text-center font-semibold" style="width:70px">Orden</th>
          <th class="px-4 py-2.5 text-left font-semibold">Área</th>
          <th class="px-4 py-2.5 text-left font-semibold" style="width:120px">Color</th>
          <th class="px-4 py-2.5 text-right font-semibold" style="width:140px"></th>
        </tr></thead>
        <tbody>
          <template x-for="a in items" :key="a.id_area">
            <tr class="hover:bg-blue-50/40">
              <td class="px-4 py-3 text-center num" x-text="a.nro_orden"></td>
              <td class="px-4 py-3">
                <span class="inline-block px-2 py-0.5 rounded text-white text-xs font-semibold" :style="'background:'+a.color" x-text="a.nombre"></span>
              </td>
              <td class="px-4 py-3"><span class="font-mono text-xs text-slate-500" x-text="a.color"></span></td>
              <td class="px-4 py-3 text-right whitespace-nowrap space-x-1">
                <button @click="abrirEditar(a)" class="w-8 h-8 rounded-md text-slate-400 hover:text-blue-600 hover:bg-blue-50"><i class="fas fa-pen"></i></button>
                <button @click="eliminar(a)"    class="w-8 h-8 rounded-md text-slate-400 hover:text-red-600 hover:bg-red-50"><i class="fas fa-trash"></i></button>
              </td>
            </tr>
          </template>
          <tr x-show="!loading && items.length===0"><td colspan="4" class="px-4 py-10 text-center text-slate-400">Sin áreas.</td></tr>
        </tbody>
      </table>
    </div>
  </main>

  <div x-show="modal.open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4" @keydown.escape.window="modal.open=false">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" @click.outside="modal.open=false">
      <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
        <h3 class="text-base font-bold text-slate-900" x-text="modal.id ? 'Editar área' : 'Nueva área'"></h3>
        <button @click="modal.open=false" class="text-slate-400 hover:text-slate-700"><i class="fas fa-xmark text-lg"></i></button>
      </div>
      <div class="p-5 space-y-4">
        <div><label class="block text-xs font-medium text-slate-500 mb-1">Nombre *</label>
          <input type="text" x-model="modal.nombre" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500"></div>
        <div class="grid grid-cols-2 gap-4">
          <div><label class="block text-xs font-medium text-slate-500 mb-1">Color</label>
            <input type="color" x-model="modal.color" class="w-full h-10 border border-gray-300 rounded-lg px-1 py-1 outline-none"></div>
          <div><label class="block text-xs font-medium text-slate-500 mb-1">Orden</label>
            <input type="number" x-model.number="modal.nro_orden" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 outline-none"></div>
        </div>
        <p x-show="modal.error" class="text-red-600 text-sm"><i class="fas fa-circle-exclamation mr-1"></i><span x-text="modal.error"></span></p>
      </div>
      <div class="flex justify-end gap-2 px-5 py-4 border-t border-gray-100">
        <button @click="modal.open=false" class="px-4 py-2 rounded-lg border border-gray-300 text-slate-600 text-sm hover:bg-gray-50">Cancelar</button>
        <button @click="guardar()" :disabled="modal.saving" class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium disabled:opacity-60"><i class="fas" :class="modal.saving?'fa-spinner fa-spin':'fa-check'"></i> Guardar</button>
      </div>
    </div>
  </div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../comun/layout.php';
