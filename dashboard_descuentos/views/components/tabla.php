<?php /* Tabla detalle: una fila por unidad entregada. Filtro/orden en el cliente. */ ?>
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
  <div class="flex flex-wrap items-center justify-between gap-3 p-3 border-b border-gray-100">
    <div class="flex items-center gap-3">
      <h3 class="text-sm font-bold text-slate-700">Detalle de entregas</h3>
      <label class="inline-flex items-center gap-1.5 text-xs text-slate-600 cursor-pointer">
        <input type="checkbox" x-model="soloDesc" class="rounded border-gray-300"> Sólo con descuento
      </label>
    </div>
    <div class="flex items-center gap-2">
      <span class="text-xs text-slate-400">
        <span class="num" x-text="int(tablaFiltrada.length)"></span> filas
        <template x-if="tablaFiltrada.length > limite"><span> · mostrando <span x-text="limite"></span></span></template>
      </span>
      <div class="relative">
        <i class="fas fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
        <input type="text" x-model="busqueda" placeholder="Buscar cliente, vendedor, modelo, unidad…"
               class="text-sm border border-gray-300 rounded-lg pl-8 pr-3 py-1.5 w-72 outline-none focus:ring-2 focus:ring-blue-500">
      </div>
    </div>
  </div>

  <div class="overflow-x-auto" style="max-height: calc(100vh - 200px);">
    <table class="w-full text-xs dv-table">
      <thead><tr>
        <th class="px-2.5 py-2 text-left">Entrega</th>
        <th class="px-2.5 py-2 text-left">Unidad</th>
        <th class="px-2.5 py-2 text-left">Cliente</th>
        <th class="px-2.5 py-2 text-left">Modelo / Versión</th>
        <th class="px-2.5 py-2 text-left">Vendedor</th>
        <th class="px-2.5 py-2 text-left">Sucursal</th>
        <th class="px-2.5 py-2 text-right">Operación</th>
        <th class="px-2.5 py-2 text-right">Descuento</th>
        <th class="px-2.5 py-2 text-right">% s/precio</th>
      </tr></thead>
      <tbody>
        <template x-for="x in tablaVisible" :key="x.id_unidad">
          <tr class="hover:bg-blue-50/40" :class="x.con_desc ? '' : 'text-slate-400'">
            <td class="px-2.5 py-1.5 whitespace-nowrap" x-text="fecha(x.fecha)"></td>
            <td class="px-2.5 py-1.5 num whitespace-nowrap" x-text="x.nro_unidad"></td>
            <td class="px-2.5 py-1.5 font-medium text-slate-800 truncate max-w-[15rem]" x-text="x.cliente" :title="x.cliente"></td>
            <td class="px-2.5 py-1.5 text-slate-600 max-w-[14rem]">
              <div class="truncate" :title="x.modelo + (x.version ? ' · ' + x.version : '')">
                <span x-text="x.modelo"></span><span class="text-slate-400" x-show="x.version" x-text="' · ' + x.version"></span>
              </div>
            </td>
            <td class="px-2.5 py-1.5 text-slate-600 truncate max-w-[10rem]" x-text="x.vendedor" :title="x.vendedor"></td>
            <td class="px-2.5 py-1.5 text-slate-600 whitespace-nowrap" x-text="x.sucursal"></td>
            <td class="px-2.5 py-1.5 text-right num whitespace-nowrap" x-text="money(x.operacion)"></td>
            <td class="px-2.5 py-1.5 text-right num font-semibold whitespace-nowrap" :class="x.con_desc ? 'text-red-600' : ''" x-text="x.descuento ? money(x.descuento) : '—'"></td>
            <td class="px-2.5 py-1.5 text-right num whitespace-nowrap" :class="x.con_desc ? 'text-red-600' : ''" x-text="x.con_desc ? pct1(pctFila(x)) : '—'"></td>
          </tr>
        </template>
        <tr x-show="!loading && tablaFiltrada.length === 0">
          <td colspan="9" class="px-3 py-10 text-center text-slate-400">
            <i class="fas fa-inbox text-2xl mb-2 block"></i> Sin entregas para este filtro.
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
