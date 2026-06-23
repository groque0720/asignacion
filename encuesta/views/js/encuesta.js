/*
 * Componente Alpine del módulo Encuesta de Satisfacción · 0km (tab Entregas).
 * Se carga al final del <body> (comun/layout.php). Carga data.php (JSON paginado)
 * y genera el link/QR de cada unidad vía token.php.
 */
function encuestaCero(puedeConfigurar, sucursales) {
  const columnas = [
    { key: 'fec_entrega', label: 'Entrega',     sortable: true,  width: '105px' },
    { key: 'cliente',     label: 'Cliente',     sortable: true,  width: '220px' },
    { key: 'grupo',       label: 'Vehículo',    sortable: true,  width: '210px' },
    { key: 'chasis',      label: 'Chasis',      sortable: false, width: '120px' },
    { key: 'nro_orden',   label: 'N° Orden',    sortable: false, width: '110px' },
    { key: 'asesor',      label: 'Asesor',      sortable: true,  width: '150px' },
    { key: 'sucursal',    label: 'Sucursal',    sortable: true,  width: '110px' },
    { key: 'estado',      label: 'Encuesta',    sortable: true,  width: '140px', align: 'center' },
    { key: 'acciones',    label: '',            sortable: false, width: '130px', align: 'center' },
  ];
  return {
    columnas,
    puedeConfigurar,
    loading: false,
    rows: [],
    total: 0,
    pages: 1,
    page: 1,
    kpis: { total: 0, sin_generar: 0, pendientes: 0, completadas: 0, prom: null },
    filtros: { suc: 0, est: '', q: '', per: 50, sort: '', dir: 'desc' },
    modal: { open: false, loading: false, error: '', cliente: '', vehiculo: '', chasis: '', nro_orden: '', asesor: '', sucursal: '', entrega: '', link: '', token: '', copiado: false },

    sucursales: sucursales || [{ id: 0, nombre: 'Todas' }],
    estados: [
      { id: '',  nombre: 'Todas' },
      { id: '0', nombre: 'Sin generar' },
      { id: '1', nombre: 'Pendiente' },
      { id: '2', nombre: 'Completada' },
    ],

    init() { this.load(); },

    async load() {
      this.loading = true;
      const p = new URLSearchParams({
        suc: this.filtros.suc, est: this.filtros.est, q: this.filtros.q,
        per: this.filtros.per, page: this.page,
        sort: this.filtros.sort, dir: this.filtros.dir,
      });
      try {
        const res = await fetch('data.php?' + p.toString(), { cache: 'no-store' });
        const d = await res.json();
        if (d.error) { alert('Error: ' + d.error); this.loading = false; return; }
        this.rows = d.rows;
        this.total = d.total;
        this.pages = d.pages;
        this.kpis = d.kpis;
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
      this.filtros = { suc: 0, est: '', q: '', per: 50, sort: '', dir: 'desc' };
      this.resetLoad();
    },

    // ── Token / link ──────────────────────────────────────────────────────
    async abrirToken(r) {
      const vehiculo = [r.grupo, r.modelo].filter(Boolean).join(' — ');
      this.modal = {
        open: true, loading: true, error: '',
        cliente: r.cliente || '', vehiculo: vehiculo,
        chasis: r.chasis || '', nro_orden: r.nro_orden || '',
        asesor: r.asesor || '', sucursal: r.sucursal || '',
        entrega: this.fecha(r.fec_entrega),
        link: '', token: '', copiado: false,
      };
      try {
        const res = await fetch('token.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: new URLSearchParams({ id: r.id_unidad }),
        });
        const d = await res.json();
        if (!d.ok) { this.modal.error = d.error || 'No se pudo generar el link'; this.modal.loading = false; return; }
        this.modal.link = d.link;
        this.modal.token = d.token;
        // Refleja el nuevo estado en la fila (sin recargar todo)
        if (r.estado === 0) { r.estado = 1; this.kpis.sin_generar--; this.kpis.pendientes++; }
      } catch (e) {
        this.modal.error = 'Error de red: ' + e;
      }
      this.modal.loading = false;
    },
    async copiarLink() {
      try {
        await navigator.clipboard.writeText(this.modal.link);
        this.modal.copiado = true;
        setTimeout(() => { this.modal.copiado = false; }, 1800);
      } catch (e) {
        // Fallback: seleccionar el contenido manualmente
        prompt('Copiá el enlace:', this.modal.link);
      }
    },
    qrUrl(link, size) {
      const s = size || 260;
      return 'https://api.qrserver.com/v1/create-qr-code/?size=' + s + 'x' + s + '&data=' + encodeURIComponent(link);
    },

    // ── Helpers de presentación ───────────────────────────────────────────
    sucNombre() { return (this.sucursales.find(s => s.id == this.filtros.suc) || {}).nombre || ''; },
    estadoBadge(e) {
      return {
        0: { label: 'Sin generar', cls: 'bg-slate-100 text-slate-600' },
        1: { label: 'Pendiente',   cls: 'bg-amber-50 text-amber-700' },
        2: { label: 'Completada',  cls: 'bg-emerald-50 text-emerald-700' },
      }[e] || { label: '—', cls: 'bg-slate-100 text-slate-500' };
    },
    promColor(p) {
      if (p === null) return '#94a3b8';
      if (p >= 8) return '#1e8449';
      if (p >= 6) return '#d68910';
      return '#c0392b';
    },
    desde() { return this.total === 0 ? 0 : (this.page - 1) * this.filtros.per + 1; },
    hasta() { return Math.min(this.page * this.filtros.per, this.total); },
    fecha(f) {
      if (!f) return '';
      const p = String(f).split('-');
      return p.length === 3 ? `${p[2]}/${p[1]}/${p[0]}` : f;
    },
  };
}
