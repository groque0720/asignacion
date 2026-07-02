<?php /*
 * Gráficos (Chart.js) con toggle Gráfico/Tabla por tarjeta.
 * El gráfico muestra Top 10; la tabla muestra el ranking completo de la dimensión.
 */ ?>
<?php
// Markup reutilizable del control segmentado (Gráfico | Tabla). $ref = clave en `vista`.
$toggle = function ($ref) { ?>
  <div class="flex items-center gap-0.5 bg-slate-100 rounded-lg p-0.5 text-xs">
    <button @click="verVista('<?= $ref ?>','chart')" title="Gráfico"
            :class="vista.<?= $ref ?>==='chart' ? 'bg-white shadow text-slate-800' : 'text-slate-500 hover:text-slate-700'"
            class="px-2 py-1 rounded-md"><i class="fas fa-chart-column"></i></button>
    <button @click="verVista('<?= $ref ?>','tabla')" title="Tabla"
            :class="vista.<?= $ref ?>==='tabla' ? 'bg-white shadow text-slate-800' : 'text-slate-500 hover:text-slate-700'"
            class="px-2 py-1 rounded-md"><i class="fas fa-table"></i></button>
  </div>
<?php };

// Cabecera de tabla de dimensión (Nombre / Entregadas / Con desc / Penetración / Monto / Prom.)
$thDim = function ($col1) { ?>
  <thead><tr>
    <th class="px-2 py-1.5 text-left"><?= $col1 ?></th>
    <th class="px-2 py-1.5 text-right">Entreg.</th>
    <th class="px-2 py-1.5 text-right">C/desc</th>
    <th class="px-2 py-1.5 text-right">Penetr.</th>
    <th class="px-2 py-1.5 text-right">Monto desc.</th>
    <th class="px-2 py-1.5 text-right">Promedio</th>
  </tr></thead>
<?php };
?>

