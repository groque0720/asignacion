    <!-- ── Grid de seguimiento ───────────────────────────────────────────── -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <div class="us-table-wrapper">

        <!-- Cargando -->
        <div x-show="loading" class="p-10 text-center text-slate-400">
          <i class="fas fa-circle-notch fa-spin text-2xl mb-2 block"></i> Cargando…
        </div>

        <!-- Sin ítems -->
        <div x-show="!loading && items.length === 0" class="p-10 text-center text-slate-400">
          No hay ítems configurados.
          <template x-if="esAdmin">
            <button @click="abrirAdmin()" class="text-blue-600 hover:underline ml-1">Agregar ítem</button>
          </template>
        </div>

        <!-- Sin filas -->
        <div x-show="!loading && items.length > 0 && usadosFiltrados().length === 0"
             class="p-10 text-center text-slate-400">
          No hay usados que coincidan con los filtros aplicados.
        </div>

        <table x-show="!loading && items.length > 0 && usadosFiltrados().length > 0"
               class="us-table dv-table" x-cloak>
          <thead>
            <tr>
              <th class="us-sticky us-col-interno">Int.</th>
              <th class="us-sticky us-col-vehiculo">Vehículo</th>
              <th class="us-sticky us-col-dominio">Dominio</th>
              <th class="us-sticky us-col-asesor">Asesor toma</th>
              <th class="us-col-info">Recep. / Ant.</th>
              <th class="us-col-reserva">Reserva</th>
              <th class="us-col-suc">Suc.</th>
              <template x-for="it in items" :key="it.id_item">
                <th class="us-col-item" :title="it.descripcion || it.nombre" x-text="it.nombre"></th>
              </template>
              <th class="us-col-estado us-sticky-right">Estado</th>
            </tr>
          </thead>
          <tbody>
            <template x-for="u in usadosFiltrados()" :key="u.id_unidad">
              <tr class="us-row" :class="u.row_hl ? ('us-row-' + u.row_hl) : ''">
                <td class="us-sticky us-col-interno" x-text="u.interno"></td>
                <td class="us-sticky us-col-vehiculo">
                  <div class="us-veh">
                    <span class="us-veh-name" x-text="u.vehiculo" :title="u.vehiculo"></span>
                    <template x-if="u.uct">
                      <span class="us-badge-uct" :class="u.uct.class" x-text="u.uct.label"></span>
                    </template>
                  </div>
                </td>
                <td class="us-sticky us-col-dominio"><div class="us-trunc" x-text="u.dominio" :title="u.dominio"></div></td>
                <td class="us-sticky us-col-asesor"><div class="us-trunc" x-text="asesorCorto(u.asesor_toma)" :title="u.asesor_toma"></div></td>

                <td class="us-col-info us-center">
                  <span x-text="u.recepcion"></span>
                  <span x-show="u.ant >= 50" class="us-badge-ant" x-text="'(' + u.ant + 'd)'"></span>
                </td>
                <td class="us-col-reserva us-center">
                  <span class="us-badge-res" :class="u.reserva.class" x-text="u.reserva.label"></span>
                </td>
                <td class="us-col-suc us-center" x-text="u.sucursal"></td>

                <template x-for="it in items" :key="u.id_unidad + '-' + it.id_item">
                  <td class="us-col-item us-celda" :class="u.celdas[it.id_item].class"
                      @click="abrirCelda(u, it)"
                      :title="u.celdas[it.id_item].observacion">
                    <span class="us-celda-icon" x-text="u.celdas[it.id_item].icon"></span>
                    <span x-show="u.celdas[it.id_item].tiene_arch" class="us-celda-clip" title="Tiene archivos adjuntos">
                      <i class="fas fa-paperclip"></i>
                    </span>
                  </td>
                </template>

                <td class="us-col-estado us-sticky-right">
                  <span class="us-badge-estado" :class="u.estado_gral.class">
                    <span x-text="u.estado_gral.icon"></span> <span x-text="u.estado_gral.label"></span>
                  </span>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>
    </div>
