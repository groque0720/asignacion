/* Configurador · Encuestas (Alpine). */
function cfgEncuestas() {
  return {
    loading: true,
    items: [],
    modal: { open: false, saving: false, error: '', id: 0, nombre: '', descripcion: '', mensaje_bienvenida: '' },

    async load() {
      this.loading = true;
      try {
        const d = await (await fetch('cfg_data.php?res=encuestas', { cache: 'no-store' })).json();
        if (d.error) { alert(d.error); this.loading = false; return; }
        this.items = d.items;
      } catch (e) { alert('No se pudo cargar: ' + e); }
      this.loading = false;
    },

    abrirNueva() { this.modal = { open: true, saving: false, error: '', id: 0, nombre: '', descripcion: '', mensaje_bienvenida: '' }; },
    abrirEditar(e) {
      this.modal = { open: true, saving: false, error: '', id: e.id_encuesta,
        nombre: e.nombre || '', descripcion: e.descripcion || '', mensaje_bienvenida: e.mensaje_bienvenida || '' };
    },

    async guardar() {
      if (!this.modal.nombre.trim()) { this.modal.error = 'El nombre es obligatorio'; return; }
      this.modal.saving = true; this.modal.error = '';
      const body = new URLSearchParams({
        res: 'encuesta', accion: this.modal.id ? 'editar' : 'crear',
        id_encuesta: this.modal.id, nombre: this.modal.nombre,
        descripcion: this.modal.descripcion, mensaje_bienvenida: this.modal.mensaje_bienvenida,
      });
      const d = await this.post(body);
      this.modal.saving = false;
      if (!d.ok) { this.modal.error = d.error || 'Error al guardar'; return; }
      this.modal.open = false; this.load();
    },
    async activar(e) {
      if (!confirm('Activar "' + e.nombre + '"? Se desactivará cualquier otra encuesta activa.')) return;
      const d = await this.post(new URLSearchParams({ res: 'encuesta', accion: 'activar', id_encuesta: e.id_encuesta }));
      if (!d.ok) { alert(d.error); return; } this.load();
    },
    async desactivar(e) {
      const d = await this.post(new URLSearchParams({ res: 'encuesta', accion: 'desactivar', id_encuesta: e.id_encuesta }));
      if (!d.ok) { alert(d.error); return; } this.load();
    },
    async eliminar(e) {
      if (!confirm('¿Eliminar la encuesta "' + e.nombre + '"?')) return;
      const d = await this.post(new URLSearchParams({ res: 'encuesta', accion: 'baja', id_encuesta: e.id_encuesta }));
      if (!d.ok) { alert(d.error); return; } this.load();
    },

    async post(body) {
      try {
        const res = await fetch('cfg_guardar.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body });
        return await res.json();
      } catch (e) { return { ok: false, error: 'Error de red: ' + e }; }
    },
  };
}
