/*
 * Componente Alpine del módulo (renombrar "plantilla" al nombre del módulo nuevo).
 * Se carga al final del <body> (comun/layout.php). Carga data.php (JSON paginado).
 */
function plantilla(puedeEditar) {
  return {
    puedeEditar: puedeEditar,
    loading: true, rows: [], total: 0, pages: 1, page: 1,
    filtros: { q: '', per: 25 },

    async load() {
      this.loading = true;
      const p = new URLSearchParams({ q: this.filtros.q, per: this.filtros.per, page: this.page });
      try {
        const res = await fetch('data.php?' + p.toString(), { cache: 'no-store' });
        const d = await res.json();
        if (d.error) { alert('Error: ' + d.error); this.loading = false; return; }
        this.rows = d.rows; this.total = d.total; this.pages = d.pages;
      } catch (e) { alert('No se pudo cargar: ' + e); }
      this.loading = false;
    },
    resetLoad() { this.page = 1; this.load(); },
    irPagina(n) { if (n < 1 || n > this.pages || n === this.page) return; this.page = n; this.load(); },
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
  };
}