<div class="grid lg:grid-cols-2 gap-4">

  <!-- ── Por sucursal ──────────────────────────────────────────────────────── -->
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
    <div class="flex items-center justify-between mb-3">
      <h3 class="text-sm font-bold text-slate-700">Descuento por sucursal <span class="text-slate-400 font-normal">· monto + penetración</span></h3>
      <?php $toggle('cSucursal'); ?>
    </div>
    <div class="relative h-72" x-show="vista.cSucursal==='chart'"><canvas x-ref="cSucursal"></canvas></div>
    <div class="overflow-auto h-72" x-show="vista.cSucursal==='tabla'" x-cloak>
      <table class="w-full text-xs dv-table">
        <?php $thDim('Sucursal'); ?>
        <tbody>
          <template x-for="s in porSucursal" :key="s.clave">
            <tr class="hover:bg-blue-50/40">
              <td class="px-2 py-1.5 font-medium text-slate-800" x-text="s.clave"></td>
              <td class="px-2 py-1.5 text-right num" x-text="int(s.entregadas)"></td>
              <td class="px-2 py-1.5 text-right num" x-text="int(s.conDesc)"></td>
              <td class="px-2 py-1.5 text-right num text-pink-600" x-text="pct1(s.penetracion)"></td>
              <td class="px-2 py-1.5 text-right num text-red-600 font-semibold" x-text="money(s.monto)"></td>
              <td class="px-2 py-1.5 text-right num" x-text="money(s.promedio)"></td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ── Tendencia mensual ─────────────────────────────────────────────────── -->
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
    <div class="flex items-center justify-between mb-3">
      <h3 class="text-sm font-bold text-slate-700">Tendencia mensual <span class="text-slate-400 font-normal">· monto + penetración</span></h3>
      <?php $toggle('cTendencia'); ?>
    </div>
    <div class="relative h-72" x-show="vista.cTendencia==='chart'"><canvas x-ref="cTendencia"></canvas></div>
    <div class="overflow-auto h-72" x-show="vista.cTendencia==='tabla'" x-cloak>
      <table class="w-full text-xs dv-table">
        <thead><tr>
          <th class="px-2 py-1.5 text-left">Mes</th>
          <th class="px-2 py-1.5 text-right">Entreg.</th>
          <th class="px-2 py-1.5 text-right">C/desc</th>
          <th class="px-2 py-1.5 text-right">Penetr.</th>
          <th class="px-2 py-1.5 text-right">Monto desc.</th>
        </tr></thead>
        <tbody>
          <template x-for="t in tendencia" :key="t.periodo">
            <tr class="hover:bg-blue-50/40">
              <td class="px-2 py-1.5 font-medium text-slate-800" x-text="t.etiqueta"></td>
              <td class="px-2 py-1.5 text-right num" x-text="int(t.entregadas)"></td>
              <td class="px-2 py-1.5 text-right num" x-text="int(t.conDesc)"></td>
              <td class="px-2 py-1.5 text-right num text-amber-600" x-text="pct1(t.penetracion)"></td>
              <td class="px-2 py-1.5 text-right num text-red-600 font-semibold" x-text="money(t.monto)"></td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ── Modelos ───────────────────────────────────────────────────────────── -->
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
    <div class="flex items-center justify-between mb-3">
      <h3 class="text-sm font-bold text-slate-700">Modelos <span class="text-slate-400 font-normal">· gráfico: top 10 · tabla: todos</span></h3>
      <?php $toggle('cModelo'); ?>
    </div>
    <div class="relative h-80" x-show="vista.cModelo==='chart'"><canvas x-ref="cModelo"></canvas></div>
    <div class="overflow-auto h-80" x-show="vista.cModelo==='tabla'" x-cloak>
      <table class="w-full text-xs dv-table">
        <?php $thDim('Modelo'); ?>
        <tbody>
          <template x-for="m in porModelo" :key="m.clave">
            <tr class="hover:bg-blue-50/40">
              <td class="px-2 py-1.5 font-medium text-slate-800" x-text="m.clave"></td>
              <td class="px-2 py-1.5 text-right num" x-text="int(m.entregadas)"></td>
              <td class="px-2 py-1.5 text-right num" x-text="int(m.conDesc)"></td>
              <td class="px-2 py-1.5 text-right num text-pink-600" x-text="pct1(m.penetracion)"></td>
              <td class="px-2 py-1.5 text-right num text-red-600 font-semibold" x-text="money(m.monto)"></td>
              <td class="px-2 py-1.5 text-right num" x-text="money(m.promedio)"></td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ── Vendedores ────────────────────────────────────────────────────────── -->
  <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
    <div class="flex items-center justify-between mb-3">
      <h3 class="text-sm font-bold text-slate-700">Vendedores <span class="text-slate-400 font-normal">· gráfico: top 10 · tabla: todos</span></h3>
      <?php $toggle('cVendedor'); ?>
    </div>
    <div class="relative h-80" x-show="vista.cVendedor==='chart'"><canvas x-ref="cVendedor"></canvas></div>
    <div class="overflow-auto h-80" x-show="vista.cVendedor==='tabla'" x-cloak>
      <table class="w-full text-xs dv-table">
        <thead><tr>
          <th class="px-2 py-1.5 text-left">Vendedor</th>
          <th class="px-2 py-1.5 text-right">Entreg.</th>
          <th class="px-2 py-1.5 text-right">C/desc</th>
          <th class="px-2 py-1.5 text-right">Penetr.</th>
          <th class="px-2 py-1.5 text-right">Monto desc.</th>
          <th class="px-2 py-1.5 text-right">Promedio</th>
        </tr></thead>
        <tbody>
          <template x-for="v in porVendedor" :key="v.clave">
            <tr class="hover:bg-blue-50/40">
              <td class="px-2 py-1.5 font-medium text-slate-800" x-text="v.clave"></td>
              <td class="px-2 py-1.5 text-right num" x-text="int(v.entregadas)"></td>
              <td class="px-2 py-1.5 text-right num" x-text="int(v.conDesc)"></td>
              <td class="px-2 py-1.5 text-right num text-pink-600" x-text="pct1(v.penetracion)"></td>
              <td class="px-2 py-1.5 text-right num text-red-600 font-semibold" x-text="money(v.monto)"></td>
              <td class="px-2 py-1.5 text-right num" x-text="money(v.promedio)"></td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>
  </div>

</div>
