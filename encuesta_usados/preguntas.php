<?php
/* Configurador · Preguntas de una encuesta. */
require __DIR__ . '/config/config_app.php';
if (!$puedeConfigurar) { header('Location: index.php'); exit(); }

$idEncuesta = isset($_GET['id_encuesta']) ? (int)$_GET['id_encuesta'] : 0;

$title    = 'Preguntas · Usados';
$cfgTitle = 'Preguntas';
$cfgIcon  = 'fa-list-ol';
$bodyData = 'cfgPreguntas(' . $idEncuesta . ')';
$bodyInit = 'load()';
$jsFile   = 'cfg_preguntas.js';

ob_start();
include __DIR__ . '/views/components/cfg_header.php';
?>
  <main class="max-w-[1400px] mx-auto px-6 py-5 space-y-5">
    <div class="flex items-center justify-between">
      <div>
        <a href="encuestas.php" class="text-xs text-blue-600 hover:underline"><i class="fas fa-arrow-left mr-1"></i>Encuestas</a>
        <h2 class="text-lg font-bold text-slate-900" x-text="encuesta.nombre || '…'"></h2>
        <p class="text-xs text-slate-500">
          <span x-text="items.length"></span> preguntas ·
          <span :class="encuesta.activa ? 'text-emerald-600 font-medium' : 'text-slate-400'" x-text="encuesta.activa ? 'Encuesta activa' : 'Inactiva'"></span>
        </p>
      </div>
      <button @click="abrirNueva()" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
        <i class="fas fa-plus mr-1"></i> Nueva pregunta
      </button>
    </div>

    <div class="space-y-2">
      <template x-for="(p, idx) in items" :key="p.id_pregunta">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex items-start gap-4">
          <!-- Orden -->
          <div class="flex flex-col items-center gap-1 pt-1">
            <button @click="mover(p,'up')"   :disabled="idx===0" class="text-slate-300 hover:text-blue-600 disabled:opacity-30"><i class="fas fa-chevron-up"></i></button>
            <span class="text-xs font-bold text-slate-400" x-text="p.nro_orden"></span>
            <button @click="mover(p,'down')" :disabled="idx===items.length-1" class="text-slate-300 hover:text-blue-600 disabled:opacity-30"><i class="fas fa-chevron-down"></i></button>
          </div>
          <!-- Contenido -->
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap mb-1">
              <span class="text-[11px] font-semibold px-2 py-0.5 rounded bg-slate-100 text-slate-600" x-text="tipoLabel(p.tipo)"></span>
              <span x-show="p.id_area" class="text-[11px] font-semibold px-2 py-0.5 rounded text-white" :style="'background:'+(p.area_color||'#607d8b')" x-text="p.area_nombre"></span>
              <span x-show="p.pondera" class="text-[11px] font-semibold px-2 py-0.5 rounded bg-violet-100 text-violet-700"><i class="fas fa-scale-balanced mr-0.5"></i>Pondera</span>
              <span x-show="p.es_observacion" class="text-[11px] font-semibold px-2 py-0.5 rounded bg-blue-100 text-blue-700">Observación</span>
              <span x-show="p.cond_ref" class="text-[11px] font-semibold px-2 py-0.5 rounded bg-amber-100 text-amber-700" :title="'Si Q'+p.cond_ref+' '+p.cond_op+' '+p.cond_val">
                <i class="fas fa-code-branch mr-0.5"></i>Condicional</span>
            </div>
            <p class="text-slate-900 font-medium" x-text="p.texto"></p>
            <div x-show="p.opciones.length" class="mt-1.5 flex flex-wrap gap-1.5">
              <template x-for="o in p.opciones" :key="o.id">
                <span class="text-[11px] px-2 py-0.5 rounded bg-slate-50 border border-slate-200 text-slate-600" x-text="o.texto"></span>
              </template>
            </div>
          </div>
          <!-- Acciones -->
          <div class="flex items-center gap-1 flex-shrink-0">
            <button @click="abrirEditar(p)" class="w-8 h-8 rounded-md text-slate-400 hover:text-blue-600 hover:bg-blue-50"><i class="fas fa-pen"></i></button>
            <button @click="eliminar(p)"    class="w-8 h-8 rounded-md text-slate-400 hover:text-red-600 hover:bg-red-50"><i class="fas fa-trash"></i></button>
          </div>
        </div>
      </template>
      <div x-show="!loading && items.length===0" class="bg-white rounded-xl border border-gray-200 p-10 text-center text-slate-400">
        <i class="fas fa-list-ol text-2xl mb-2 block"></i> Sin preguntas. Agregá la primera.
      </div>
    </div>
  </main>

  <!-- Modal pregunta -->
  <div x-show="modal.open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4 overflow-y-auto" @keydown.escape.window="modal.open=false">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl my-8" @click.outside="modal.open=false">
      <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
        <h3 class="text-base font-bold text-slate-900" x-text="modal.id ? 'Editar pregunta' : 'Nueva pregunta'"></h3>
        <button @click="modal.open=false" class="text-slate-400 hover:text-slate-700"><i class="fas fa-xmark text-lg"></i></button>
      </div>
      <div class="p-5 space-y-4 max-h-[70vh] overflow-y-auto">
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Texto de la pregunta *</label>
          <textarea x-model="modal.texto" rows="2" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none resize-none"></textarea>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Tipo</label>
            <select x-model.number="modal.tipo" @change="onTipo()" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 outline-none">
              <option :value="1">Escala 1 a 10</option>
              <option :value="2">Sí / No</option>
              <option :value="3">Selección múltiple</option>
              <option :value="4">Lista Sí/No</option>
              <option :value="5">Texto libre</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Área responsable</label>
            <select x-model.number="modal.id_area" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 outline-none">
              <option :value="0">— Sin área —</option>
              <template x-for="a in areas" :key="a.id_area"><option :value="a.id_area" x-text="a.nombre"></option></template>
            </select>
          </div>
        </div>
        <div class="flex items-center gap-5">
          <label class="flex items-center gap-2 text-sm" :class="ponderaBloqueada() ? 'text-slate-300' : 'text-slate-700'">
            <input type="checkbox" x-model="modal.pondera" :disabled="ponderaBloqueada()"> Pondera al promedio
          </label>
          <label x-show="modal.tipo===5" class="flex items-center gap-2 text-sm text-slate-700">
            <input type="checkbox" x-model="modal.es_observacion"> Es campo de observación
          </label>
        </div>

        <!-- Opciones (tipo 3 y 4) -->
        <div x-show="modal.tipo===3 || modal.tipo===4" class="border border-gray-200 rounded-lg p-3 bg-slate-50">
          <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-semibold text-slate-600">Opciones</span>
            <button @click="addOpcion()" class="text-xs text-blue-600 hover:underline"><i class="fas fa-plus mr-0.5"></i>Agregar</button>
          </div>
          <div class="space-y-2">
            <template x-for="(o,i) in modal.opciones" :key="i">
              <div class="flex items-center gap-2">
                <input type="text" x-model="o.texto" placeholder="Texto de la opción" class="flex-1 text-sm border border-gray-300 rounded-lg px-3 py-1.5 outline-none">
                <button @click="delOpcion(i)" class="text-slate-400 hover:text-red-600"><i class="fas fa-xmark"></i></button>
              </div>
            </template>
            <p x-show="modal.opciones.length===0" class="text-xs text-slate-400">Agregá al menos una opción.</p>
          </div>
        </div>

        <!-- Condicional -->
        <div class="border border-gray-200 rounded-lg p-3">
          <label class="flex items-center gap-2 text-sm text-slate-700 mb-2">
            <input type="checkbox" x-model="modal.cond_on"> Mostrar sólo si se cumple una condición
          </label>
          <div x-show="modal.cond_on" class="grid grid-cols-3 gap-2">
            <select x-model.number="modal.cond_ref" class="text-sm border border-gray-300 rounded-lg px-2 py-1.5 outline-none">
              <option :value="0">— Pregunta —</option>
              <template x-for="p in refDisponibles()" :key="p.id_pregunta">
                <option :value="p.id_pregunta" x-text="'Q'+p.nro_orden+': '+recortar(p.texto)"></option>
              </template>
            </select>
            <select x-model="modal.cond_op" class="text-sm border border-gray-300 rounded-lg px-2 py-1.5 outline-none">
              <option value="<">&lt;</option><option value="<=">&le;</option><option value="=">=</option>
              <option value=">=">&ge;</option><option value=">">&gt;</option><option value="!=">&ne;</option>
            </select>
            <input type="text" x-model="modal.cond_val" placeholder="Valor (ej. 7)" class="text-sm border border-gray-300 rounded-lg px-2 py-1.5 outline-none">
          </div>
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
