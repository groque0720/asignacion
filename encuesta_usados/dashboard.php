<?php
/* Resultados · Dashboard de encuestas de usados. */
require __DIR__ . '/config/config_app.php';

// Años con respuestas (render server-side: las <option> deben existir en el parseo
// para que el x-model del select enganche el año actual sin resetearse a "Todos").
$anioActual = (int)date('Y');
$anios = [];
$ry = mysqli_query($con, "SELECT DISTINCT YEAR(t.fecha_respuesta) y
                          FROM encu_tokens t JOIN encu_respuestas r ON r.id_token = t.id_token
                          WHERE t.fecha_respuesta IS NOT NULL ORDER BY y DESC");
while ($y = mysqli_fetch_assoc($ry)) if ((int)$y['y'] > 0) $anios[] = (int)$y['y'];
if (!in_array($anioActual, $anios, true)) array_unshift($anios, $anioActual);

$meses = [1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',
          7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'];

$title    = 'Resultados · Usados';
$bodyData = 'dashboardUsados()';
$bodyInit = 'init()';
$jsFile   = 'dashboard_usados.js';
$extraHead = '<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>'
           . '<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>';

ob_start();
?>
  <header class="bg-slate-900 text-white shadow-lg sticky top-0 z-30">
    <div class="max-w-[1400px] mx-auto px-6 py-3 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <a href="index.php" class="w-9 h-9 bg-slate-700 hover:bg-slate-600 rounded-lg flex items-center justify-center" title="Volver a Entregas"><i class="fas fa-arrow-left text-sm"></i></a>
        <div class="w-9 h-9 bg-blue-600 rounded-lg flex items-center justify-center"><i class="fas fa-chart-line text-sm"></i></div>
        <div><h1 class="text-sm font-bold leading-tight">Resultados de Encuestas · Usados</h1>
        <p class="text-slate-400 text-xs">Derka y Vargas S.A.</p></div>
      </div>
    </div>
  </header>

  <main class="max-w-[1400px] mx-auto px-6 py-5 space-y-5">
    <!-- Filtros -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
      <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-3"><i class="fas fa-filter mr-1.5"></i>Filtros</h3>
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 items-end" @keydown.enter="load()">
        <div>
          <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wide mb-1">Año</label>
          <select x-model.number="filtros.anio" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
            <option value="0">Todos</option>
            <?php foreach ($anios as $y): ?><option value="<?= $y ?>"><?= $y ?></option><?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wide mb-1">Mes</label>
          <select x-model.number="filtros.mes" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
            <option value="0">Todos</option>
            <?php foreach ($meses as $mid => $mnom): ?><option value="<?= $mid ?>"><?= $mnom ?></option><?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wide mb-1">Desde</label>
          <input type="date" x-model="filtros.desde" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wide mb-1">Hasta</label>
          <input type="date" x-model="filtros.hasta" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wide mb-1">Sucursal</label>
          <select x-model.number="filtros.suc" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
            <template x-for="s in sucursales" :key="s.id"><option :value="s.id" x-text="s.nombre"></option></template>
          </select>
        </div>
        <div>
          <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wide mb-1">Asesor</label>
          <input type="text" x-model="filtros.asesor" placeholder="Todos" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wide mb-1">Área</label>
          <select x-model.number="filtros.area" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
            <option :value="0">Todas</option>
            <template x-for="ar in opciones.areas" :key="ar.id"><option :value="ar.id" x-text="ar.nombre"></option></template>
          </select>
        </div>
        <div class="col-span-2 sm:col-span-3 lg:col-span-5 flex items-center gap-2">
          <button @click="load()" class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
            <i class="fas fa-magnifying-glass"></i> Aplicar
          </button>
          <button @click="limpiar()" class="inline-flex items-center gap-1.5 text-sm text-slate-600 hover:text-slate-900 border border-gray-300 rounded-lg px-3 py-2 hover:bg-gray-50">
            <i class="fas fa-xmark"></i> Limpiar
          </button>
        </div>
      </div>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <div class="rounded-xl shadow-sm border border-emerald-100 p-4 bg-gradient-to-br from-emerald-50 to-white">
        <p class="text-xs text-slate-500 font-medium">Encuestas completadas</p>
        <p class="text-2xl font-bold text-slate-900 num" x-text="kpis.completadas"></p>
      </div>
      <div class="rounded-xl shadow-sm border border-violet-100 p-4 bg-gradient-to-br from-violet-50 to-white">
        <p class="text-xs text-slate-500 font-medium">Promedio general</p>
        <p class="text-2xl font-bold num" :class="kpis.prom===null?'text-slate-300':'text-violet-700'" x-text="kpis.prom===null?'—':kpis.prom.toFixed(2)"></p>
      </div>
      <div class="rounded-xl shadow-sm border border-blue-100 p-4 bg-gradient-to-br from-blue-50 to-white">
        <p class="text-xs text-slate-500 font-medium">Links generados</p>
        <p class="text-2xl font-bold text-slate-900 num" x-text="kpis.generadas"></p>
      </div>
      <div class="rounded-xl shadow-sm border border-amber-100 p-4 bg-gradient-to-br from-amber-50 to-white">
        <p class="text-xs text-slate-500 font-medium">Tasa de respuesta</p>
        <p class="text-2xl font-bold text-slate-900 num" x-text="kpis.tasa===null?'—':(kpis.tasa+'%')"></p>
      </div>
    </div>

    <!-- Tarjetas dinámicas por nivel -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3" x-show="niveles.length">
      <template x-for="n in niveles" :key="n.nombre">
        <div class="rounded-xl bg-white shadow-sm border border-gray-200 border-t-4 p-4" :style="'border-top-color:'+n.color">
          <div class="flex items-center justify-between mb-1.5">
            <span class="text-[11px] font-bold uppercase tracking-wide truncate" :style="'color:'+n.color" x-text="n.nombre"></span>
            <span class="w-3 h-3 rounded-full flex-shrink-0" :style="'background:'+n.color"></span>
          </div>
          <p class="text-3xl font-extrabold num leading-none" :style="'color:'+n.color" x-text="pct(n.n) + '%'"></p>
          <p class="text-[11px] text-slate-400 num mt-1.5" x-text="n.n + ' enc · ' + n.desde.toFixed(1) + '–' + n.hasta.toFixed(1)"></p>
        </div>
      </template>
    </div>

    <!-- Charts -->
    <div class="grid md:grid-cols-2 gap-4">
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <h3 class="text-sm font-bold text-slate-700 mb-3">Tendencia mensual (promedio)</h3>
        <div class="relative h-64"><canvas x-ref="cMes"></canvas></div>
      </div>
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <h3 class="text-sm font-bold text-slate-700 mb-3">Distribución por nivel</h3>
        <div class="relative h-64"><canvas x-ref="cNivel"></canvas></div>
      </div>
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <h3 class="text-sm font-bold text-slate-700 mb-3">Promedio por área</h3>
        <div class="relative h-64"><canvas x-ref="cArea"></canvas></div>
      </div>
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <h3 class="text-sm font-bold text-slate-700 mb-3">Promedio por sucursal</h3>
        <div class="relative h-64"><canvas x-ref="cSuc"></canvas></div>
      </div>
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 md:col-span-2">
        <h3 class="text-sm font-bold text-slate-700 mb-3">Top asesores (promedio)</h3>
        <div class="relative h-64"><canvas x-ref="cAsesor"></canvas></div>
      </div>
    </div>

    <!-- Tabla -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto" style="max-height: calc(100vh - 320px);">
        <table class="w-full text-sm dv-table">
          <thead><tr>
            <th class="px-3 py-2.5 text-left font-semibold">Respondida</th>
            <th class="px-3 py-2.5 text-left font-semibold">Cliente</th>
            <th class="px-3 py-2.5 text-left font-semibold">Vehículo</th>
            <th class="px-3 py-2.5 text-left font-semibold">Asesor</th>
            <th class="px-3 py-2.5 text-left font-semibold">Sucursal</th>
            <th class="px-3 py-2.5 text-center font-semibold">Promedio</th>
            <th class="px-3 py-2.5 text-right font-semibold"></th>
          </tr></thead>
          <tbody>
            <template x-for="r in tabla" :key="r.id_respuesta">
              <tr class="hover:bg-blue-50/40">
                <td class="px-3 py-2 whitespace-nowrap text-slate-600" x-text="fechaHora(r.fecha)"></td>
                <td class="px-3 py-2 font-medium text-slate-900 truncate" x-text="r.cliente" :title="r.cliente"></td>
                <td class="px-3 py-2 text-slate-600 truncate" x-text="r.vehiculo" :title="r.vehiculo"></td>
                <td class="px-3 py-2 text-slate-600 truncate" x-text="r.asesor"></td>
                <td class="px-3 py-2 text-slate-600" x-text="sucNombre(r.id_sucursal)"></td>
                <td class="px-3 py-2 text-center font-bold num" :style="'color:'+promColor(r.promedio)" x-text="r.promedio!==null?r.promedio.toFixed(1):'—'"></td>
                <td class="px-3 py-2 text-right">
                  <a :href="'detalle.php?id='+r.id_respuesta" class="text-blue-600 hover:underline text-xs font-medium">Ver <i class="fas fa-arrow-right"></i></a>
                </td>
              </tr>
            </template>
            <tr x-show="!loading && tabla.length===0"><td colspan="7" class="px-3 py-10 text-center text-slate-400"><i class="fas fa-inbox text-2xl mb-2 block"></i> Sin respuestas para este filtro.</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </main>
<?php
$content = ob_get_clean();
include __DIR__ . '/../comun/layout.php';
