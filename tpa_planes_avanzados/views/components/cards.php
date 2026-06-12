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
          <div class="relative bg-white rounded-xl shadow-sm border overflow-hidden text-xs"
               :class="p.estado_id === 1 ? 'border-green-200' : 'border-gray-200'">
            <!-- Encabezado tarjeta -->
            <div class="relative px-3 pt-3 pb-2 text-center"
                 :class="p.estado_id === 1 ? 'bg-green-50/60' : ''">
              <span class="absolute top-3 right-3 w-3 h-3 rounded-full" :style="'background:' + estadoColor(p.estado_id)"></span>
              <p class="font-bold text-slate-900 leading-tight truncate" x-text="p.modelo + ' ' + p.version"></p>
              <p class="text-slate-600" x-text="p.modalidad"></p>
            </div>

            <!-- Grupo y Orden -->
            <div class="border-t border-gray-100 px-3 py-2 text-center bg-slate-50">
              <template x-if="puedeEditar">
                <button @click="editarPlan(p)" class="text-blue-600 underline font-medium">Grupo y Orden: <span x-text="p.grupo_orden"></span></button>
              </template>
              <template x-if="!puedeEditar">
                <span class="font-medium text-slate-700">Grupo y Orden: <span x-text="p.grupo_orden"></span></span>
              </template>
            </div>

            <!-- Montos -->
            <div class="px-3 py-2 space-y-1">
              <div class="flex justify-between">
                <span class="text-slate-600">Cuotas Pagas <span class="text-red-600 font-medium" x-text="'(' + p.cuotas_pagadas_cantidad + ')'"></span></span>
                <template x-if="puedeEditar"><span class="num" x-text="money(p.cuotas_pagadas_monto)"></span></template>
                <template x-if="!puedeEditar"><span class="num">&nbsp;</span></template>
              </div>
              <template x-if="puedeEditar">
                <div class="flex justify-between"><span class="text-slate-600">Costo DYV</span><span class="num" x-text="money(p.costo)"></span></div>
              </template>
              <template x-if="puedeEditar">
                <div class="flex justify-between"><span class="text-slate-600">Cesión</span><span class="num" x-text="money(p.cesion)"></span></div>
              </template>
              <div class="flex justify-between font-bold"><span class="text-slate-800">Precio Venta</span><span class="num" x-text="money(p.venta)"></span></div>
              <template x-if="puedeEditar">
                <div class="flex justify-between"><span class="text-red-600">Plus</span><span class="num text-red-600" x-text="money(p.plus)"></span></div>
              </template>
            </div>

            <!-- Integración / Total -->
            <div class="border-t border-gray-100 px-3 py-2 space-y-1">
              <div class="flex justify-between"><span class="text-slate-600">Integración</span><span class="num" x-text="money(p.integracion)"></span></div>
              <div class="flex justify-between"><span class="text-slate-600">Derecho Adjudicación</span><span class="num" x-text="money(p.derecho_adjudicacion)"></span></div>
              <div class="flex justify-between font-bold"><span class="text-slate-800">Total</span><span class="num" x-text="money(p.precio_final)"></span></div>
            </div>

            <!-- Valor / Ahorro -->
            <div class="border-t border-gray-100 px-3 py-2 space-y-1">
              <div class="flex justify-between"><span class="text-red-600">Cuota Promedio</span><span class="num text-red-600" x-text="money(p.cuota_promedio)"></span></div>
              <div class="flex justify-between"><span class="text-slate-600">Valor actual del Plan</span><span class="num" x-text="money(p.cuota_promedio * p.cuotas_pagadas_cantidad)"></span></div>
              <div class="flex justify-between font-bold"><span class="text-blue-600">Ahorro cliente</span><span class="num text-blue-600" x-text="money((p.cuota_promedio * p.cuotas_pagadas_cantidad) - p.venta)"></span></div>
              <div class="flex justify-between"><span class="text-slate-600">Valor de la unidad</span><span class="num" x-text="money(p.valor_unidad)"></span></div>
            </div>

            <!-- Reserva (admin) -->
            <template x-if="puedeEditar">
              <div class="border-t border-gray-100 px-3 py-2">
                <div class="flex justify-between"><span class="text-slate-600">Reserva</span><span class="num" x-text="p.monto_reserva === null ? money(0) : money(p.monto_reserva)"></span></div>
              </div>
            </template>

            <!-- Situación -->
            <div class="border-t border-gray-100 px-3 py-2">
              <template x-if="p.estado_id === 1">
                <div class="text-right">
                  <button @click="reservar(p)" class="text-green-600 font-semibold hover:underline">Reservar</button>
                </div>
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
