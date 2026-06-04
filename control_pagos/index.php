<?php
@session_start();
if (!isset($_SESSION["autentificado"]) || $_SESSION["autentificado"] !== "SI") {
    header("Location: ../login");
    exit();
}
$fecha_actual = date('d/m/Y');
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Control de Pagos · Derka y Vargas</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter', system-ui, sans-serif; }
    [x-cloak] { display: none !important; }
    .num { font-variant-numeric: tabular-nums; }
    thead th {
      position: sticky;
      top: 0;
      z-index: 10;
      background: #f1f5f9;            /* slate-100: el fondo va en el TH, no en el THEAD */
      box-shadow: inset 0 -1px 0 #e5e7eb;  /* borde inferior que no se pierde al scrollear */
    }
  </style>
</head>
<body class="bg-gray-100 min-h-screen text-slate-800"
      x-data="controlPagos(<?php echo (int)$_SESSION['idperfil']; ?>)" x-init="init()" x-cloak>

  <!-- ── Header ──────────────────────────────────────────────────────────── -->
  <header class="bg-slate-900 text-white shadow-lg sticky top-0 z-30">
    <div class="max-w-[1800px] mx-auto px-6 py-3 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 bg-blue-600 rounded-lg flex items-center justify-center">
          <i class="fas fa-hand-holding-dollar text-sm"></i>
        </div>
        <div>
          <h1 class="text-sm font-bold leading-tight">Control de Pagos</h1>
          <p class="text-slate-400 text-xs">Derka y Vargas S.A.</p>
        </div>
      </div>
      <div class="flex items-center gap-5">
        <div class="text-right">
          <p class="text-[10px] text-slate-500 uppercase tracking-widest leading-none mb-0.5">Fecha</p>
          <p class="text-sm font-semibold"><?php echo $fecha_actual; ?></p>
        </div>
      </div>
    </div>
  </header>

  <main class="max-w-[1800px] mx-auto px-6 py-5 space-y-5">

    <!-- ── KPIs ──────────────────────────────────────────────────────────── -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <p class="text-xs text-slate-500 font-medium">Saldo total (filtro)</p>
        <p class="text-2xl font-bold text-slate-900 num mt-1" :class="saldoTotal < 0 ? 'text-red-600' : ''">
          $ <span x-text="money(saldoTotal)"></span>
        </p>
      </div>
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <p class="text-xs text-slate-500 font-medium">Operaciones</p>
        <p class="text-2xl font-bold text-slate-900 num mt-1" x-text="total"></p>
      </div>
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <p class="text-xs text-slate-500 font-medium">Sucursal</p>
        <p class="text-lg font-semibold text-slate-900 mt-1.5" x-text="sucNombre()"></p>
      </div>
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <p class="text-xs text-slate-500 font-medium">Estado</p>
        <p class="text-lg font-semibold text-slate-900 mt-1.5" x-text="estNombre()"></p>
      </div>
    </div>

    <!-- ── Toolbar de filtros ────────────────────────────────────────────── -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
      <div class="flex flex-wrap items-end gap-4">
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Sucursal</label>
          <select x-model="filtros.suc" @change="resetLoad()" :disabled="filtros.q.length > 0"
                  class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none disabled:bg-gray-100 disabled:text-gray-400">
            <template x-for="s in sucursales" :key="s.id">
              <option :value="s.id" x-text="s.nombre"></option>
            </template>
          </select>
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Estado</label>
          <select x-model="filtros.est" @change="resetLoad()" :disabled="filtros.q.length > 0"
                  class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none disabled:bg-gray-100 disabled:text-gray-400">
            <template x-for="e in estados" :key="e.id">
              <option :value="e.id" x-text="e.nombre"></option>
            </template>
          </select>
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Buscar por</label>
          <select x-model="filtros.campo" @change="filtros.q && resetLoad()"
                  class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
            <template x-for="c in campos" :key="c.id">
              <option :value="c.id" x-text="c.nombre"></option>
            </template>
          </select>
        </div>
        <div class="flex-1 min-w-[220px]">
          <label class="block text-xs font-medium text-slate-500 mb-1">Buscar</label>
          <div class="relative">
            <i class="fas fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input type="text" x-model="filtros.q" @input.debounce.400ms="resetLoad()"
                   :placeholder="placeholderBusqueda()"
                   class="w-full text-sm border border-gray-300 rounded-lg pl-9 pr-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
          </div>
          <p x-show="filtros.q.length > 0" class="text-xs text-amber-600 mt-1">
            <i class="fas fa-circle-info"></i> Buscando en todas las sucursales y estados.
          </p>
        </div>
        <button @click="resetFiltros()"
                class="text-sm text-slate-600 hover:text-slate-900 border border-gray-300 rounded-lg px-3 py-2 hover:bg-gray-50">
          <i class="fas fa-rotate-left mr-1"></i> Limpiar
        </button>
      </div>
    </div>

    <!-- ── Tabla ─────────────────────────────────────────────────────────── -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto" style="max-height: calc(100vh - 360px);">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wide border-b border-gray-200">
            <tr>
              <template x-for="c in columnas" :key="c.key">
                <th class="px-3 py-2.5 font-semibold whitespace-nowrap select-none"
                    :class="[c.align === 'right' ? 'text-right' : 'text-left', c.sortable ? 'cursor-pointer hover:text-slate-900' : '']"
                    @click="c.sortable && ordenar(c.key)">
                  <span x-text="c.label"></span>
                  <i x-show="filtros.sort === c.key" class="fas ml-1 text-[10px]"
                     :class="filtros.dir === 'asc' ? 'fa-arrow-up' : 'fa-arrow-down'"></i>
                </th>
              </template>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <template x-for="r in rows" :key="r.idreserva">
              <tr class="hover:bg-blue-50/40 transition-colors">
                <td class="px-3 py-2 text-slate-500" x-text="r.idreserva"></td>
                <td class="px-3 py-2 font-medium text-slate-900" x-text="r.nrounidad"></td>
                <td class="px-3 py-2 text-slate-500" x-text="r.interno"></td>
                <td class="px-3 py-2 text-slate-500" x-text="r.nroorden"></td>
                <td class="px-3 py-2" x-text="r.asesor"></td>
                <td class="px-3 py-2">
                  <div class="font-medium text-slate-900" x-text="r.cliente"></div>
                  <div class="text-xs text-blue-600" x-text="'(' + r.tipo_venta + ')'"></div>
                </td>
                <td class="px-3 py-2 text-slate-600" x-text="r.modelo"></td>
                <td class="px-3 py-2 text-right num font-semibold"
                    :class="r.saldo == 0 ? 'text-emerald-700' : (r.saldo < 0 ? 'text-red-600' : 'text-slate-900')">
                  <span x-text="money(r.saldo)"></span>
                </td>
                <td class="px-3 py-2 whitespace-nowrap text-slate-500" x-text="fecha(r.fecres)"></td>
                <td class="px-3 py-2 whitespace-nowrap">
                  <span x-show="r.llego" x-text="fecha(r.llego)"
                        class="inline-block px-2 py-0.5 rounded text-xs font-medium"
                        :class="arriboDemorado(r.llego) ? 'bg-red-100 text-red-700 font-bold italic' : 'bg-emerald-50 text-emerald-700'"
                        :title="arriboDemorado(r.llego) ? 'Arribó hace más de 10 días' : ''"></span>
                  <span x-show="!r.llego" class="text-slate-300">—</span>
                </td>
                <td class="px-3 py-2 whitespace-nowrap">
                  <span x-show="r.fechacanc" x-text="fecha(r.fechacanc)"
                        class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-amber-50 text-amber-700"></span>
                  <span x-show="!r.fechacanc" class="text-slate-300">—</span>
                </td>
                <td class="px-3 py-2">
                  <div class="flex items-center gap-1.5">
                    <template x-for="b in badges(r)" :key="b.key">
                      <a :href="b.href || null" :target="b.href ? '_blank' : null" :title="b.title"
                         class="w-6 h-6 rounded-md flex items-center justify-center text-xs flex-shrink-0 transition"
                         :class="b.href ? 'hover:ring-2 hover:ring-blue-300 hover:scale-110' : 'cursor-default'"
                         :style="`background:${b.bg};color:${b.fg};border:1px solid ${b.fg}40`">
                        <i :class="b.icon"></i>
                      </a>
                    </template>
                  </div>
                </td>
                <template x-if="perfil == 9">
                  <td class="px-3 py-2 text-center">
                    <button @click="abrirEdicion(r)" title="Editar"
                            class="w-7 h-7 rounded-md text-slate-400 hover:text-blue-600 hover:bg-blue-50">
                      <i class="fas fa-pen-to-square"></i>
                    </button>
                  </td>
                </template>
              </tr>
            </template>

            <tr x-show="!loading && rows.length === 0">
              <td :colspan="columnas.length" class="px-3 py-10 text-center text-slate-400">
                <i class="fas fa-inbox text-2xl mb-2 block"></i> Sin resultados para este filtro.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Loading -->
      <div x-show="loading" class="py-10 text-center text-slate-400">
        <i class="fas fa-circle-notch fa-spin text-2xl"></i>
        <p class="text-sm mt-2">Cargando…</p>
      </div>

      <!-- ── Paginación ──────────────────────────────────────────────────── -->
      <div class="flex items-center justify-between px-4 py-3 border-t border-gray-200 bg-slate-50 text-sm">
        <div class="text-slate-500">
          <span x-text="desde()"></span>–<span x-text="hasta()"></span> de <span x-text="total"></span>
        </div>
        <div class="flex items-center gap-1">
          <button @click="irPagina(1)" :disabled="page === 1"
                  class="px-2.5 py-1.5 rounded-lg border border-gray-300 bg-white disabled:opacity-40 hover:bg-gray-50">
            <i class="fas fa-angles-left text-xs"></i>
          </button>
          <button @click="irPagina(page - 1)" :disabled="page === 1"
                  class="px-2.5 py-1.5 rounded-lg border border-gray-300 bg-white disabled:opacity-40 hover:bg-gray-50">
            <i class="fas fa-angle-left text-xs"></i>
          </button>
          <span class="px-3 text-slate-600">Pág. <strong x-text="page"></strong> / <span x-text="pages || 1"></span></span>
          <button @click="irPagina(page + 1)" :disabled="page >= pages"
                  class="px-2.5 py-1.5 rounded-lg border border-gray-300 bg-white disabled:opacity-40 hover:bg-gray-50">
            <i class="fas fa-angle-right text-xs"></i>
          </button>
          <button @click="irPagina(pages)" :disabled="page >= pages"
                  class="px-2.5 py-1.5 rounded-lg border border-gray-300 bg-white disabled:opacity-40 hover:bg-gray-50">
            <i class="fas fa-angles-right text-xs"></i>
          </button>
          <select x-model.number="filtros.per" @change="resetLoad()"
                  class="ml-2 border border-gray-300 rounded-lg px-2 py-1.5 bg-white">
            <option :value="50">50</option>
            <option :value="100">100</option>
            <option :value="200">200</option>
          </select>
        </div>
      </div>
    </div>

    <p class="text-xs text-slate-400 text-center">
      Módulo nuevo (en construcción). El módulo original sigue intacto en <code>ventas/web/control_pagos_clientes.php</code>.
    </p>
  </main>

  <!-- ── Modal de edición ──────────────────────────────────────────────────── -->
  <div x-show="modal.open" x-cloak
       class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4"
       @keydown.escape.window="modal.open = false">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden"
         @click.outside="modal.open = false">
      <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 bg-slate-50">
        <div>
          <h3 class="text-sm font-bold text-slate-900">Editar operación</h3>
          <p class="text-xs text-slate-500" x-text="modal.form.cliente"></p>
        </div>
        <button @click="modal.open = false" class="text-slate-400 hover:text-slate-700">
          <i class="fas fa-xmark text-lg"></i>
        </button>
      </div>

      <div class="p-5 grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Nro Unidad</label>
          <input type="number" x-model="modal.form.nrounidad"
                 class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Interno</label>
          <input type="text" x-model="modal.form.interno"
                 class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Nro Orden</label>
          <input type="text" x-model="modal.form.nroorden"
                 class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Fec. de Arribo</label>
          <input type="date" x-model="modal.form.arribo"
                 class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Fec. Est. Cancelación</label>
          <input type="date" x-model="modal.form.cancela"
                 class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Fecha de Entrega</label>
          <input type="date" x-model="modal.form.entrega"
                 class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
        </div>
        <div class="col-span-2">
          <label class="block text-xs font-medium text-slate-500 mb-1">Observación</label>
          <textarea x-model="modal.form.obs" rows="3"
                    class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"></textarea>
        </div>
      </div>

      <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-gray-200 bg-slate-50">
        <button @click="modal.open = false"
                class="text-sm text-slate-600 border border-gray-300 rounded-lg px-4 py-2 hover:bg-gray-100">
          Cancelar
        </button>
        <button @click="guardar()" :disabled="modal.saving"
                class="text-sm text-white bg-blue-600 hover:bg-blue-700 rounded-lg px-4 py-2 disabled:opacity-50 flex items-center gap-2">
          <i class="fas" :class="modal.saving ? 'fa-circle-notch fa-spin' : 'fa-floppy-disk'"></i>
          <span x-text="modal.saving ? 'Guardando…' : 'Guardar'"></span>
        </button>
      </div>
    </div>
  </div>

  <script>
    function controlPagos(perfil) {
      return {
        perfil: perfil,
        loading: false,
        rows: [],
        total: 0,
        pages: 1,
        page: 1,
        saldoTotal: 0,
        filtros: { suc: 0, est: '11', venta: '', q: '', campo: 'todo', per: 50, sort: '', dir: 'asc' },
        modal: { open: false, saving: false, form: {} },

        campos: [
          { id: 'todo',    nombre: 'Todo' },
          { id: 'nr',      nombre: 'Nro Reserva' },
          { id: 'nu',      nombre: 'Nro Unidad' },
          { id: 'orden',   nombre: 'Nro Orden' },
          { id: 'interno', nombre: 'Interno' },
          { id: 'cliente', nombre: 'Cliente' },
        ],

        sucursales: [
          { id: 0, nombre: 'Todas' }, { id: 1, nombre: 'Resistencia' },
          { id: 2, nombre: 'Sáenz Peña' }, { id: 3, nombre: 'Villa Ángela' },
          { id: 4, nombre: 'Charata' },
        ],
        estados: [
          { id: '1',  nombre: 'Llegadas Todas' },
          { id: '11', nombre: 'Llegadas No Canceladas' },
          { id: '12', nombre: 'Llegadas Canceladas' },
          { id: '2',  nombre: 'No Llegadas' },
          { id: '21', nombre: 'No Llegadas Canceladas' },
          { id: '3',  nombre: 'Llegadas +10 días' },
          { id: '4',  nombre: 'Cancelación Vencida' },
        ],
        ventas: ['Convencional','Usado Certificado','Reventa','Plan Dueño','Plan Empleado',
                 'Especial','Plan de Ahorro','Plan Adjudicado','Plan Avanzado','Reg. Discapacidad'],

        columnas: [
          { key: 'idreserva', label: 'N.R.',     sortable: true  },
          { key: 'nrounidad', label: 'N.U.',     sortable: true  },
          { key: 'interno',   label: 'Interno',  sortable: true  },
          { key: 'nroorden',  label: 'Nro Orden',sortable: true  },
          { key: 'asesor',    label: 'Asesor',   sortable: true  },
          { key: 'cliente',   label: 'Cliente',  sortable: true  },
          { key: 'modelo',    label: 'Modelo',   sortable: false },
          { key: 'saldo',     label: 'Saldo',    sortable: false, align: 'right' },
          { key: 'fecres',    label: 'Fec.Res.', sortable: true  },
          { key: 'llego',     label: 'Llegó',    sortable: true  },
          { key: 'fechacanc', label: 'Cancela',  sortable: true  },
          { key: 'estados',   label: 'Estados',  sortable: false },
        ],

        async load() {
          this.loading = true;
          const p = new URLSearchParams({
            suc: this.filtros.suc, est: this.filtros.est, venta: this.filtros.venta,
            q: this.filtros.q, campo: this.filtros.campo, per: this.filtros.per, page: this.page,
            sort: this.filtros.sort, dir: this.filtros.dir,
          });
          try {
            const res = await fetch('data.php?' + p.toString(), { cache: 'no-store' });
            const d = await res.json();
            if (d.error) { alert('Error: ' + d.error); this.loading = false; return; }
            this.rows = d.rows;
            this.total = d.total;
            this.pages = d.pages;
            this.saldoTotal = d.saldo_total;
          } catch (e) {
            alert('No se pudo cargar: ' + e);
          }
          this.loading = false;
        },
        resetLoad() { this.page = 1; this.load(); },
        irPagina(n) {
          if (n < 1 || n > this.pages || n === this.page) return;
          this.page = n; this.load();
        },
        ordenar(key) {
          if (this.filtros.sort === key) {
            this.filtros.dir = this.filtros.dir === 'asc' ? 'desc' : 'asc';
          } else {
            this.filtros.sort = key; this.filtros.dir = 'asc';
          }
          this.resetLoad();
        },
        resetFiltros() {
          this.filtros = { suc: 0, est: '11', venta: '', q: '', campo: 'todo', per: 50, sort: '', dir: 'asc' };
          this.resetLoad();
        },
        placeholderBusqueda() {
          return {
            nr: 'Número de reserva exacto…', nu: 'Número de unidad exacto…',
            orden: 'Número de orden…', interno: 'Interno…', cliente: 'Nombre o documento…',
          }[this.filtros.campo] || 'N.R., cliente, documento, unidad, orden, interno…';
        },

        init() {
          if (this.perfil == 9) this.columnas.push({ key: 'adm', label: '', sortable: false });
          this.load();
        },

        // ── Badges de estado ──────────────────────────────────────────────
        // Cada estado tiene su PROPIO ícono + color, para distinguirlos de un vistazo.
        badges(r) {
          const C = {
            slate:  ['#f1f5f9', '#64748b'], blue:   ['#dbeafe', '#1d4ed8'],
            indigo: ['#e0e7ff', '#4338ca'], cyan:   ['#cffafe', '#0e7490'],
            amber:  ['#fde68a', '#b45309'], green:  ['#bbf7d0', '#047857'],
            red:    ['#fecaca', '#b91c1c'],
          };
          // s = [color, icono, titulo]
          const make = (key, s, href) =>
            ({ key, bg: C[s[0]][0], fg: C[s[0]][1], icon: 'fas ' + s[1], title: s[2], href: href || '' });

          const resv = {
            0:  ['slate',  'fa-pen',                  'Reserva sin enviar'],
            1:  ['blue',   'fa-paper-plane',          'Reserva enviada'],
            2:  ['indigo', 'fa-rotate',               'Reserva actualizada'],
            3:  ['amber',  'fa-triangle-exclamation', 'Reserva observada'],
            4:  ['cyan',   'fa-eye',                  'Reserva vista'],
            5:  ['green',  'fa-circle-check',         'Reserva aprobada'],
          }[r.enviada] || ['slate', 'fa-file-lines', 'Reserva'];

          const fact = {
            0:  ['slate',  'fa-receipt',              'Sin facturar'],
            1:  ['blue',   'fa-paper-plane',          'Facturación enviada'],
            2:  ['amber',  'fa-triangle-exclamation', 'Facturación observada'],
            3:  ['green',  'fa-circle-check',         'Facturación OK'],
          }[r.factura_estado] || ['slate', 'fa-receipt', 'Sin facturar'];

          const cred = {
            0:  ['slate',  'fa-minus',                'Sin crédito'],
            20: ['blue',   'fa-file-circle-xmark',    'Crédito sin papeles'],
            1:  ['blue',   'fa-inbox',                'Crédito recibido'],
            2:  ['blue',   'fa-paper-plane',          'Crédito enviado'],
            22: ['indigo', 'fa-magnifying-glass',     'Crédito en análisis'],
            3:  ['amber',  'fa-triangle-exclamation', 'Crédito observado'],
            4:  ['red',    'fa-circle-xmark',         'Crédito rechazado'],
            5:  ['cyan',   'fa-thumbs-up',            'Crédito pre-aprobado'],
            6:  ['green',  'fa-circle-check',         'Crédito aprobado'],
            66: ['amber',  'fa-circle-check',         'Crédito aprobado observado'],
            7:  ['green',  'fa-sack-dollar',          'Crédito liquidado'],
            70: ['green',  'fa-sack-dollar',          'Crédito liquidado'],
          }[r.credito_estado] || ['slate', 'fa-landmark', 'Sin crédito'];

          const pago = {
            0:  ['slate',  'fa-dollar-sign',          'Sin pagos'],
            1:  ['blue',   'fa-coins',                'Con seña'],
            2:  ['indigo', 'fa-money-bill-wave',      'Pagos a cuenta'],
            3:  ['green',  'fa-circle-check',         'Cancelada'],
          }[r.estadopago] || ['slate', 'fa-dollar-sign', 'Sin pagos'];

          const arribo = r.tiene_arribo
            ? ['green', 'fa-car-on',  'Con arribo']
            : ['slate', 'fa-car',     'Sin arribo'];

          const base = '../ventas/web/';
          return [
            make('reserva', resv,   base + 'reserva.php?IDrecord=' + r.idreserva),
            make('factura', fact,   base + 'facturacion.php?IDrecord=' + r.idreserva),
            make('credito', cred,   r.idcredito ? (base + 'credito.php?IDrecord=' + r.idcredito) : ''),
            make('pago',    pago,   base + 'pago.php?IDrecord=' + r.idcliente),
            make('arribo',  arribo, ''),
          ];
        },

        // ── Edición ───────────────────────────────────────────────────────
        abrirEdicion(r) {
          this.modal.form = {
            idreserva: r.idreserva,
            cliente:   r.cliente || '',
            nrounidad: r.nrounidad || '',
            interno:   r.interno || '',
            nroorden:  r.nroorden || '',
            arribo:    r.llego || '',
            cancela:   r.fechacanc || '',
            entrega:   r.fechaentrega || '',
            obs:       r.obs || '',
          };
          this.modal.open = true;
        },

        async guardar() {
          const f = this.modal.form;
          const nu = parseInt(f.nrounidad);
          if (!nu || nu < 300) { alert('Ingresá un número de unidad válido (≥ 300).'); return; }
          this.modal.saving = true;
          const body = new URLSearchParams({
            id: f.idreserva, nrou: f.nrounidad, nroint: f.interno || '',
            no: f.nroorden || '', fecarr: f.arribo || '', feccan: f.cancela || '',
            fecent: f.entrega || '', obs: f.obs || '',
          });
          try {
            const res = await fetch('guardar.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
              body,
            });
            const d = await res.json();
            if (!d.ok) { alert('Error al guardar: ' + (d.error || '')); this.modal.saving = false; return; }
            const r = this.rows.find(x => x.idreserva == f.idreserva);
            if (r) {
              r.nrounidad = f.nrounidad; r.interno = f.interno; r.nroorden = f.nroorden;
              r.llego = f.arribo || null; r.fechacanc = f.cancela || null;
              r.fechaentrega = f.entrega || null; r.obs = f.obs;
              r.tiene_arribo = !!f.arribo;
            }
            this.modal.open = false;
          } catch (e) {
            alert('No se pudo guardar: ' + e);
          }
          this.modal.saving = false;
        },

        sucNombre() { return (this.sucursales.find(s => s.id == this.filtros.suc) || {}).nombre || ''; },
        estNombre() { return (this.estados.find(e => e.id == this.filtros.est) || {}).nombre || ''; },
        desde() { return this.total === 0 ? 0 : (this.page - 1) * this.filtros.per + 1; },
        hasta() { return Math.min(this.page * this.filtros.per, this.total); },
        money(n) {
          return new Intl.NumberFormat('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(n || 0);
        },
        fecha(f) {
          if (!f) return '';
          const p = String(f).split('-');
          return p.length === 3 ? `${p[2]}/${p[1]}/${p[0]}` : f;
        },
        arriboDemorado(f) {
          if (!f) return false;
          const d = new Date(String(f) + 'T00:00:00');
          if (isNaN(d)) return false;
          const dias = Math.floor((Date.now() - d.getTime()) / 86400000);
          return dias > 10;
        },
      };
    }
  </script>
</body>
</html>
