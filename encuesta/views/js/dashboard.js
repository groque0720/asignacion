/* Resultados · Dashboard 0km (Alpine + Chart.js). */
function dashboardCero(sucursales) {
  return {
    loading: true,
    kpis: { completadas: 0, generadas: 0, prom: null, tasa: null },
    tabla: [],
    niveles: [],
    charts: {},
    filtros: { suc: 0, desde: '', hasta: '', asesor: '', anio: new Date().getFullYear(), mes: new Date().getMonth() + 1, grupo: 0, modelo: 0, area: 0 },
    sucursales: sucursales || [{ id: 0, nombre: 'Todas' }],
    // El año actual ya presente para que el <select> no se resetee en el primer render
    // (si no, x-model no encuentra la opción y el navegador fuerza "Todos").
    opciones: { anios: [new Date().getFullYear()], grupos: [], modelos: [], areas: [] },
    meses: [
      { id: 1, nombre: 'Enero' }, { id: 2, nombre: 'Febrero' }, { id: 3, nombre: 'Marzo' },
      { id: 4, nombre: 'Abril' }, { id: 5, nombre: 'Mayo' }, { id: 6, nombre: 'Junio' },
      { id: 7, nombre: 'Julio' }, { id: 8, nombre: 'Agosto' }, { id: 9, nombre: 'Septiembre' },
      { id: 10, nombre: 'Octubre' }, { id: 11, nombre: 'Noviembre' }, { id: 12, nombre: 'Diciembre' },
    ],

    init() { this.load(); },

    async load() {
      this.loading = true;
      const p = new URLSearchParams(this.filtros);
      try {
        const d = await (await fetch('dashboard_data.php?' + p.toString(), { cache: 'no-store' })).json();
        if (d.error) { alert(d.error); this.loading = false; return; }
        this.kpis = d.kpis;
        this.tabla = d.tabla;
        this.niveles = d.por_nivel;
        if (d.opciones) {
          this.opciones = d.opciones;
          const cy = new Date().getFullYear();
          if (!this.opciones.anios.includes(cy)) this.opciones.anios.unshift(cy);
        }
        this.$nextTick(() => this.pintar(d));
      } catch (e) { alert('No se pudo cargar: ' + e); }
      this.loading = false;
    },
    limpiar() { this.filtros = { suc: 0, desde: '', hasta: '', asesor: '', anio: new Date().getFullYear(), mes: new Date().getMonth() + 1, grupo: 0, modelo: 0, area: 0 }; this.load(); },

    pct(n) {
      const tot = this.niveles.reduce((s, x) => s + x.n, 0);
      return tot > 0 ? +(n / tot * 100).toFixed(1) : 0;
    },

    pintar(d) {
      // Registrar el plugin de etiquetas de valor una sola vez (igual que el legacy).
      if (typeof Chart !== 'undefined' && window.ChartDataLabels && !this._dlReg) {
        Chart.register(window.ChartDataLabels);
        this._dlReg = true;
      }
      const f1 = v => (v === null || v === undefined || v === '') ? '' : (+v).toFixed(1);

      this.dibujar('cMes', 'bar', {
        labels: d.por_mes.map(m => m.mes),
        datasets: [
          { type: 'line', label: 'Promedio', data: d.por_mes.map(m => m.prom), borderColor: '#2563eb', backgroundColor: 'rgba(37,99,235,.1)', fill: true, tension: .3, yAxisID: 'y',
            datalabels: { align: 'top', anchor: 'end', color: '#2563eb', font: { size: 10, weight: 'bold' }, formatter: f1 } },
          { type: 'bar', label: 'Respuestas', data: d.por_mes.map(m => m.n), backgroundColor: 'rgba(148,163,184,.45)', yAxisID: 'y1',
            datalabels: { display: false } },
        ],
      }, { scales: { y: { min: 0, max: 10, position: 'left' }, y1: { min: 0, position: 'right', grid: { drawOnChartArea: false }, ticks: { precision: 0 } } } });

      this.dibujar('cSuc', 'bar', {
        labels: d.por_sucursal.map(s => s.sucursal || this.sucNombre(s.id_sucursal)),
        datasets: [{ label: 'Promedio', data: d.por_sucursal.map(s => s.prom), backgroundColor: '#0ea5e9', borderRadius: 6 }],
      }, { indexAxis: 'y', scales: { x: { min: 0, max: 10 } }, plugins: { legend: { display: false },
            datalabels: { anchor: 'end', align: 'start', offset: 6, color: '#fff', font: { size: 11, weight: 'bold' }, formatter: f1 } } });

      this.dibujar('cNivel', 'doughnut', {
        labels: d.por_nivel.map(n => n.nombre),
        datasets: [{ data: d.por_nivel.map(n => n.n), backgroundColor: d.por_nivel.map(n => n.color) }],
      }, { plugins: { legend: { position: 'right', labels: { boxWidth: 12, font: { size: 11 } } },
            datalabels: { color: '#fff', font: { size: 12, weight: 'bold' },
              formatter: (val, ctx) => { const t = ctx.dataset.data.reduce((a, b) => a + b, 0); return t > 0 && val > 0 ? Math.round(val / t * 100) + '%' : ''; } } } });

      this.dibujar('cArea', 'bar', {
        labels: d.por_area.map(a => a.nombre),
        datasets: [{ label: 'Promedio', data: d.por_area.map(a => a.prom), backgroundColor: d.por_area.map(a => a.color || '#607d8b'), borderRadius: 6 }],
      }, { indexAxis: 'y', scales: { x: { min: 0, max: 10 } }, plugins: { legend: { display: false },
            datalabels: { anchor: 'end', align: 'start', offset: 6, color: '#fff', font: { size: 11, weight: 'bold' }, formatter: f1 } } });

      this.dibujar('cAsesor', 'bar', {
        labels: d.top_asesores.map(a => a.asesor),
        datasets: [{ label: 'Promedio', data: d.top_asesores.map(a => a.prom), backgroundColor: '#7c3aed', borderRadius: 6 }],
      }, { scales: { y: { min: 0, max: 10 } }, plugins: { legend: { display: false },
            datalabels: { anchor: 'end', align: 'start', offset: 6, color: '#fff', font: { size: 11, weight: 'bold' }, formatter: f1 } } });
    },
    dibujar(ref, type, data, opts) {
      const el = this.$refs[ref];
      if (!el || typeof Chart === 'undefined') return;
      if (this.charts[ref]) this.charts[ref].destroy();
      this.charts[ref] = new Chart(el, { type, data, options: Object.assign({ responsive: true, maintainAspectRatio: false, animation: false }, opts || {}) });
    },

    sucNombre(id) { return (this.sucursales.find(s => s.id == id) || {}).nombre || ('Suc. ' + id); },
    promColor(p) { if (p === null) return '#94a3b8'; if (p >= 8) return '#1e8449'; if (p >= 6) return '#d68910'; return '#c0392b'; },
    fechaHora(f) {
      if (!f) return '—';
      const s = String(f).replace(' ', 'T'); const d = new Date(s);
      if (isNaN(d)) { const p = String(f).split(' ')[0].split('-'); return p.length === 3 ? `${p[2]}/${p[1]}/${p[0]}` : f; }
      const z = n => String(n).padStart(2, '0');
      return `${z(d.getDate())}/${z(d.getMonth() + 1)}/${d.getFullYear()}`;
    },
  };
}
