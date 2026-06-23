/*
 * Componente Alpine de la encuesta pública de usados (mobile, 1 pregunta por slide).
 * Maneja navegación, lógica condicional y envío a responder_guardar.php.
 */
function responderEncuesta(datos) {
  return {
    token: datos.token,
    id_token: datos.id_token,
    id_encuesta: datos.id_encuesta,
    bienvenida: datos.bienvenida || '',
    preguntas: datos.preguntas || [],
    resp: {},
    paso: -1,          // -1 = bienvenida
    enviando: false,
    errorMsg: '',

    init() {
      // Si no hay mensaje de bienvenida, arrancar directo en la primera pregunta visible.
      if (!this.bienvenida) { this.paso = this.primerVisible(); }
    },

    // ── Lógica condicional ────────────────────────────────────────────────
    valorNum(idPreg) {
      const r = this.resp[idPreg];
      if (!r) return null;
      return (typeof r.valor === 'number') ? r.valor : null;
    },
    evaluar(v, op, c) {
      const a = parseFloat(v), b = parseFloat(c);
      switch (op) {
        case '<':  return a <  b;
        case '<=': return a <= b;
        case '=':  return a === b;
        case '>=': return a >= b;
        case '>':  return a >  b;
        case '!=': return a !== b;
        default:   return true;
      }
    },
    visible(i) {
      const p = this.preguntas[i];
      if (!p || !p.cond_ref) return true;
      const refVal = this.valorNum(p.cond_ref);
      if (refVal === null) return false;        // el disparador no se respondió → se omite
      return this.evaluar(refVal, p.cond_op, p.cond_val);
    },
    visibles() {
      const out = [];
      for (let i = 0; i < this.preguntas.length; i++) if (this.visible(i)) out.push(i);
      return out;
    },
    primerVisible() { const v = this.visibles(); return v.length ? v[0] : 0; },
    posVisible()  { const i = this.visibles().indexOf(this.paso); return i < 0 ? 0 : i; },
    totalVisible(){ return Math.max(1, this.visibles().length); },
    esUltima()    { const v = this.visibles(); return v.length === 0 || this.paso === v[v.length - 1]; },
    progreso()    { return this.paso < 0 ? 0 : Math.round(((this.posVisible() + 1) / this.totalVisible()) * 100); },

    // ── Setters de respuesta ──────────────────────────────────────────────
    setValor(id, n)  { this.resp[id] = { valor: n }; this.errorMsg = ''; },
    setTexto(id, v)  { this.resp[id] = { texto: v }; this.errorMsg = ''; },
    toggleMultiple(id, opId) {
      const cur = (this.resp[id] && this.resp[id].opciones) ? this.resp[id].opciones.slice() : [];
      const ix = cur.indexOf(opId);
      if (ix >= 0) cur.splice(ix, 1); else cur.push(opId);
      this.resp[id] = { opciones: cur };
      this.errorMsg = '';
    },
    estaMarcada(id, opId) {
      return !!(this.resp[id] && this.resp[id].opciones && this.resp[id].opciones.indexOf(opId) >= 0);
    },
    setLista(id, opId, val) {
      const cur = (this.resp[id] && this.resp[id].opciones) ? this.resp[id].opciones.slice() : [];
      const item = cur.find(x => x.id === opId);
      if (item) item.val = val; else cur.push({ id: opId, val: val });
      this.resp[id] = { opciones: cur };
      this.errorMsg = '';
    },
    valLista(id, opId) {
      if (!this.resp[id] || !this.resp[id].opciones) return null;
      const item = this.resp[id].opciones.find(x => x.id === opId);
      return item ? item.val : null;
    },

    // ── Validación ────────────────────────────────────────────────────────
    valida(p) {
      const r = this.resp[p.id];
      if (p.tipo === 1) return r && typeof r.valor === 'number' && r.valor >= 1 && r.valor <= 10;
      if (p.tipo === 2) return r && (r.valor === 0 || r.valor === 1);
      if (p.tipo === 3) return r && r.opciones && r.opciones.length >= 1;
      if (p.tipo === 4) return r && r.opciones && r.opciones.length === p.opciones.length;
      return true; // tipo 5: opcional
    },
    msgValida(p) {
      if (p.tipo === 3) return 'Seleccioná al menos una opción.';
      if (p.tipo === 4) return 'Respondé Sí o No en cada ítem.';
      return 'Seleccioná una respuesta para continuar.';
    },

    // ── Navegación ────────────────────────────────────────────────────────
    empezar() { this.paso = this.primerVisible(); },
    anterior() {
      const v = this.visibles();
      const pos = v.indexOf(this.paso);
      if (pos <= 0) { this.paso = this.bienvenida ? -1 : this.paso; }
      else { this.paso = v[pos - 1]; }
      this.errorMsg = '';
    },
    siguiente() {
      const p = this.preguntas[this.paso];
      if (p && !this.valida(p)) { this.errorMsg = this.msgValida(p); return; }
      this.errorMsg = '';
      if (this.esUltima()) { this.enviar(); return; }
      const v = this.visibles();
      const pos = v.indexOf(this.paso);
      this.paso = v[pos + 1];
    },

    // ── Envío ─────────────────────────────────────────────────────────────
    async enviar() {
      this.enviando = true;
      // Construir payload sólo con preguntas visibles respondidas (el resto = omitidas)
      const visibles = this.visibles();
      const payload = {};
      visibles.forEach(i => {
        const id = this.preguntas[i].id;
        if (this.resp[id] !== undefined) payload[id] = this.resp[id];
      });
      try {
        const res = await fetch('responder_guardar.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: new URLSearchParams({
            token: this.token,
            id_token: this.id_token,
            id_encuesta: this.id_encuesta,
            respuestas_json: JSON.stringify(payload),
          }),
        });
        const d = await res.json();
        if (!d.ok) { this.errorMsg = d.error || 'No se pudo guardar.'; this.enviando = false; return; }
        window.location = 'gracias.php';
      } catch (e) {
        this.errorMsg = 'Error de red. Reintentá.';
        this.enviando = false;
      }
    },
  };
}
