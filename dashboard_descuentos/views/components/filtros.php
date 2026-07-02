<?php /* Panel de filtros (server-side vía load()). */ ?>
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
  <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-3"><i class="fas fa-filter mr-1.5"></i>Filtros</h3>
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 items-end" @keydown.enter="load()">

    <div>
      <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wide mb-1">Año</label>
      <select x-model.number="filtros.anio" @change="filtros.desde='';filtros.hasta='';load()"
              class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
        <template x-for="y in opciones.anios" :key="y"><option :value="y" x-text="y"></option></template>
      </select>
    </div>

    <div>
      <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wide mb-1">Desde (entrega)</label>
      <input type="date" x-model="filtros.desde" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
    </div>
    <div>
      <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wide mb-1">Hasta (entrega)</label>
      <input type="date" x-model="filtros.hasta" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div>
      <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wide mb-1">Sucursal</label>
      <select x-model.number="filtros.idsucursal" @change="load()"
              class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
        <option :value="0">Todas</option>
        <template x-for="s in opciones.sucursales" :key="s.id"><option :value="s.id" x-text="s.nombre"></option></template>
      </select>
    </div>

    <div>
      <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wide mb-1">Modelo</label>
      <select x-model.number="filtros.idgrupo" @change="load()"
              class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
        <option :value="0">Todos</option>
        <template x-for="g in opciones.grupos" :key="g.id"><option :value="g.id" x-text="g.nombre"></option></template>
      </select>
    </div>

    <div>
      <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wide mb-1">Vendedor</label>
      <select x-model.number="filtros.idvendedor" @change="load()"
              class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
        <option :value="0">Todos</option>
        <template x-for="v in opciones.vendedores" :key="v.id"><option :value="v.id" x-text="v.nombre"></option></template>
      </select>
    </div>

    <div class="col-span-2 sm:col-span-3 lg:col-span-6 flex items-center gap-2">
      <button @click="load()" class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
        <i class="fas fa-magnifying-glass"></i> Aplicar
      </button>
      <button @click="limpiar()" class="inline-flex items-center gap-1.5 text-sm text-slate-600 hover:text-slate-900 border border-gray-300 rounded-lg px-3 py-2 hover:bg-gray-50">
        <i class="fas fa-xmark"></i> Limpiar
      </button>
      <span class="text-xs text-slate-400 ml-1">El rango de fechas (Desde/Hasta) tiene prioridad sobre el Año.</span>
    </div>
  </div>
</div>
