/*
 * Componente Alpine de la lista de clientes activos (index.php).
 * Carga lista_data.php (JSON paginado) y enlaza cada fila a cuenta.php?IDrecord=.
 */
function lista(idsuc) {
  return {
    loading: true, rows: [], total: 0, pages: 1, page: 1,
    filtros: { suc: idsuc, q: '', per: 25 },
    sucursales: [
      { id: 0, nombre: 'Todas' }, { id: 1, nombre: 'Resistencia' },
      { id: 2, nombre: 'Sáenz Peña' }, { id: 3, nombre: 'Villa Ángela' }, { id: 4, nombre: 'Charata' },
    ],

    async load() {
      this.loading = true;
      const p = new URLSearchParams({ suc: this.filtros.suc, q: this.filtros.q, per: this.filtros.per, page: this.page });
      try {
        const res = await fetch('lista_data.php?' + p.toString(), { cache: 'no-store' });
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

    cred(r) {
      const C = { slate:['#f1f5f9','#64748b'], blue:['#dbeafe','#1d4ed8'], indigo:['#e0e7ff','#4338ca'],
                  cyan:['#cffafe','#0e7490'], amber:['#fde68a','#b45309'], green:['#bbf7d0','#047857'], red:['#fecaca','#b91c1c'] };
      const s = {
        0:['slate','fa-minus','Sin crédito'], 20:['blue','fa-file-circle-xmark','Sin papeles'],
        1:['blue','fa-inbox','Recibido'], 2:['blue','fa-paper-plane','Enviado'], 22:['indigo','fa-magnifying-glass','En análisis'],
        3:['amber','fa-triangle-exclamation','Observado'], 4:['red','fa-circle-xmark','Rechazado'],
        5:['cyan','fa-thumbs-up','Pre-aprobado'], 6:['green','fa-circle-check','Aprobado'],
        66:['amber','fa-circle-check','Aprobado observado'], 7:['green','fa-sack-dollar','Liquidado'], 70:['green','fa-sack-dollar','Liquidado'],
      }[r.credito_estado] || ['slate','fa-minus','Sin crédito'];
      return { bg: C[s[0]][0], fg: C[s[0]][1], icon: 'fas ' + s[1], t: 'Crédito: ' + s[2] };
    },
    pago(r) {
      const m = {
        0: ['#f1f5f9','#64748b','Sin pagos'], 1: ['#dbeafe','#1d4ed8','Con seña'],
        2: ['#e0e7ff','#4338ca','A cuenta'], 3: ['#bbf7d0','#047857','Cancelada'],
      }[r.estadopago] || ['#f1f5f9','#64748b','Sin pagos'];
      return { bg: m[0], fg: m[1], t: m[2] };
    },
  };
}
