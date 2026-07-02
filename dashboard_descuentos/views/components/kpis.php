<?php /* Tarjetas KPI del período filtrado. */ ?>
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
  <div class="rounded-xl shadow-sm border border-slate-200 p-4 bg-white">
    <p class="text-xs text-slate-500 font-medium">Entregadas</p>
    <p class="text-2xl font-bold text-slate-900 num" x-text="int(kpis.entregadas)"></p>
  </div>
  <div class="rounded-xl shadow-sm border border-blue-100 p-4 bg-gradient-to-br from-blue-50 to-white">
    <p class="text-xs text-slate-500 font-medium">Con descuento</p>
    <p class="text-2xl font-bold text-blue-700 num" x-text="int(kpis.conDescuento)"></p>
  </div>
  <div class="rounded-xl shadow-sm border border-pink-100 p-4 bg-gradient-to-br from-pink-50 to-white">
    <p class="text-xs text-slate-500 font-medium">Penetración</p>
    <p class="text-2xl font-bold text-pink-600 num" x-text="pct1(kpis.penetracion)"></p>
  </div>
  <div class="rounded-xl shadow-sm border border-red-100 p-4 bg-gradient-to-br from-red-50 to-white">
    <p class="text-xs text-slate-500 font-medium">Monto descontado</p>
    <p class="text-xl font-bold text-red-600 num" x-text="money(kpis.montoDescuento)"></p>
  </div>
  <div class="rounded-xl shadow-sm border border-amber-100 p-4 bg-gradient-to-br from-amber-50 to-white">
    <p class="text-xs text-slate-500 font-medium">Descuento promedio</p>
    <p class="text-xl font-bold text-amber-600 num" x-text="money(kpis.descPromedio)"></p>
    <p class="text-[11px] text-slate-400">por unidad con descuento</p>
  </div>
  <div class="rounded-xl shadow-sm border border-violet-100 p-4 bg-gradient-to-br from-violet-50 to-white">
    <p class="text-xs text-slate-500 font-medium">Desc. s/ precio</p>
    <p class="text-2xl font-bold text-violet-700 num" x-text="pct1(kpis.descPctGlobal)"></p>
    <p class="text-[11px] text-slate-400">descuento / precio bruto</p>
  </div>
</div>
