/* Configurador · Áreas (Alpine). */
function cfgAreas() {
  return {
    loading: true,
    items: [],
    modal: { open: false, saving: false, error: '', id: 0, nombre: '', color: '#607d8b', nro_orden: 99 },

    async load() {
      this.loading = true;
      try {
        const d = await (await fetch('cfg_data.php?res=areas', { cache: 'no-store' })).json();
        if (d.error) { alert(d.error); this.loading = false; return; }
        this.items = d.items;
      } catch (e) { alert('No se pudo cargar: ' + e); }
      this.loading = false;
    },
    abrirNueva() { this.modal = { open: true, saving: false, error: '', id: 0, nombre: '', color: '#607d8b', nro_orden: (this.items.length + 1) }; },
    abrirEditar(a) { this.modal = { open: true, saving: false, error: '', id: a.id_area, nombre: a.nombre, color: a.color, nro_orden: a.nro_orden }; },
    async guardar() {
      if (!this.modal.nombre.trim()) { this.modal.error = 'El nombre es obligatorio'; return; }
      this.modal.saving = true; this.modal.error = '';
      const d = await this.post(new URLSearchParams({ res: 'area', accion: 'guardar', id_area: this.modal.id, nombre: this.modal.nombre, color: this.modal.color, nro_orden: this.modal.nro_orden }));
      this.modal.saving = false;
      if (!d.ok) { this.modal.error = d.error || 'Error'; return; }
      this.modal.open = false; this.load();
    },
    async eliminar(a) {
      if (!confirm('¿Eliminar el área "' + a.nombre + '"? Las preguntas con esta área quedarán sin área.')) return;
      const d = await this.post(new URLSearchParams({ res: 'area', accion: 'eliminar', id_area: a.id_area }));
      if (!d.ok) { alert(d.error); return; } this.load();
    },
    async post(body) {
      try { return await (await fetch('cfg_guardar.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body })).json(); }
      catch (e) { return { ok: false, error: 'Error de red: ' + e }; }
    },
  };
}
