/*
 * Componente Alpine del Control de Reservas (versión moderna de
 * ventas/web/control_reservas.php). Se carga al final del <body> (comun/layout.php).
 *
 * Carga data.php (lista paginada + búsqueda), pollea noti.php (contador de
 * notificaciones, como el original cada 7s) y dispara facturar.php / anular.php.
 */
function controlReserva(puedeControlar) {
  return {
    puedeControlar: puedeControlar,
    loading: true, rows: [], total: 0, pages: 1, page: 1,
    filtros: { q: '', per: 20 },
    noti: 0,

    init() {
      this.load();
      this.refrescarNoti();
      setInterval(() => this.refrescarNoti(), 7000);    // notis cada 7s (igual que el original)
      setInterval(() => this.load(), 300000);           // refresco de la lista cada 5 min (meta refresh original)
    },

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

    async refrescarNoti() {
      try {
        const res = await fetch('noti.php', { cache: 'no-store' });
        const j = await res.json();
        if (j.ok) this.noti = j.cantidad;
      } catch (e) { /* silencioso, como el original */ }
    },

    // Estado de envío de la reserva (reservas.enviada).
    estado(r) {
      const C = { slate:['#f1f5f9','#64748b'], blue:['#dbeafe','#1d4ed8'], indigo:['#e0e7ff','#4338ca'],
                  cyan:['#cffafe','#0e7490'], amber:['#fde68a','#b45309'], green:['#bbf7d0','#047857'] };
      const s = {
        0:['slate','fa-pen','Sin enviar'],         1:['blue','fa-paper-plane','Enviada'],
        2:['indigo','fa-rotate','Actualizada'],    3:['amber','fa-triangle-exclamation','Observada'],
        4:['cyan','fa-eye','Vista'],               5:['green','fa-circle-check','Aprobada'],
      }[r.enviada] || ['slate','fa-pen','Sin enviar'];
      return { bg: C[s[0]][0], fg: C[s[0]][1], icon: 'fas ' + s[1], t: s[2] };
    },
    // Estado de pago (reservas.estadopago) → mismos estados que el módulo de pagos.
    pago(r) {
      const m = {
        0: ['#f1f5f9','#64748b','Sin pagos'], 1: ['#dbeafe','#1d4ed8','Con seña'],
        2: ['#e0e7ff','#4338ca','A cuenta'],  3: ['#bbf7d0','#047857','Cancelada'],
      }[r.estadopago] || ['#f1f5f9','#64748b','Sin pagos'];
      return { bg: m[0], fg: m[1], t: m[2] };
    },
    // Estado de la factura asociada (facturas.estado) → color del botón Facturar.
    factura(r) {
      const m = {
        0: ['#64748b','Sin facturar'], 1: ['#1d4ed8','Facturación enviada'],
        2: ['#b45309','Facturación observada'], 3: ['#047857','Facturación OK'],
      }[r.factura_estado] || ['#64748b','Sin facturar'];
      return { fg: m[0], t: m[1] };
    },

    editarUrl(r) { return '../ventas/web/reserva.php?IDrecord=' + r.idreserva; },
    pagoUrl(r)   { return '../estado_cuenta/cuenta.php?IDrecord=' + r.idcliente; },

    async facturar(r) {
      const body = new URLSearchParams({ idres: r.idreserva });
      try {
        const res = await fetch('facturar.php', {
          method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body,
        });
        const j = await res.json();
        if (!j.ok) { alert('Error: ' + (j.error || '')); return; }
        window.location.href = j.url;   // → pantalla de facturación legacy
      } catch (e) { alert('No se pudo iniciar la facturación: ' + e); }
    },

    async anular(r) {
      if (!confirm('¿Seguro que querés anular la reserva #' + r.idreserva + '?')) return;
      const obs = prompt('Ingresá el motivo por el cual anulás la reserva.');
      if (obs === null || obs.trim() === '') return;
      const body = new URLSearchParams({ idres: r.idreserva, obs: obs });
      try {
        const res = await fetch('anular.php', {
          method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body,
        });
        const j = await res.json();
        if (!j.ok) { alert('Error: ' + (j.error || '')); return; }
        alert('Se anuló la reserva.');
        this.load();
        this.refrescarNoti();
      } catch (e) { alert('No se pudo anular: ' + e); }
    },

    fecha(f) {
      if (!f) return '';
      const p = String(f).split('-');
      return p.length === 3 ? `${p[2]}/${p[1]}/${p[0]}` : f;
    },
  };
}
