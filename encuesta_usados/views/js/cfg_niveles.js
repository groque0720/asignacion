/* Configurador · Niveles (Alpine). */
function cfgNiveles() {
  return {
    loading: true,
    items: [],
    modal: { open: false, saving: false, error: '', id: 0, nombre: '', desde: 0, hasta: 10, color: '#607d8b' },

    async load() {
      this.loading = true;
      try {
        const d = await (await fetch('cfg_data.php?res=niveles', { cache: 'no-store' })).json();
        if (d.error) { alert(d.error); this.loading = false; return; }
        this.items = d.items;
      } catch (e) { alert('No se pudo cargar: ' + e); }
      this.loading = false;
    },
    abrirNueva() { this.modal = { open: true, saving: false, error: '', id: 0, nombre: '', desde: 0, hasta: 10, color: '#607d8b' }; },
    abrirEditar(n) { this.modal = { open: true, saving: false, error: '', id: n.id_nivel, nombre: n.nombre, desde: n.desde, hasta: n.hasta, color: n.color }; },
    async guardar() {
      if (!this.modal.nombre.trim()) { this.modal.error = 'El nombre es obligatorio'; return; }
      if (this.modal.desde > this.modal.hasta) { this.modal.error = '"Desde" no puede ser mayor que "Hasta"'; return; }
      this.modal.saving = true; this.modal.error = '';
      const m = this.modal;
      const d = await this.post(new URLSearchParams({ res: 'nivel', accion: 'guardar', id_nivel: m.id, nombre: m.nombre, desde: m.desde, hasta: m.hasta, color: m.color }));
      this.modal.saving = false;
      if (!d.ok) { this.modal.error = d.error || 'Error'; return; }
      this.modal.open = false; this.load();
    },
    async eliminar(n) {
      if (!confirm('¿Eliminar el nivel "' + n.nombre + '"?')) return;
      const d = await this.post(new URLSearchParams({ res: 'nivel', accion: 'eliminar', id_nivel: n.id_nivel }));
      if (!d.ok) { alert(d.error); return; } this.load();
    },
    async post(body) {
      try { return await (await fetch('cfg_guardar.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body })).json(); }
      catch (e) { return { ok: false, error: 'Error de red: ' + e }; }
    },
  };
}
