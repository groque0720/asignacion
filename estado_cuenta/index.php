<?php
@session_start();
if (!isset($_SESSION["autentificado"]) || $_SESSION["autentificado"] !== "SI") {
    header("Location: ../login");
    exit();
}
$idsuc = (int)($_SESSION['idsuc'] ?? 0);
$fecha_actual = date('d/m/Y');
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Clientes Activos · Estado de Cuenta</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter', system-ui, sans-serif; }
    [x-cloak] { display: none !important; }
    thead th { position: sticky; top: 0; z-index: 10; background: #f1f5f9; box-shadow: inset 0 -1px 0 #e5e7eb; }
  </style>
</head>
<body class="bg-gray-100 min-h-screen text-slate-800"
      x-data="lista(<?php echo $idsuc; ?>)" x-init="load()" x-cloak>

  <!-- ── Header ──────────────────────────────────────────────────────────── -->
  <header class="bg-slate-900 text-white shadow-lg sticky top-0 z-30">
    <div class="max-w-[1400px] mx-auto px-6 py-3 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 bg-blue-600 rounded-lg flex items-center justify-center">
          <i class="fas fa-users text-sm"></i>
        </div>
        <div>
          <h1 class="text-sm font-bold leading-tight">Clientes Activos · Estado de Cuenta</h1>
          <p class="text-slate-400 text-xs">Derka y Vargas S.A.</p>
        </div>
      </div>
      <div class="text-right">
        <p class="text-[10px] text-slate-500 uppercase tracking-widest leading-none mb-0.5">Fecha</p>
        <p class="text-sm font-semibold"><?php echo $fecha_actual; ?></p>
      </div>
    </div>
  </header>

  <main class="max-w-[1400px] mx-auto px-6 py-5 space-y-5">

    <!-- ── Toolbar ───────────────────────────────────────────────────────── -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
      <div class="flex flex-wrap items-end gap-4">
        <div>
          <label class="block text-xs font-medium text-slate-500 mb-1">Sucursal</label>
          <select x-model="filtros.suc" @change="resetLoad()"
                  class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
            <template x-for="s in sucursales" :key="s.id"><option :value="s.id" x-text="s.nombre"></option></template>
          </select>
        </div>
        <div class="flex-1 min-w-[240px]">
          <label class="block text-xs font-medium text-slate-500 mb-1">Buscar</label>
          <div class="relative">
            <i class="fas fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input type="text" x-model="filtros.q" @input.debounce.400ms="resetLoad()"
                   placeholder="Cliente, documento, asesor, unidad, N.R., interno…"
                   class="w-full text-sm border border-gray-300 rounded-lg pl-9 pr-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
          </div>
        </div>
        <div class="text-sm text-slate-500">
          <span class="font-semibold text-slate-700" x-text="total"></span> clientes
        </div>
      </div>
    </div>

    <!-- ── Tabla ─────────────────────────────────────────────────────────── -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto" style="max-height: calc(100vh - 290px);">
        <table class="w-full text-sm">
          <thead class="text-slate-600 text-xs uppercase tracking-wide">
            <tr>
              <th class="px-3 py-2.5 text-left font-semibold">N.R.</th>
              <th class="px-3 py-2.5 text-left font-semibold">Unidad</th>
              <th class="px-3 py-2.5 text-left font-semibold">Asesor</th>
              <th class="px-3 py-2.5 text-left font-semibold">Cliente</th>
              <th class="px-3 py-2.5 text-left font-semibold">Modelo</th>
              <th class="px-3 py-2.5 text-center font-semibold">Crédito</th>
              <th class="px-3 py-2.5 text-center font-semibold">Pago</th>
              <th class="px-3 py-2.5 text-center font-semibold">Estado de Cuenta</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <template x-for="r in (loading ? [] : rows)" :key="r.idreserva">
              <tr class="hover:bg-blue-50/40">
                <td class="px-3 py-2 text-slate-400" x-text="r.idreserva"></td>
                <td class="px-3 py-2 text-slate-600" x-text="r.compra"></td>
                <td class="px-3 py-2" x-text="r.asesor"></td>
                <td class="px-3 py-2 font-medium text-slate-900" x-text="r.cliente"></td>
                <td class="px-3 py-2 text-slate-600" x-text="r.modelo"></td>
                <td class="px-3 py-2 text-center">
                  <span class="inline-flex items-center justify-center w-6 h-6 rounded-md text-xs"
                        :title="cred(r).t" :style="`background:${cred(r).bg};color:${cred(r).fg}`">
                    <i :class="cred(r).icon"></i>
                  </span>
                </td>
                <td class="px-3 py-2 text-center">
                  <span class="inline-block px-2 py-0.5 rounded text-xs font-medium"
                        :style="`background:${pago(r).bg};color:${pago(r).fg}`" x-text="pago(r).t"></span>
                </td>
                <td class="px-3 py-2 text-center">
                  <a :href="'cuenta.php?IDrecord=' + r.idcliente"
                     class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium px-3 py-1.5 rounded-lg">
                    <i class="fas fa-file-invoice-dollar"></i> Ver
                  </a>
                </td>
              </tr>
            </template>

            <!-- skeleton -->
            <template x-for="i in (loading ? 10 : 0)" :key="'sk'+i">
              <tr><template x-for="n in 8" :key="n"><td class="px-3 py-3"><div class="h-3 rounded bg-slate-200 animate-pulse w-4/5"></div></td></template></tr>
            </template>

            <tr x-show="!loading && rows.length === 0">
              <td colspan="8" class="px-3 py-10 text-center text-slate-400">
                <i class="fas fa-inbox text-2xl mb-2 block"></i> Sin clientes para este filtro.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Paginación -->
      <div class="flex items-center justify-between px-4 py-3 border-t border-gray-200 bg-slate-50 text-sm">
        <div class="text-slate-500"><span x-text="desde()"></span>–<span x-text="hasta()"></span> de <span x-text="total"></span></div>
        <div class="flex items-center gap-1">
          <button @click="irPagina(1)" :disabled="page===1" class="px-2.5 py-1.5 rounded-lg border border-gray-300 bg-white disabled:opacity-40 hover:bg-gray-50"><i class="fas fa-angles-left text-xs"></i></button>
          <button @click="irPagina(page-1)" :disabled="page===1" class="px-2.5 py-1.5 rounded-lg border border-gray-300 bg-white disabled:opacity-40 hover:bg-gray-50"><i class="fas fa-angle-left text-xs"></i></button>
          <span class="px-3 text-slate-600">Pág. <strong x-text="page"></strong> / <span x-text="pages||1"></span></span>
          <button @click="irPagina(page+1)" :disabled="page>=pages" class="px-2.5 py-1.5 rounded-lg border border-gray-300 bg-white disabled:opacity-40 hover:bg-gray-50"><i class="fas fa-angle-right text-xs"></i></button>
          <button @click="irPagina(pages)" :disabled="page>=pages" class="px-2.5 py-1.5 rounded-lg border border-gray-300 bg-white disabled:opacity-40 hover:bg-gray-50"><i class="fas fa-angles-right text-xs"></i></button>
          <select x-model.number="filtros.per" @change="resetLoad()" class="ml-2 border border-gray-300 rounded-lg px-2 py-1.5 bg-white">
            <option :value="25">25</option><option :value="50">50</option><option :value="100">100</option>
          </select>
        </div>
      </div>
    </div>

    <p class="text-xs text-slate-400 text-center">
      Módulo nuevo. ¿Preferís la pantalla clásica?
      <a href="../ventas/web/pagos_clientes.php" class="text-blue-600 hover:underline font-medium">Ir a la versión anterior</a>.
    </p>
  </main>

  <script>
    function lista(idsuc) {
      return {
        loading: true, rows: [], total: 0, pages: 1, page: 1,
        filtros: { suc: idsuc, q: '', per: 25 },
        sucursales: [
          { id: 0, nombre: 'Todas' }, { id: 1, nombre: 'Resistencia' },
          { id: 2, nombre: 'Sáenz Peña' }, { id: 3, nombre: 'Villa Ángela' }, { id: 4, nombre: 'Charata' },
        ],

        async load() {
          this.loading = true;
          const p = new URLSearchParams({ suc: this.filtros.suc, q: this.filtros.q, per: this.filtros.per, page: this.page });
          try {
            const res = await fetch('lista_data.php?' + p.toString(), { cache: 'no-store' });
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

        cred(r) {
          const C = { slate:['#f1f5f9','#64748b'], blue:['#dbeafe','#1d4ed8'], indigo:['#e0e7ff','#4338ca'],
                      cyan:['#cffafe','#0e7490'], amber:['#fde68a','#b45309'], green:['#bbf7d0','#047857'], red:['#fecaca','#b91c1c'] };
          const s = {
            0:['slate','fa-minus','Sin crédito'], 20:['blue','fa-file-circle-xmark','Sin papeles'],
            1:['blue','fa-inbox','Recibido'], 2:['blue','fa-paper-plane','Enviado'], 22:['indigo','fa-magnifying-glass','En análisis'],
            3:['amber','fa-triangle-exclamation','Observado'], 4:['red','fa-circle-xmark','Rechazado'],
            5:['cyan','fa-thumbs-up','Pre-aprobado'], 6:['green','fa-circle-check','Aprobado'],
            66:['amber','fa-circle-check','Aprobado observado'], 7:['green','fa-sack-dollar','Liquidado'], 70:['green','fa-sack-dollar','Liquidado'],
          }[r.credito_estado] || ['slate','fa-minus','Sin crédito'];
          return { bg: C[s[0]][0], fg: C[s[0]][1], icon: 'fas ' + s[1], t: 'Crédito: ' + s[2] };
        },
        pago(r) {
          const m = {
            0: ['#f1f5f9','#64748b','Sin pagos'], 1: ['#dbeafe','#1d4ed8','Con seña'],
            2: ['#e0e7ff','#4338ca','A cuenta'], 3: ['#bbf7d0','#047857','Cancelada'],
          }[r.estadopago] || ['#f1f5f9','#64748b','Sin pagos'];
          return { bg: m[0], fg: m[1], t: m[2] };
        },
      };
    }
  </script>
</body>
</html>
