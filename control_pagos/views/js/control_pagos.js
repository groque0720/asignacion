/*
 * Componente Alpine del módulo Control de Pagos.
 * Se carga al final del <body> (views/layouts/main.php), antes de que Alpine
 * (cargado con defer) inicialice el x-data="controlPagos(...)" del body.
 */
function controlPagos(puedeEditar) {
  const columnas = [
    { key: 'idreserva', label: 'N.R.',     sortable: true,  width: '64px',  align: 'center', cls: 'text-[11px]' },
    { key: 'nrounidad', label: 'N.U.',     sortable: true,  width: '70px',  align: 'center', cls: 'text-[11px]' },
    { key: 'interno',   label: 'Int.',     sortable: true,  width: '76px',  align: 'center', cls: 'text-[11px]' },
    { key: 'nroorden',  label: 'O.N.',     sortable: true,  width: '104px', align: 'center', cls: 'text-[11px]' },
    { key: 'asesor',    label: 'Asesor',   sortable: true,  width: '96px'  },
    { key: 'cliente',   label: 'Cliente',  sortable: true,  width: '200px' },
    { key: 'modelo',    label: 'Modelo',   sortable: false, width: '190px' },
    { key: 'saldo',     label: 'Saldo',    sortable: false, align: 'right', width: '120px' },
    { key: 'fecres',    label: 'Fec.Res.', sortable: true,  width: '96px'  },
    { key: 'llego',     label: 'Llegó',    sortable: true,  width: '100px' },
    { key: 'fechacanc', label: 'Cancela',  sortable: true,  width: '100px' },
    { key: 'estados',   label: 'Estados',  sortable: false, width: '64px',  icon: 'fa-list-check', align: 'center' },
  ];
  if (puedeEditar) columnas.push({ key: 'adm', label: '', sortable: false, width: '44px', align: 'center' });
  return {
    columnas: columnas,
    puedeEditar: puedeEditar,
    loading: false,
    rows: [],
    total: 0,
    pages: 1,
    page: 1,
    saldoTotal: 0,
    filtros: { suc: 0, est: '11', venta: '', q: '', campo: 'todo', per: 50, sort: '', dir: 'asc' },
    modal: { open: false, saving: false, form: {} },
    popState: { open: false, idreserva: null, x: 0, y: 0, badges: [] },

    campos: [
      { id: 'todo',    nombre: 'Todo' },
      { id: 'nr',      nombre: 'Nro Reserva' },
      { id: 'nu',      nombre: 'Nro Unidad' },
      { id: 'orden',   nombre: 'Nro Orden' },
      { id: 'interno', nombre: 'Interno' },
      { id: 'asesor',  nombre: 'Asesor' },
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
    exportUrl(tipo) {
      const p = new URLSearchParams({
        suc: this.filtros.suc, est: this.filtros.est, venta: this.filtros.venta,
        q: this.filtros.q, campo: this.filtros.campo,
        sort: this.filtros.sort, dir: this.filtros.dir,
      });
      return tipo + '.php?' + p.toString();
    },
    placeholderBusqueda() {
      return {
        nr: 'Número de reserva exacto…', nu: 'Número de unidad exacto…',
        orden: 'Número de orden…', interno: 'Interno…', asesor: 'Nombre del asesor…', cliente: 'Nombre o documento…',
      }[this.filtros.campo] || 'N.R., cliente, documento, unidad, orden, interno…';
    },

    init() {
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

      const resv = (r.anulada == 1)
        ? ['red', 'fa-ban', 'OPERACIÓN ANULADA']
        : ({
            0:  ['slate',  'fa-pen',                  'Reserva sin enviar'],
            1:  ['blue',   'fa-paper-plane',          'Reserva enviada'],
            2:  ['indigo', 'fa-rotate',               'Reserva actualizada'],
            3:  ['amber',  'fa-triangle-exclamation', 'Reserva observada'],
            4:  ['cyan',   'fa-eye',                  'Reserva vista'],
            5:  ['green',  'fa-circle-check',         'Reserva aprobada'],
          }[r.enviada] || ['slate', 'fa-file-lines', 'Reserva']);

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
        make('pago',    pago,   '../estado_cuenta/cuenta.php?IDrecord=' + r.idcliente),
        make('arribo',  arribo, ''),
      ];
    },

    // ── Menú de estados ───────────────────────────────────────────────
    toggleEstados(r, ev) {
      if (this.popState.open && this.popState.idreserva == r.idreserva) {
        this.popState.open = false;
        return;
      }
      const rect = ev.currentTarget.getBoundingClientRect();
      const W = 240, badges = this.badges(r);
      let x = rect.right - W;
      if (x < 8) x = 8;
      // Abre hacia abajo; si no entra, abre hacia arriba.
      const h = badges.length * 36 + 48;
      let y = rect.bottom + 4;
      if (y + h > window.innerHeight - 8) y = Math.max(8, rect.top - h - 4);
      this.popState = { open: true, idreserva: r.idreserva, x, y, badges };
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
    asesorCorto(n) {
      if (!n) return '';
      const i = String(n).indexOf(' ');
      return i === -1 ? n : n.slice(0, i);
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
