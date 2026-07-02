/*
 * Componente Alpine del Dashboard · Descuentos (0km entregados).
 * Carga data.php (JSON) y renderiza KPIs, 4 gráficos (Chart.js) y la tabla detalle.
 * Filtros: año / rango de fechas / sucursal / modelo / vendedor (todos server-side).
 */
function dashboardDescuentos(anioIni) {
  const PALETA = ['#2563eb','#16a34a','#db2777','#f59e0b','#7c3aed','#0891b2','#dc2626','#65a30d','#ea580c','#0d9488'];

  return {
    loading: true,
    filtros: { anio: anioIni, desde: '', hasta: '', idsucursal: 0, idgrupo: 0, idvendedor: 0 },
    opciones: { anios: [], sucursales: [], grupos: [], vendedores: [] },
    kpis: { entregadas: 0, conDescuento: 0, penetracion: 0, montoDescuento: 0, operacionNeta: 0, descPromedio: 0, descPctGlobal: 0 },
    porSucursal: [], porModelo: [], porVendedor: [], tendencia: [], tabla: [],
    // Vista de tabla
    soloDesc: true, busqueda: '', limite: 300,
    charts: {},
    // Por cada gráfico: 'chart' | 'tabla'
    vista: { cSucursal: 'chart', cTendencia: 'chart', cModelo: 'chart', cVendedor: 'chart' },

    verVista(ref, v) {
      this.vista[ref] = v;
      // Al volver al gráfico, reajustar tamaño (el canvas pudo dibujarse oculto).
      if (v === 'chart') this.$nextTick(() => { if (this.charts[ref]) this.charts[ref].resize(); });
    },

    async load() {
      this.loading = true;
      const p = new URLSearchParams({
        anio: this.filtros.anio, desde: this.filtros.desde, hasta: this.filtros.hasta,
        idsucursal: this.filtros.idsucursal, idgrupo: this.filtros.idgrupo, idvendedor: this.filtros.idvendedor,
      });
      try {
        const res = await fetch('data.php?' + p.toString(), { cache: 'no-store' });
        const d = await res.json();
        if (d.error) { alert('Error: ' + d.error); this.loading = false; return; }
        this.opciones = d.opciones;
        this.kpis = d.kpis;
        this.porSucursal = d.porSucursal;
        this.porModelo = d.porModelo;
        this.porVendedor = d.porVendedor;
        this.tendencia = d.tendencia;
        this.tabla = d.tabla;
        this.$nextTick(() => this.renderCharts());
      } catch (e) { alert('No se pudo cargar: ' + e); }
      this.loading = false;
    },

    limpiar() {
      this.filtros = { anio: anioIni, desde: '', hasta: '', idsucursal: 0, idgrupo: 0, idvendedor: 0 };
      this.busqueda = '';
      this.load();
    },

    // ── Tabla (filtrado/orden en el cliente) ──────────────────────────────────
    get tablaFiltrada() {
      let r = this.tabla;
      if (this.soloDesc) r = r.filter(x => x.con_desc === 1);
      const q = this.busqueda.trim().toLowerCase();
      if (q) r = r.filter(x =>
        (x.cliente || '').toLowerCase().includes(q) ||
        (x.vendedor || '').toLowerCase().includes(q) ||
        (x.modelo || '').toLowerCase().includes(q) ||
        (x.version || '').toLowerCase().includes(q) ||
        String(x.nro_unidad).includes(q) ||
        (x.chasis || '').toLowerCase().includes(q));
      return r;
    },
    get tablaVisible() { return this.tablaFiltrada.slice(0, this.limite); },

    pctFila(x) { return x.bruto > 0 ? (100 * x.descuento / x.bruto) : 0; },

    // ── Formato ───────────────────────────────────────────────────────────────
    int(n) { return new Intl.NumberFormat('es-AR', { maximumFractionDigits: 0 }).format(n || 0); },
    money(n) { return '$ ' + this.int(n); }, // espacio irrompible: no parte "$" del número
    moneyShort(n) {
      n = n || 0;
      if (Math.abs(n) >= 1e9) return '$' + (n / 1e9).toFixed(1) + 'MM';
      if (Math.abs(n) >= 1e6) return '$' + (n / 1e6).toFixed(1) + 'M';
      if (Math.abs(n) >= 1e3) return '$' + (n / 1e3).toFixed(0) + 'k';
      return '$' + n;
    },
    pct(n) { return this.int(n) + '%'; },
    pct1(n) { return (n || 0).toFixed(1) + '%'; },
    fecha(f) {
      if (!f) return '';
      const p = String(f).split('-');
      return p.length === 3 ? `${p[2]}/${p[1]}/${p[0]}` : f;
    },

    // ── Charts ──────────────────────────────────────────────────────────────
    nuevoChart(ref, config) {
      if (this.charts[ref]) this.charts[ref].destroy();
      const el = this.$refs[ref];
      if (!el) return;
      this.charts[ref] = new Chart(el, config);
    },

    // eje secundario de penetración (0-100%)
    ejePenetracion() {
      return { position: 'right', beginAtZero: true, suggestedMax: 100,
               grid: { drawOnChartArea: false }, ticks: { callback: v => v + '%' } };
    },

    renderCharts() {
      const self = this;
      const moneyTick = { ticks: { callback: v => self.moneyShort(v) } };

      // 1) Por sucursal: barras monto + línea penetración
      const suc = this.porSucursal;
      this.nuevoChart('cSucursal', {
        type: 'bar',
        data: {
          labels: suc.map(s => s.clave),
          datasets: [
            { type: 'bar', label: 'Descuento', data: suc.map(s => s.monto),
              backgroundColor: '#2563eb', yAxisID: 'y', order: 2 },
            { type: 'line', label: 'Penetración', data: suc.map(s => s.penetracion),
              borderColor: '#db2777', backgroundColor: '#db2777', yAxisID: 'y1', tension: .3, order: 1 },
          ],
        },
        options: { responsive: true, maintainAspectRatio: false,
          plugins: { legend: { labels: { boxWidth: 12 } },
            tooltip: { callbacks: { label: c => c.dataset.yAxisID === 'y1'
              ? 'Penetración: ' + c.raw + '%' : 'Descuento: ' + self.money(c.raw) } } },
          scales: { y: { beginAtZero: true, ...moneyTick }, y1: this.ejePenetracion() } },
      });

      // 2) Por modelo: barras horizontales top 10
      const mod = this.porModelo.slice(0, 10);
      this.nuevoChart('cModelo', {
        type: 'bar',
        data: { labels: mod.map(m => m.clave),
          datasets: [{ label: 'Descuento', data: mod.map(m => m.monto),
            backgroundColor: mod.map((_, i) => PALETA[i % PALETA.length]) }] },
        options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false,
          plugins: { legend: { display: false },
            tooltip: { callbacks: { label: c => self.money(c.raw) + '  ·  ' + mod[c.dataIndex].conDesc + ' u.' } } },
          scales: { x: { beginAtZero: true, ...moneyTick } } },
      });

      // 3) Por vendedor: barras horizontales top 10
      const ven = this.porVendedor.slice(0, 10);
      this.nuevoChart('cVendedor', {
        type: 'bar',
        data: { labels: ven.map(v => v.clave),
          datasets: [{ label: 'Descuento', data: ven.map(v => v.monto),
            backgroundColor: '#16a34a' }] },
        options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false,
          plugins: { legend: { display: false },
            tooltip: { callbacks: {
              title: c => ven[c[0].dataIndex].clave,
              label: c => self.money(c.raw),
              afterLabel: c => 'Penetración: ' + ven[c.dataIndex].penetracion + '%  ·  ' + ven[c.dataIndex].conDesc + '/' + ven[c.dataIndex].entregadas + ' u.' } } },
          scales: { x: { beginAtZero: true, ...moneyTick } } },
      });

      // 4) Tendencia mensual: barras monto + línea penetración
      const t = this.tendencia;
      this.nuevoChart('cTendencia', {
        type: 'bar',
        data: {
          labels: t.map(x => x.etiqueta),
          datasets: [
            { type: 'bar', label: 'Descuento', data: t.map(x => x.monto),
              backgroundColor: '#7c3aed', yAxisID: 'y', order: 2 },
            { type: 'line', label: 'Penetración', data: t.map(x => x.penetracion),
              borderColor: '#f59e0b', backgroundColor: '#f59e0b', yAxisID: 'y1', tension: .3, order: 1 },
          ],
        },
        options: { responsive: true, maintainAspectRatio: false,
          plugins: { legend: { labels: { boxWidth: 12 } },
            tooltip: { callbacks: { label: c => c.dataset.yAxisID === 'y1'
              ? 'Penetración: ' + c.raw + '%' : 'Descuento: ' + self.money(c.raw) } } },
          scales: { y: { beginAtZero: true, ...moneyTick }, y1: this.ejePenetracion() } },
      });
    },
  };
}
