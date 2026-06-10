/*
 * Componente Alpine del detalle de Estado de Cuenta (cuenta.php).
 * Carga data.php (resumen + pagos) y maneja el ABM de pagos contra guardar.php.
 */
function estadoCuenta(idcliente, puedeEditar) {
  return {
    idcliente: idcliente,
    puedeEditar: puedeEditar,
    loading: true,
    error: '',
    modal: { open: false, saving: false, form: {}, montoDisplay: '' },
    d: { cliente:'', asesor:'', credito:'', financiera_cred:'', monto_cred:0,
         monto_operacion:0, pagado:0, pagado_bruto:0, devoluciones:0, a_cancelar:0, pagos:[],
         lookups:{ tipos:[], modos:[], financieras:[] } },

    async load() {
      this.loading = true;
      this.error = '';
      if (!this.idcliente) { this.error = 'Falta el cliente (IDrecord).'; this.loading = false; return; }
      try {
        const res = await fetch('data.php?idcliente=' + this.idcliente, { cache: 'no-store' });
        const j = await res.json();
        if (j.error) { this.error = j.error; this.loading = false; return; }
        this.d = j;
      } catch (e) {
        this.error = 'No se pudo cargar: ' + e;
      }
      this.loading = false;
    },

    abrirAlta() {
      this.modal.form = { idpago: 0, fecha: '', monto: '', tipo: 0, modo: 0, finan: 0, nrorecibo: '', obs: '' };
      this.modal.montoDisplay = '';
      this.modal.open = true;
    },
    abrirEdicion(p) {
      this.modal.form = {
        idpago: p.idpago, fecha: p.fecha || '', monto: p.monto,
        tipo: p.tipo_id, modo: p.modo_id, finan: p.fin_id || 0,
        nrorecibo: p.nrorecibo || '', obs: p.obs || '',
      };
      this.modal.montoDisplay = this.montoADisplay(p.monto);
      this.modal.open = true;
    },

    // Formatea el monto en vivo a moneda AR (1.234.567,89) y guarda el valor numérico.
    // Un signo "-" (en cualquier posición) marca el monto como NEGATIVO = devolución.
    formatearMonto(e) {
      const raw = e.target.value || '';
      const neg = raw.indexOf('-') !== -1;                      // hay signo menos
      let v = raw.replace(/[^\d,]/g, '');                        // solo dígitos y coma
      const partes = v.split(',');
      let ent = partes[0].replace(/^0+(?=\d)/, '');             // sin ceros a la izquierda
      let dec = partes.length > 1 ? partes.slice(1).join('').slice(0, 2) : null;
      const entFmt = ent.replace(/\B(?=(\d{3})+(?!\d))/g, '.'); // puntos de miles
      let display = entFmt;
      if (dec !== null) display = (entFmt || '0') + ',' + dec;
      if (neg && display !== '') display = '-' + display;       // preserva el signo
      this.modal.montoDisplay = display;
      let num = parseFloat((ent || '0') + '.' + (dec || '0'));
      if (neg) num = -num;
      this.modal.form.monto = isNaN(num) ? 0 : num;
    },
    montoADisplay(n) {
      if (n === '' || n === null || isNaN(n)) return '';
      return new Intl.NumberFormat('es-AR', { minimumFractionDigits: 0, maximumFractionDigits: 2 }).format(n);
    },
    async guardar() {
      const f = this.modal.form;
      if (!f.fecha || !f.tipo || !f.modo || !f.monto || parseFloat(f.monto) === 0) {
        alert('Ingresá como mínimo fecha, tipo, modo y monto.'); return;
      }
      if ((f.modo == 3 || f.modo == 4) && !f.finan) {
        alert('Si es Financiado o Leasing, ingresá la financiera.'); return;
      }
      if (f.tipo == 3 && !confirm('El tipo "Cancelación" marcará la unidad como CANCELADA y generará notificaciones. ¿Continuar?')) return;

      this.modal.saving = true;
      const body = new URLSearchParams({
        mov: f.idpago ? 2 : 1, nrolin: f.idpago || 0, idreserva: this.d.idreserva,
        fecha: f.fecha, tipo_pago: f.tipo, modo_pago: f.modo, finan: f.finan || 0,
        nrorecibo: f.nrorecibo || '', monto_pago: f.monto, obs: f.obs || '',
      });
      await this.enviar(body);
      this.modal.saving = false;
      this.modal.open = false;
    },
    async eliminar(p) {
      if (!confirm('¿Seguro que querés eliminar este pago (#' + p.idpago + ')?')) return;
      const body = new URLSearchParams({
        mov: 3, nrolin: p.idpago, idreserva: this.d.idreserva,
        fecha: '', tipo_pago: 0, modo_pago: 0, finan: 0, nrorecibo: '', monto_pago: 0, obs: '',
      });
      await this.enviar(body);
    },
    async enviar(body) {
      try {
        const res = await fetch('guardar.php', {
          method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body,
        });
        const j = await res.json();
        if (!j.ok) { alert('Error: ' + (j.error || '')); return; }
        await this.load();   // recarga lista + montos actualizados
      } catch (e) {
        alert('No se pudo guardar: ' + e);
      }
    },

    pct() {
      if (!this.d.monto_operacion) return 0;
      return Math.round((this.d.pagado / this.d.monto_operacion) * 100);
    },
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
