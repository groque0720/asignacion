    <!-- ── Vista tarjetas ────────────────────────────────────────────────── -->
    <div x-show="vista === 'cards'" x-cloak>
      <div x-show="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        <template x-for="i in 8" :key="'csk'+i">
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="h-4 w-2/3 bg-slate-200 rounded animate-pulse mb-3"></div>
            <div class="h-3 w-1/2 bg-slate-200 rounded animate-pulse mb-2"></div>
            <div class="h-3 w-3/4 bg-slate-200 rounded animate-pulse"></div>
          </div>
        </template>
      </div>

      <div x-show="!loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        <template x-for="p in filasFiltradas()" :key="'card'+p.uuid">
          <div class="bg-white rounded-xl shadow-sm border p-4 flex flex-col gap-3"
               :class="p.estado_id === 1 ? 'border-green-200' : 'border-gray-200'">
            <!-- Encabezado tarjeta -->
            <div class="flex items-start justify-between gap-2">
              <div class="min-w-0">
                <p class="font-bold text-slate-900 truncate" x-text="p.modelo + ' ' + p.version"></p>
                <p class="text-xs text-slate-500" x-text="p.modalidad"></p>
              </div>
              <span class="w-4 h-4 rounded-full flex-shrink-0 mt-1" :style="'background:' + estadoColor(p.estado_id)"></span>
            </div>

            <div class="flex items-center justify-between text-xs">
              <span class="text-slate-500">Grupo-Orden</span>
              <template x-if="puedeEditar">
                <button @click="editarPlan(p)" class="text-blue-600 underline font-medium" x-text="p.grupo_orden"></button>
              </template>
              <template x-if="!puedeEditar"><span class="font-medium text-slate-700" x-text="p.grupo_orden"></span></template>
            </div>

            <!-- Montos -->
            <div class="space-y-1 text-xs border-t border-gray-100 pt-2">
              <div class="flex justify-between"><span class="text-slate-500">Cuotas pagadas</span><span class="num" x-text="p.cuotas_pagadas_cantidad"></span></div>
              <template x-if="puedeEditar">
                <div class="flex justify-between"><span class="text-slate-500">Costo</span><span class="num" x-text="money(p.costo)"></span></div>
              </template>
              <template x-if="puedeEditar">
                <div class="flex justify-between"><span class="text-slate-500">Plus</span><span class="num text-red-600" x-text="money(p.plus)"></span></div>
              </template>
              <div class="flex justify-between"><span class="text-slate-500">Valor unidad</span><span class="num" x-text="money(p.valor_unidad)"></span></div>
              <div class="flex justify-between"><span class="text-slate-500">Venta</span><span class="num" x-text="money(p.venta)"></span></div>
              <div class="flex justify-between font-bold"><span class="text-slate-700">Total</span><span class="num text-red-600" x-text="money(p.precio_final)"></span></div>
            </div>

            <!-- Situación -->
            <div class="border-t border-gray-100 pt-2 text-xs">
              <template x-if="p.estado_id === 1">
                <button @click="reservar(p)" class="text-green-600 font-semibold hover:underline"><i class="fas fa-hand-pointer mr-1"></i> Reservar</button>
              </template>
              <template x-if="p.estado_id !== 1">
                <div class="truncate">
                  <span class="text-slate-700" x-text="(p.cliente || '—')"></span>
                  <span class="text-blue-600"> / </span><span class="text-blue-600" x-text="p.usuario_venta"></span>
                  <template x-if="p.usuario_venta_id === userId">
                    <button @click="reservar(p)" class="ml-1 text-slate-400 hover:text-blue-600" title="Editar reserva"><i class="fas fa-pen-to-square"></i></button>
                  </template>
                </div>
              </template>
            </div>
          </div>
        </template>
      </div>

      <div x-show="!loading && filasFiltradas().length === 0" class="text-center text-slate-400 py-16">
        <i class="fas fa-inbox text-3xl mb-2 block"></i> Sin planes para este filtro.
      </div>
    </div>
