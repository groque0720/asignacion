/* Configurador · Preguntas (Alpine). */
function cfgPreguntas(idEncuesta) {
  return {
    idEncuesta,
    loading: true,
    encuesta: { nombre: '', activa: 0 },
    areas: [],
    items: [],
    modal: {
      open: false, saving: false, error: '', id: 0,
      texto: '', tipo: 1, pondera: true, es_observacion: false, id_area: 0,
      cond_on: false, cond_ref: 0, cond_op: '<', cond_val: '',
      opciones: [],
    },

    async load() {
      this.loading = true;
      try {
        const d = await (await fetch('cfg_data.php?res=preguntas&id_encuesta=' + this.idEncuesta, { cache: 'no-store' })).json();
        if (d.error) { alert(d.error); this.loading = false; return; }
        this.encuesta = d.encuesta; this.areas = d.areas; this.items = d.items;
      } catch (e) { alert('No se pudo cargar: ' + e); }
      this.loading = false;
    },

    tipoLabel(t) { return { 1: 'Escala 1-10', 2: 'Sí / No', 3: 'Selección múltiple', 4: 'Lista Sí/No', 5: 'Texto libre' }[t] || '—'; },
    ponderaBloqueada() { return this.modal.tipo === 4 || this.modal.tipo === 5; },
    onTipo() {
      if (this.ponderaBloqueada()) this.modal.pondera = false;
      if (this.modal.tipo !== 5) this.modal.es_observacion = false;
      if ((this.modal.tipo === 3 || this.modal.tipo === 4) && this.modal.opciones.length === 0) this.addOpcion();
    },
    refDisponibles() { return this.items.filter(p => p.id_pregunta !== this.modal.id); },
    recortar(t) { t = t || ''; return t.length > 40 ? t.slice(0, 40) + '…' : t; },

    addOpcion() { this.modal.opciones.push({ id: 0, texto: '' }); },
    delOpcion(i) { this.modal.opciones.splice(i, 1); },

    abrirNueva() {
      this.modal = { open: true, saving: false, error: '', id: 0, texto: '', tipo: 1, pondera: true,
        es_observacion: false, id_area: 0, cond_on: false, cond_ref: 0, cond_op: '<', cond_val: '', opciones: [] };
    },
    abrirEditar(p) {
      this.modal = {
        open: true, saving: false, error: '', id: p.id_pregunta,
        texto: p.texto || '', tipo: p.tipo, pondera: !!p.pondera, es_observacion: !!p.es_observacion,
        id_area: p.id_area || 0,
        cond_on: !!p.cond_ref, cond_ref: p.cond_ref || 0, cond_op: p.cond_op || '<', cond_val: p.cond_val || '',
        opciones: (p.opciones || []).map(o => ({ id: o.id, texto: o.texto })),
      };
    },

    async guardar() {
      const m = this.modal;
      if (!m.texto.trim()) { m.error = 'El texto es obligatorio'; return; }
      if ((m.tipo === 3 || m.tipo === 4) && m.opciones.filter(o => o.texto.trim()).length === 0) { m.error = 'Agregá al menos una opción'; return; }
      if (m.cond_on && (!m.cond_ref || m.cond_val.trim() === '')) { m.error = 'Completá la condición (pregunta y valor)'; return; }
      m.saving = true; m.error = '';
      const body = new URLSearchParams({
        res: 'pregunta', accion: 'guardar', id_pregunta: m.id, id_encuesta: this.idEncuesta,
        texto: m.texto, tipo: m.tipo, pondera: m.pondera ? 1 : 0, es_observacion: m.es_observacion ? 1 : 0,
        id_area: m.id_area || 0,
        cond_ref: m.cond_on ? m.cond_ref : 0, cond_op: m.cond_op, cond_val: m.cond_on ? m.cond_val : '',
        opciones_json: JSON.stringify(m.opciones.filter(o => o.texto.trim())),
      });
      const d = await this.post(body);
      m.saving = false;
      if (!d.ok) { m.error = d.error || 'Error al guardar'; return; }
      m.open = false; this.load();
    },
    async eliminar(p) {
      if (!confirm('¿Eliminar esta pregunta?')) return;
      const d = await this.post(new URLSearchParams({ res: 'pregunta', accion: 'baja', id_pregunta: p.id_pregunta }));
      if (!d.ok) { alert(d.error); return; } this.load();
    },
    async mover(p, dir) {
      const d = await this.post(new URLSearchParams({ res: 'pregunta', accion: 'orden', id_pregunta: p.id_pregunta, dir }));
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
