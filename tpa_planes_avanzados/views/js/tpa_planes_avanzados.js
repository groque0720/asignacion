/*
 * Componente Alpine del módulo TPA Planes Avanzados.
 * Se carga al final del <body> (comun/layout.php). Lee data.php (lista) /
 * opciones.php (catálogos) y postea reservar.php / guardar.php.
 */
function tpaPlanes(puedeEditar, esEFV, userId) {
  return {
    puedeEditar, esEFV, userId,
    vista: 'tabla',
    loading: true,
    rows: [],
    modelosActivos: [],
    exportMenu: false,
    filtros: { situacion: 1, modelo: 0, estado: 0, q: '' },
    cat: { versiones: [], modalidades: [], estados: [], situaciones: [], asesores: [] },
    catCargado: false,
    modalRes:  { open: false, saving: false, form: {} },
    modalPlan: { open: false, saving: false, form: {} },

    get colCount() {
      // Plan, Modalidad, Grupo-Orden, Cuotas, CuotaProm, ValorUnidad, Venta, Integr, DerAdjud, Total, Situación = 11
      return this.puedeEditar ? 15 : 11;
    },

    init() {
      this.load();
      this.loadCatalogos();
    },

    async load() {
      this.loading = true;
      const p = new URLSearchParams({
        situacionId: this.filtros.situacion,
        modelo: this.filtros.modelo,
        estado: this.filtros.estado,
      });
      try {
        const res = await fetch('data.php?' + p.toString(), { cache: 'no-store' });
        const d = await res.json();
        if (d.error) { alert('Error: ' + d.error); this.loading = false; return; }
        this.rows = d.rows;
        this.modelosActivos = d.modelosActivos;
        this.filtros.modelo = d.modeloActivo;   // el server resuelve el modelo por defecto
      } catch (e) {
        alert('No se pudo cargar: ' + e);
      }
      this.loading = false;
    },

    async loadCatalogos() {
      try {
        const res = await fetch('opciones.php', { cache: 'no-store' });
        const d = await res.json();
        if (d.ok) { this.cat = d; this.catCargado = true; }
      } catch (e) { /* los modales avisan si faltan */ }
    },

    setSituacion(s) { this.filtros.situacion = s; this.filtros.modelo = 0; this.load(); },
    setModelo(m)    { this.filtros.modelo = m; this.load(); },

    filasFiltradas() {
      const q = this.filtros.q.trim().toLowerCase();
      if (!q) return this.rows;
      return this.rows.filter(p =>
        (p.grupo_orden || '').toLowerCase().includes(q) ||
        (p.cliente || '').toLowerCase().includes(q) ||
        (p.usuario_venta || '').toLowerCase().includes(q) ||
        (p.modelo + ' ' + p.version).toLowerCase().includes(q) ||
        (p.modalidad || '').toLowerCase().includes(q)
      );
    },

    estadoColor(id) { return { 1: '#4CAF50', 2: '#FFEB3B', 3: '#F44336' }[id] || '#9ca3af'; },

    // ── Reservar / editar reserva ──────────────────────────────────────
    reservar(p) {
      this.modalRes.form = {
        planUuId: p.uuid,
        titulo: p.modelo + ' ' + p.version + ' · Grupo-Orden ' + (p.grupo_orden || ''),
        cliente: p.cliente || '',
        sexo: p.sexo || '',
        fecha_nacimiento: p.fecha_nacimiento || '',
        edad: p.edad || '',
        dni: p.dni || '',
        cuil: p.cuil || '',
        direccion: p.direccion || '',
        localidad: p.localidad || '',
        provincia: p.provincia || '',
        email: p.email || '',
        celular: p.celular || '',
        fecha_reserva: p.fecha_reserva || '',
        hora_reserva: p.hora_reserva || '',
        modelo_version_retirar: p.modelo_version_retirar || '',
        monto_reserva: p.monto_reserva != null ? this.money(p.monto_reserva) : '',
      };
      this.modalRes.open = true;
    },

    async guardarReserva() {
      const f = this.modalRes.form;
      if (!f.cliente.trim()) { alert('Ingresá el nombre del cliente.'); return; }
      this.modalRes.saving = true;
      try {
        const res = await fetch('reservar.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: new URLSearchParams(f),
        });
        const d = await res.json();
        if (!d.ok) { alert(d.error || 'No se pudo reservar.'); this.modalRes.saving = false; return; }
        this.modalRes.open = false;
        await this.load();
      } catch (e) { alert('No se pudo reservar: ' + e); }
      this.modalRes.saving = false;
    },

    // ── Crear / editar plan (admin) ────────────────────────────────────
    planVacio() {
      return {
        planUuId: '', version_id: 0, modalidad_id: 0, grupo_orden: '',
        situacion_id: this.filtros.situacion, estado_id: 1,
        usuario_venta_id: '', cuotas_pagadas_cantidad: 0,
        cuotas_pagadas_monto: '', costo: '', cesion: '', plus: '',
        cuota_promedio: '', valor_unidad: '', monto_reserva: '',
        venta: '', integracion: '', derecho_adjudicacion: '', precio_final: '',
        observaciones: '',
      };
    },

    nuevoPlan() {
      if (!this.catCargado) this.loadCatalogos();
      this.modalPlan.form = this.planVacio();
      this.modalPlan.open = true;
    },

    editarPlan(p) {
      if (!this.catCargado) this.loadCatalogos();
      this.modalPlan.form = {
        planUuId: p.uuid,
        version_id: p.version_id,
        modalidad_id: p.modalidad_id,
        grupo_orden: p.grupo_orden || '',
        situacion_id: p.situacion_id,
        estado_id: p.estado_id,
        usuario_venta_id: p.usuario_venta_id != null ? String(p.usuario_venta_id) : '',
        cuotas_pagadas_cantidad: p.cuotas_pagadas_cantidad,
        cuotas_pagadas_monto: this.money(p.cuotas_pagadas_monto),
        costo: this.money(p.costo),
        cesion: this.money(p.cesion),
        plus: this.money(p.plus),
        cuota_promedio: this.money(p.cuota_promedio),
        valor_unidad: this.money(p.valor_unidad),
        monto_reserva: p.monto_reserva != null ? this.money(p.monto_reserva) : '',
        venta: this.money(p.venta),
        integracion: this.money(p.integracion),
        derecho_adjudicacion: this.money(p.derecho_adjudicacion),
        precio_final: this.money(p.precio_final),
        observaciones: p.observaciones || '',
      };
      this.modalPlan.open = true;
    },

    recalcTotal() {
      const f = this.modalPlan.form;
      const t = this.parseNum(f.venta) + this.parseNum(f.integracion) + this.parseNum(f.derecho_adjudicacion);
      f.precio_final = this.money(t);
    },

    async guardarPlan() {
      const f = this.modalPlan.form;
      if (!f.version_id || !f.modalidad_id) { alert('Elegí versión y modalidad.'); return; }
      this.modalPlan.saving = true;
      try {
        const res = await fetch('guardar.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: new URLSearchParams(f),
        });
        const d = await res.json();
        if (!d.ok) { alert(d.error || 'No se pudo guardar.'); this.modalPlan.saving = false; return; }
        this.modalPlan.open = false;
        await this.load();
      } catch (e) { alert('No se pudo guardar: ' + e); }
      this.modalPlan.saving = false;
    },

    // ── Exportaciones ──────────────────────────────────────────────────
    exportUrl(tipo, estadoOverride) {
      const est = (estadoOverride !== undefined) ? estadoOverride : this.filtros.estado;
      const p = new URLSearchParams({ situacionId: this.filtros.situacion, modelo: this.filtros.modelo });
      if (est) p.set('estado', est);
      return tipo + '.php?' + p.toString();
    },
    exportTodoUrl(tipo) {
      return tipo + '.php?' + new URLSearchParams({ situacionId: this.filtros.situacion }).toString();
    },

    // ── Helpers ────────────────────────────────────────────────────────
    parseNum(v) {
      if (typeof v === 'number') return v;
      if (!v) return 0;
      let s = String(v).trim().replace(/[$\s.]/g, '').replace(',', '.');
      const n = parseFloat(s.replace(/[^0-9.\-]/g, ''));
      return isNaN(n) ? 0 : n;
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
