/*
 * Componente Alpine del módulo Seguimiento Documentación Usados.
 * Recibe la config inicial (catálogos + permisos) desde index.php.
 */
function usadosSeguimientos(cfg) {
  return {
    // ── Config / catálogos ────────────────────────────────────────────
    puedeEditar:  cfg.puedeEditar,
    esAdmin:      cfg.esAdmin,
    sucursales:   cfg.sucursales,
    estadosUsado: cfg.estadosUsado,
    estados:      cfg.estados,
    uploadsUrl:   cfg.uploadsUrl,

    // ── Estado del grid ───────────────────────────────────────────────
    loading: false,
    items: [],
    usados: [],
    filtros: { sucursal: 0, estado_usado: 0, estado: '' },
    busqueda: '',

    // ── Modal de celda ────────────────────────────────────────────────
    modal: {
      open: false, loading: false, saving: false, vistaHistorial: false,
      id_unidad: 0, id_item: 0, titulo: '', subtitulo: '', meta: '',
      estado: 0, observacion: '', adjuntos: [], files: [], historial: [],
      _u: null, _it: null,
    },

    // ── Modal admin ───────────────────────────────────────────────────
    admin: {
      open: false, loading: false, saving: false, items: [],
      form: { id_item: 0, nombre: '', descripcion: '', posicion: 1, activo: true },
    },

    // ── Lightbox / carrusel de imágenes ───────────────────────────────
    lightbox: { open: false, index: 0, imagenes: [] },

    init() { this.load(); },

    // ── Carga del grid ────────────────────────────────────────────────
    async load() {
      this.loading = true;
      const p = new URLSearchParams({
        sucursal:     this.filtros.sucursal,
        estado_usado: this.filtros.estado_usado,
        estado:       this.filtros.estado,
      });
      try {
        const res = await fetch('data.php?' + p.toString(), { cache: 'no-store' });
        const d = await res.json();
        if (!d.ok) { this.toast('Error: ' + (d.error || ''), 'error'); this.loading = false; return; }
        this.items  = d.items;
        this.usados = d.usados;
      } catch (e) {
        this.toast('No se pudo cargar: ' + e, 'error');
      }
      this.loading = false;
    },

    resetFiltros() {
      this.filtros = { sucursal: 0, estado_usado: 0, estado: '' };
      this.busqueda = '';
      this.load();
    },

    // Filtro de texto en pantalla (sin ir al servidor).
    usadosFiltrados() {
      const q = this.busqueda.trim().toLowerCase();
      if (!q) return this.usados;
      return this.usados.filter(u =>
        String(u.interno).toLowerCase().includes(q) ||
        (u.vehiculo || '').toLowerCase().includes(q) ||
        (u.dominio  || '').toLowerCase().includes(q) ||
        (u.asesor_toma || '').toLowerCase().includes(q)
      );
    },

    // Apellido + inicial del nombre: "Gonzalez Cristian" -> "Gonzalez C."
    asesorCorto(nombre) {
      if (!nombre) return '';
      const p = String(nombre).trim().split(/\s+/);
      if (p.length === 1) return p[0];
      return p[0] + ' ' + p[1].charAt(0).toUpperCase() + '.';
    },

    // ── Helpers de estado ─────────────────────────────────────────────
    estadoMeta(val) {
      return this.estados.find(e => e.estado === val) || this.estados[0];
    },
    // Replica us_estado_general() del backend.
    estadoGeneral(u) {
      let hayPend = false, hayProc = false;
      for (const it of this.items) {
        const e = u.celdas[it.id_item] ? u.celdas[it.id_item].estado : 0;
        if (e === 0) hayPend = true;
        if (e === 3) hayProc = true;
      }
      if (hayPend) return { estado: 0, label: 'Pendiente',  icon: '○', class: 'est-pendiente' };
      if (hayProc) return { estado: 3, label: 'En proceso', icon: '◑', class: 'est-en-proceso' };
      return { estado: 1, label: 'Completo', icon: '✓', class: 'est-hecho' };
    },

    // ── Modal de celda ────────────────────────────────────────────────
    async abrirCelda(u, it) {
      this.modal.open = true;
      this.modal.loading = true;
      this.modal.vistaHistorial = false;
      this.modal.files = [];
      this.modal.titulo = 'Interno ' + u.interno + ' — ' + u.vehiculo;
      this.modal.subtitulo = it.nombre;
      this.modal.id_unidad = u.id_unidad;
      this.modal.id_item = it.id_item;
      this.modal._u = u;
      this.modal._it = it;

      try {
        const res = await fetch('celda.php?id_unidad=' + u.id_unidad + '&id_item=' + it.id_item, { cache: 'no-store' });
        const d = await res.json();
        if (!d.ok) { this.toast('Error: ' + (d.error || ''), 'error'); this.cerrarCelda(); return; }
        this.modal.estado = d.estado;
        this.modal.observacion = d.observacion;
        this.modal.meta = d.meta;
        this.modal.adjuntos = d.adjuntos;
      } catch (e) {
        this.toast('No se pudo abrir: ' + e, 'error');
        this.cerrarCelda();
      }
      this.modal.loading = false;
    },

    cerrarCelda() {
      this.modal.open = false;
      this.modal.vistaHistorial = false;
      this.modal.files = [];
    },

    async guardarCelda() {
      if (!this.puedeEditar) return;
      this.modal.saving = true;

      const fd = new FormData();
      fd.append('id_unidad', this.modal.id_unidad);
      fd.append('id_item', this.modal.id_item);
      fd.append('estado', this.modal.estado);
      fd.append('observacion', this.modal.observacion || '');
      for (const f of this.modal.files) fd.append('archivo[]', f);

      try {
        const res = await fetch('guardar_celda.php', { method: 'POST', body: fd });
        const d = await res.json();
        if (!d.ok) { this.toast('Error: ' + (d.error || ''), 'error'); this.modal.saving = false; return; }

        // Actualizar la celda en el grid.
        const u = this.modal._u;
        if (u && u.celdas[this.modal.id_item]) {
          const c = u.celdas[this.modal.id_item];
          c.estado = d.estado;
          c.icon = d.icon;
          c.class = d.class;
          c.observacion = this.modal.observacion || '';
          c.tiene_arch = d.tiene_arch;
          u.estado_gral = this.estadoGeneral(u);
        }

        if (d.errores && d.errores.length) {
          this.toast('Guardado. No se subieron: ' + d.errores.join(', '), 'error');
          // Refrescar adjuntos para reflejar lo que sí entró.
          this.modal.files = [];
          await this.abrirCelda(u, this.modal._it);
        } else {
          this.toast('Guardado correctamente', 'success');
          this.cerrarCelda();
        }
      } catch (e) {
        this.toast('No se pudo guardar: ' + e, 'error');
      }
      this.modal.saving = false;
    },

    async eliminarArchivo(a) {
      if (!this.puedeEditar) return;
      if (!confirm('¿Eliminar este archivo? Esta acción no se puede deshacer.')) return;

      const body = new URLSearchParams({
        tipo: a.tipo, id_unidad: this.modal.id_unidad, id_item: this.modal.id_item, id_arch: a.id,
      });
      try {
        const res = await fetch('eliminar_archivo.php', {
          method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body,
        });
        const d = await res.json();
        if (!d.ok) { this.toast('Error: ' + (d.error || ''), 'error'); return; }
        this.modal.adjuntos = this.modal.adjuntos.filter(x => !(x.tipo === a.tipo && x.id === a.id));
        const u = this.modal._u;
        if (u && u.celdas[this.modal.id_item]) u.celdas[this.modal.id_item].tiene_arch = d.tiene_arch;
        this.toast('Archivo eliminado', 'success');
      } catch (e) {
        this.toast('No se pudo eliminar: ' + e, 'error');
      }
    },

    // ── Adjuntos: imagen / ícono / lightbox ───────────────────────────
    esImagen(nombre) {
      return /\.(jpe?g|png|gif|webp)$/i.test(nombre || '');
    },
    iconArchivo(nombre) {
      return /\.pdf$/i.test(nombre || '') ? 'fa-file-pdf' : 'fa-file';
    },
    abrirImagen(a) {
      const imgs = this.modal.adjuntos.filter(x => this.esImagen(x.nombre));
      let idx = imgs.findIndex(x => x.tipo === a.tipo && x.id === a.id);
      if (idx < 0) idx = 0;
      this.lightbox = { open: true, index: idx, imagenes: imgs };
    },
    lbActual() {
      return this.lightbox.imagenes[this.lightbox.index] || { url: '', nombre: '' };
    },
    lbPrev() {
      const n = this.lightbox.imagenes.length;
      if (n > 1) this.lightbox.index = (this.lightbox.index - 1 + n) % n;
    },
    lbNext() {
      const n = this.lightbox.imagenes.length;
      if (n > 1) this.lightbox.index = (this.lightbox.index + 1) % n;
    },

    async verHistorial() {
      this.modal.vistaHistorial = true;
      this.modal.historial = [];
      try {
        const res = await fetch('historial.php?id_unidad=' + this.modal.id_unidad + '&id_item=' + this.modal.id_item, { cache: 'no-store' });
        const d = await res.json();
        if (d.ok) this.modal.historial = d.historial;
      } catch (e) {
        this.toast('No se pudo cargar el historial: ' + e, 'error');
      }
    },

    // ── Admin de ítems ────────────────────────────────────────────────
    async abrirAdmin() {
      this.admin.open = true;
      this.nuevoItem();
      await this.cargarItems();
    },
    cerrarAdmin() {
      this.admin.open = false;
      // Recargar el grid por si cambiaron las columnas.
      this.load();
    },
    async cargarItems() {
      this.admin.loading = true;
      try {
        const res = await fetch('items.php', { cache: 'no-store' });
        const d = await res.json();
        if (d.ok) this.admin.items = d.items;
      } catch (e) {
        this.toast('No se pudieron cargar los ítems: ' + e, 'error');
      }
      this.admin.loading = false;
    },
    nuevoItem() {
      const pos = this.admin.items.length + 1;
      this.admin.form = { id_item: 0, nombre: '', descripcion: '', posicion: pos, activo: true };
    },
    editarItem(it) {
      this.admin.form = {
        id_item: it.id_item, nombre: it.nombre, descripcion: it.descripcion,
        posicion: it.posicion, activo: it.activo == 1,
      };
    },
    async guardarItem() {
      if (!this.admin.form.nombre.trim()) { this.toast('El nombre es requerido', 'error'); return; }
      this.admin.saving = true;
      const body = new URLSearchParams({
        id_item: this.admin.form.id_item,
        nombre: this.admin.form.nombre,
        descripcion: this.admin.form.descripcion || '',
        posicion: this.admin.form.posicion || 1,
      });
      if (this.admin.form.activo) body.append('activo', '1');
      try {
        const res = await fetch('guardar_item.php', {
          method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body,
        });
        const d = await res.json();
        if (!d.ok) { this.toast('Error: ' + (d.error || ''), 'error'); this.admin.saving = false; return; }
        this.toast('Ítem ' + d.accion, 'success');
        this.nuevoItem();
        await this.cargarItems();
      } catch (e) {
        this.toast('No se pudo guardar: ' + e, 'error');
      }
      this.admin.saving = false;
    },

    // ── Toast ─────────────────────────────────────────────────────────
    toast(msg, tipo) {
      let t = document.getElementById('us-toast');
      if (!t) {
        t = document.createElement('div');
        t.id = 'us-toast';
        t.className = 'us-toast';
        document.body.appendChild(t);
      }
      t.textContent = msg;
      t.className = 'us-toast us-toast-' + (tipo || 'info');
      // Forzar reflow para reiniciar la animación.
      void t.offsetWidth;
      t.classList.add('visible');
      clearTimeout(window._usToastTimer);
      window._usToastTimer = setTimeout(() => t.classList.remove('visible'), 2600);
    },
  };
}
