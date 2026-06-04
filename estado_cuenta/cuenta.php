<?php
@session_start();
if (!isset($_SESSION["autentificado"]) || $_SESSION["autentificado"] !== "SI") {
    header("Location: ../login");
    exit();
}
// IDrecord = idcliente (compatibilidad con el módulo viejo). También acepta ?idcliente=
$idcliente = (int)($_GET['IDrecord'] ?? $_GET['idcliente'] ?? 0);
$fecha_actual = date('d/m/Y');

// Permiso para registrar pagos: Tesorería (8) + admins (1,2) + usuarios habilitados.
$perfil = (int)($_SESSION['idperfil'] ?? 0);
$uid    = (int)($_SESSION['id'] ?? 0);
$puedeEditar = in_array($perfil, [1, 2, 8]) || in_array($uid, [119, 120, 87, 28, 11, 94, 96]);
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Estado de Cuenta · Derka y Vargas</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter', system-ui, sans-serif; }
    [x-cloak] { display: none !important; }
    .num { font-variant-numeric: tabular-nums; }
  </style>
</head>
<body class="bg-gray-100 min-h-screen text-slate-800"
      x-data="estadoCuenta(<?php echo $idcliente; ?>, <?php echo $puedeEditar ? 'true' : 'false'; ?>)" x-init="load()" x-cloak>

  <!-- ── Header ──────────────────────────────────────────────────────────── -->
  <header class="bg-slate-900 text-white shadow-lg sticky top-0 z-30">
    <div class="max-w-[1100px] mx-auto px-6 py-3 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 bg-blue-600 rounded-lg flex items-center justify-center">
          <i class="fas fa-file-invoice-dollar text-sm"></i>
        </div>
        <div>
          <h1 class="text-sm font-bold leading-tight">Estado de Cuenta</h1>
          <p class="text-slate-400 text-xs">Derka y Vargas S.A.</p>
        </div>
      </div>
      <div class="flex items-center gap-4">
        <a href="javascript:history.back()" class="text-xs text-slate-300 hover:text-white">
          <i class="fas fa-arrow-left mr-1"></i> Volver
        </a>
        <div class="w-px h-7 bg-slate-700"></div>
        <div class="flex items-center gap-2" x-show="!error">
          <a :href="'excel.php?IDrecord=' + idcliente" target="_blank"
             class="flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 px-3 py-1.5 rounded-md text-xs font-medium transition-colors">
            <i class="fas fa-file-excel"></i> Excel
          </a>
          <a :href="'pdf.php?IDrecord=' + idcliente" target="_blank"
             class="flex items-center gap-1.5 bg-red-600 hover:bg-red-700 px-3 py-1.5 rounded-md text-xs font-medium transition-colors">
            <i class="fas fa-file-pdf"></i> PDF / Imprimir
          </a>
        </div>
        <div class="w-px h-7 bg-slate-700"></div>
        <div class="text-right">
          <p class="text-[10px] text-slate-500 uppercase tracking-widest leading-none mb-0.5">Fecha</p>
          <p class="text-sm font-semibold"><?php echo $fecha_actual; ?></p>
        </div>
      </div>
    </div>
  </header>

  <main class="max-w-[1100px] mx-auto px-6 py-5 space-y-5">

    <!-- Error -->
    <div x-show="error" class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 text-sm">
      <i class="fas fa-triangle-exclamation mr-1"></i> <span x-text="error"></span>
    </div>

    <template x-if="!error">
      <div class="space-y-5">

        <!-- ── Datos del cliente / financiación ──────────────────────────── -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-3">
            <div class="flex justify-between border-b border-gray-100 pb-2">
              <span class="text-xs text-slate-500 font-medium">Cliente</span>
              <span class="text-sm font-semibold text-slate-900" x-text="d.cliente"></span>
            </div>
            <div class="flex justify-between border-b border-gray-100 pb-2">
              <span class="text-xs text-slate-500 font-medium">Asesor</span>
              <span class="text-sm font-semibold text-slate-900" x-text="d.asesor"></span>
            </div>
            <div class="flex justify-between border-b border-gray-100 pb-2">
              <span class="text-xs text-slate-500 font-medium">Tipo de Crédito</span>
              <span class="text-sm text-slate-700" x-text="d.credito || '—'"></span>
            </div>
            <div class="flex justify-between border-b border-gray-100 pb-2">
              <span class="text-xs text-slate-500 font-medium">Financiera</span>
              <span class="text-sm text-slate-700" x-text="d.financiera_cred || '—'"></span>
            </div>
            <div class="flex justify-between">
              <span class="text-xs text-slate-500 font-medium">Monto financiación</span>
              <span class="text-sm font-semibold text-slate-900 num">$ <span x-text="money(d.monto_cred)"></span></span>
            </div>
          </div>
        </div>

        <!-- ── KPIs montos ───────────────────────────────────────────────── -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="rounded-xl shadow-sm border border-slate-200 p-4 flex items-center gap-3 bg-gradient-to-br from-slate-50 to-white">
            <div class="w-11 h-11 rounded-xl bg-slate-200 text-slate-600 flex items-center justify-center flex-shrink-0">
              <i class="fas fa-file-invoice"></i>
            </div>
            <div class="min-w-0">
              <p class="text-xs text-slate-500 font-medium">Monto Operación</p>
              <p class="text-xl font-bold text-slate-900 num">$ <span x-text="money(d.monto_operacion)"></span></p>
            </div>
          </div>
          <div class="rounded-xl shadow-sm border border-emerald-100 p-4 flex items-center gap-3 bg-gradient-to-br from-emerald-50 to-white">
            <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
              <i class="fas fa-circle-check"></i>
            </div>
            <div class="min-w-0">
              <p class="text-xs text-slate-500 font-medium">Pagado</p>
              <p class="text-xl font-bold text-emerald-700 num">$ <span x-text="money(d.pagado)"></span></p>
            </div>
          </div>
          <div class="rounded-xl shadow-sm border p-4 flex items-center gap-3"
               :class="d.a_cancelar > 0 ? 'border-amber-100 bg-gradient-to-br from-amber-50 to-white' : 'border-emerald-100 bg-gradient-to-br from-emerald-50 to-white'">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                 :class="d.a_cancelar > 0 ? 'bg-amber-100 text-amber-600' : 'bg-emerald-100 text-emerald-600'">
              <i class="fas fa-hourglass-half"></i>
            </div>
            <div class="min-w-0">
              <p class="text-xs text-slate-500 font-medium">A cancelar</p>
              <p class="text-xl font-bold num" :class="d.a_cancelar > 0 ? 'text-amber-700' : 'text-emerald-700'">
                $ <span x-text="money(d.a_cancelar)"></span>
              </p>
            </div>
          </div>
        </div>

        <!-- Barra de progreso pagado/operación -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
          <div class="flex justify-between text-xs text-slate-500 mb-1.5">
            <span>Avance de pago</span>
            <span class="font-semibold text-slate-700" x-text="pct() + '%'"></span>
          </div>
          <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden">
            <div class="h-full rounded-full transition-all"
                 :class="pct() >= 100 ? 'bg-emerald-500' : 'bg-blue-500'"
                 :style="`width:${Math.min(pct(),100)}%`"></div>
          </div>
        </div>

        <!-- ── Tabla de pagos ────────────────────────────────────────────── -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
          <div class="px-5 py-3 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-800">
              <i class="fas fa-receipt text-slate-400 mr-1"></i> Detalle de pagos
              <span class="text-slate-400 font-normal" x-text="'(' + d.pagos.length + ')'"></span>
            </h2>
            <button x-show="puedeEditar" @click="abrirAlta()"
                    class="flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium px-3 py-1.5 rounded-lg">
              <i class="fas fa-plus"></i> Registrar Pago
            </button>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wide border-b border-gray-200">
                <tr>
                  <th class="px-3 py-2.5 text-left font-semibold">Nro</th>
                  <th class="px-3 py-2.5 text-left font-semibold">Fecha</th>
                  <th class="px-3 py-2.5 text-left font-semibold">Tipo</th>
                  <th class="px-3 py-2.5 text-left font-semibold">Modo</th>
                  <th class="px-3 py-2.5 text-left font-semibold">Financiera</th>
                  <th class="px-3 py-2.5 text-left font-semibold">Nro Rec.</th>
                  <th class="px-3 py-2.5 text-right font-semibold">Monto</th>
                  <th class="px-3 py-2.5 text-left font-semibold">Observación</th>
                  <th x-show="puedeEditar" class="px-3 py-2.5 text-center font-semibold">Acciones</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <template x-for="p in (loading ? [] : d.pagos)" :key="p.idpago">
                  <tr class="hover:bg-blue-50/40">
                    <td class="px-3 py-2 text-slate-400" x-text="p.idpago"></td>
                    <td class="px-3 py-2 whitespace-nowrap" x-text="fecha(p.fecha)"></td>
                    <td class="px-3 py-2" x-text="p.tipo"></td>
                    <td class="px-3 py-2" x-text="p.modo"></td>
                    <td class="px-3 py-2 text-slate-600" x-text="p.financiera"></td>
                    <td class="px-3 py-2 text-slate-600" x-text="p.nrorecibo"></td>
                    <td class="px-3 py-2 text-right num font-semibold text-slate-900" x-text="money(p.monto)"></td>
                    <td class="px-3 py-2 text-slate-600" x-text="p.obs"></td>
                    <td x-show="puedeEditar" class="px-3 py-2 whitespace-nowrap text-center">
                      <button @click="abrirEdicion(p)" title="Editar"
                              class="w-7 h-7 rounded-md text-slate-400 hover:text-blue-600 hover:bg-blue-50">
                        <i class="fas fa-pen-to-square"></i>
                      </button>
                      <button @click="eliminar(p)" title="Eliminar"
                              class="w-7 h-7 rounded-md text-slate-400 hover:text-red-600 hover:bg-red-50">
                        <i class="fas fa-trash-can"></i>
                      </button>
                    </td>
                  </tr>
                </template>

                <tr x-show="loading">
                  <td colspan="9" class="px-3 py-8 text-center text-slate-400">
                    <i class="fas fa-circle-notch fa-spin"></i> Cargando…
                  </td>
                </tr>
                <tr x-show="!loading && d.pagos.length === 0">
                  <td colspan="9" class="px-3 py-8 text-center text-slate-400">
                    <i class="fas fa-inbox mr-1"></i> Sin pagos registrados.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <p class="text-xs text-slate-400 text-center">
          Módulo nuevo. ¿Preferís la pantalla clásica?
          <a :href="'../ventas/web/pago.php?IDrecord=' + idcliente" class="text-blue-600 hover:underline font-medium">Ir a la versión anterior</a>.
        </p>
      </div>
    </template>
  </main>

  <!-- ── Modal Registrar / Editar pago ─────────────────────────────────────── -->
  <div x-show="modal.open" x-cloak
       class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4"
       @keydown.escape.window="modal.open = false">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden" @click.outside="modal.open = false">
      <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 bg-slate-50">
        <h3 class="text-sm font-bold text-slate-900" x-text="modal.form.idpago ? 'Editar pago' : 'Registrar pago'"></h3>
        <button @click="modal.open = false" class="text-slate-400 hover:text-slate-700"><i class="fas fa-xmark text-lg"></i></button>
      </div>

      <div class="p-5 grid grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Fecha *</label>
          <input type="date" x-model="modal.form.fecha"
                 class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Monto *</label>
          <input type="number" step="0.01" x-model="modal.form.monto" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 text-right focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Tipo de pago *</label>
          <select x-model.number="modal.form.tipo" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
            <option value="0"></option>
            <template x-for="t in d.lookups.tipos" :key="t.id"><option :value="t.id" x-text="t.nombre"></option></template>
          </select>
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Modo de pago *</label>
          <select x-model.number="modal.form.modo" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
            <option value="0"></option>
            <template x-for="m in d.lookups.modos" :key="m.id"><option :value="m.id" x-text="m.nombre"></option></template>
          </select>
        </div>
        <div class="col-span-2" x-show="modal.form.modo == 3 || modal.form.modo == 4">
          <label class="block text-xs font-medium text-slate-500 mb-1">Financiera * <span class="text-amber-600">(requerida para Crédito/Leasing)</span></label>
          <select x-model.number="modal.form.finan" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
            <option value="0"></option>
            <template x-for="f in d.lookups.financieras" :key="f.id"><option :value="f.id" x-text="f.nombre"></option></template>
          </select>
        </div>
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Nro Recibo</label>
          <input type="text" x-model="modal.form.nrorecibo" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        <div class="flex items-end">
          <p x-show="modal.form.tipo == 3" class="text-xs text-red-600"><i class="fas fa-triangle-exclamation"></i> Tipo "Cancelación": marcará la unidad como cancelada.</p>
        </div>
        <div class="col-span-2">
          <label class="block text-xs font-medium text-slate-500 mb-1">Observación</label>
          <textarea x-model="modal.form.obs" rows="3" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
        </div>
      </div>

      <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-gray-200 bg-slate-50">
        <button @click="modal.open = false" class="text-sm text-slate-600 border border-gray-300 rounded-lg px-4 py-2 hover:bg-gray-100">Cancelar</button>
        <button @click="guardar()" :disabled="modal.saving"
                class="text-sm text-white bg-blue-600 hover:bg-blue-700 rounded-lg px-4 py-2 disabled:opacity-50 flex items-center gap-2">
          <i class="fas" :class="modal.saving ? 'fa-circle-notch fa-spin' : 'fa-floppy-disk'"></i>
          <span x-text="modal.saving ? 'Guardando…' : 'Guardar'"></span>
        </button>
      </div>
    </div>
  </div>

  <script>
    function estadoCuenta(idcliente, puedeEditar) {
      return {
        idcliente: idcliente,
        puedeEditar: puedeEditar,
        loading: true,
        error: '',
        modal: { open: false, saving: false, form: {} },
        d: { cliente:'', asesor:'', credito:'', financiera_cred:'', monto_cred:0,
             monto_operacion:0, pagado:0, a_cancelar:0, pagos:[],
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
          this.modal.open = true;
        },
        abrirEdicion(p) {
          this.modal.form = {
            idpago: p.idpago, fecha: p.fecha || '', monto: p.monto,
            tipo: p.tipo_id, modo: p.modo_id, finan: p.fin_id || 0,
            nrorecibo: p.nrorecibo || '', obs: p.obs || '',
          };
          this.modal.open = true;
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
  </script>
</body>
</html>
