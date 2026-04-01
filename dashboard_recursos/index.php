<?php
include("funciones/func_mysql.php");
conectar();
@session_start();

if (!isset($_SESSION["autentificado"]) || $_SESSION["autentificado"] !== "SI") {
    header("Location: ../login");
    exit();
}

// ─── Cargar datos ──────────────────────────────────────────────────────────────
$data_pendiente  = [];
$data_en_viaje   = [];
$data_con_arribo = [];
$data_asesor     = [];
$data_modelo     = [];
$total_pendiente  = 0.0;
$total_en_viaje   = 0.0;
$total_con_arribo = 0.0;

$res = mysqli_query($con, "SELECT * FROM view_asignaciones_saldo_pendiente_corregida_no_llegadas");
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) {
        $data_pendiente[] = $r;
        $total_pendiente += (float)$r['Saldo'];
    }
}

$res = mysqli_query($con, "SELECT * FROM view_asignaciones_saldo_pendiente_corregida_en_viaje");
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) {
        $data_en_viaje[] = $r;
        $total_en_viaje += (float)$r['Saldo'];
    }
}

$res = mysqli_query($con, "SELECT * FROM view_asignaciones_saldo_pendiente_corregida_llegadas");
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) {
        $data_con_arribo[] = $r;
        $total_con_arribo += (float)$r['Saldo'];
    }
}

$res = mysqli_query($con,
  "SELECT " .
  "COALESCE(NULLIF(TRIM(Asesor), ''), 'SIN ASESOR') AS Nombre, " .
  "SUM(Saldo) AS Saldo, " .
  "COUNT(*) AS Unidades " .
  "FROM view_asignaciones_saldo_pendiente_corregida " .
  "GROUP BY COALESCE(NULLIF(TRIM(Asesor), ''), 'SIN ASESOR') " .
  "ORDER BY Saldo DESC " .
  "LIMIT 10"
);
if ($res) {
  while ($r = mysqli_fetch_assoc($res)) {
    $data_asesor[] = $r;
  }
}

$res = mysqli_query($con,
  "SELECT " .
  "COALESCE(NULLIF(TRIM(Modelo), ''), 'SIN MODELO') AS Nombre, " .
  "SUM(Saldo) AS Saldo, " .
  "COUNT(*) AS Unidades " .
  "FROM view_asignaciones_saldo_pendiente_corregida " .
  "GROUP BY COALESCE(NULLIF(TRIM(Modelo), ''), 'SIN MODELO') " .
  "ORDER BY Saldo DESC " .
  "LIMIT 10"
);
if ($res) {
  while ($r = mysqli_fetch_assoc($res)) {
    $data_modelo[] = $r;
  }
}

$total_general    = $total_pendiente + $total_en_viaje + $total_con_arribo;
$total_financiado = $total_en_viaje + $total_con_arribo;

// ─── Construir tabla combinada "Todas" ────────────────────────────────────────
$todas_map = [];
foreach ($data_pendiente as $r) {
    $id = $r['IdSucursal'];
    $todas_map[$id] = ['IdSucursal' => $id, 'Sucursal' => $r['Sucursal'], 'Saldo' => (float)$r['Saldo']];
}
foreach ($data_en_viaje as $r) {
    $id = $r['IdSucursal'];
    if (isset($todas_map[$id])) {
        $todas_map[$id]['Saldo'] += (float)$r['Saldo'];
    } else {
        $todas_map[$id] = ['IdSucursal' => $id, 'Sucursal' => $r['Sucursal'], 'Saldo' => (float)$r['Saldo']];
    }
}
foreach ($data_con_arribo as $r) {
    $id = $r['IdSucursal'];
    if (isset($todas_map[$id])) {
        $todas_map[$id]['Saldo'] += (float)$r['Saldo'];
    } else {
        $todas_map[$id] = ['IdSucursal' => $id, 'Sucursal' => $r['Sucursal'], 'Saldo' => (float)$r['Saldo']];
    }
}
$todas = array_values($todas_map);
usort($todas, function($a, $b) { return strcmp($a['Sucursal'], $b['Sucursal']); });

// ─── Funciones de apoyo ────────────────────────────────────────────────────────
function fmt(float $n): string {
    return '$ ' . number_format($n, 0, ',', '.');
}
function fmtM(float $n): string {
    return '$ ' . number_format($n / 1000000, 1, ',', '.') . 'M';
}
function pct(float $part, float $total): float {
    if ($total <= 0) return 0.0;
    return round(($part / $total) * 100, 1);
}
function riskBadge(float $saldo, float $total): string {
    $p = $total > 0 ? ($saldo / $total) * 100 : 0;
    if ($p >= 40) {
        return '<span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700 ring-1 ring-inset ring-red-200">Alto</span>';
    }
    if ($p >= 20) {
        return '<span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-200">Medio</span>';
    }
    return '<span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200">Bajo</span>';
}

// ─── Datos para Chart.js ───────────────────────────────────────────────────────
$chart_labels = array_values(array_column($todas, 'Sucursal'));
$idx_p  = array_column($data_pendiente,  'Saldo', 'Sucursal');
$idx_ev = array_column($data_en_viaje,   'Saldo', 'Sucursal');
$idx_ca = array_column($data_con_arribo, 'Saldo', 'Sucursal');
$chart_p  = array_values(array_map(function($l) use ($idx_p)  { return (float)(isset($idx_p[$l])  ? $idx_p[$l]  : 0); }, $chart_labels));
$chart_ev = array_values(array_map(function($l) use ($idx_ev) { return (float)(isset($idx_ev[$l]) ? $idx_ev[$l] : 0); }, $chart_labels));
$chart_ca = array_values(array_map(function($l) use ($idx_ca) { return (float)(isset($idx_ca[$l]) ? $idx_ca[$l] : 0); }, $chart_labels));

$fecha_actual = date('d/m/Y');
$hora_actual  = date('H:i:s');
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Contable · Recursos DyV</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter', system-ui, sans-serif; }

    .tab-btn {
      color: #64748b;
      border-bottom: 2px solid transparent;
      margin-bottom: -1px;
      padding: 0.7rem 1.1rem;
      font-size: 0.8125rem;
      font-weight: 500;
      transition: color 0.15s, border-color 0.15s;
      cursor: pointer;
      background: none;
      border-top: none;
      border-left: none;
      border-right: none;
      white-space: nowrap;
    }
    .tab-btn:hover { color: #334155; }
    .tab-btn.active { color: #2563eb; border-bottom-color: #2563eb; }

    .print-show { display: none; }

    @media print {
      .no-print  { display: none !important; }
      body       { background: #fff !important; }
      .tab-pane  { display: block !important; page-break-inside: avoid; }
      .chart-wrap { display: none !important; }
      .print-show { display: block !important; }
      .shadow-sm  { box-shadow: none !important; }
      header.sticky { position: static !important; }
      .metrics-bar { display: none !important; }
    }
  </style>
</head>
<body class="bg-gray-100 min-h-screen">

<!-- ── Print-only header ─────────────────────────────────────────────────────── -->
<div class="print-show px-8 pt-6 pb-3 border-b border-gray-300">
  <h1 class="text-lg font-bold text-slate-900">Dashboard Contable · Recursos — Derka y Vargas S.A.</h1>
  <p class="text-xs text-gray-500 mt-0.5">Generado el <?php echo $fecha_actual; ?> a las <?php echo $hora_actual; ?></p>
</div>

<!-- ── Header ────────────────────────────────────────────────────────────────── -->
<header class="no-print bg-slate-900 text-white shadow-xl sticky top-0 z-20">
  <div class="max-w-screen-xl mx-auto px-8 py-3.5 flex items-center justify-between">
    <div class="flex items-center gap-3">
      <div class="w-9 h-9 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
        <i class="fas fa-chart-bar text-sm"></i>
      </div>
      <div>
        <h1 class="text-sm font-bold leading-tight">Dashboard Contable · Recursos</h1>
        <p class="text-slate-400 text-xs">Derka y Vargas S.A.</p>
      </div>
    </div>

    <div class="flex items-center gap-5">
      <div class="text-right">
        <p class="text-xs text-slate-500 uppercase tracking-widest leading-none mb-0.5">Fecha</p>
        <p class="text-sm font-semibold"><?php echo $fecha_actual; ?></p>
      </div>
      <div class="w-px h-7 bg-slate-700"></div>
      <div class="text-right">
        <p class="text-xs text-slate-500 uppercase tracking-widest leading-none mb-0.5">Actualizado</p>
        <p class="text-sm font-semibold"><?php echo $hora_actual; ?></p>
      </div>
      <div class="w-px h-7 bg-slate-700"></div>
      <div class="flex items-center gap-2">
        <a href="/asignacion/costos_recursos_completa_resumen.php" target="_blank"
           class="flex items-center gap-1.5 bg-slate-700 hover:bg-slate-600 px-3 py-1.5 rounded-md text-xs font-medium transition-colors">
          <i class="fas fa-file-alt"></i> Resumen Viejo
        </a>
        <a href="pdf/resumen_ejecutivo.php" target="_blank"
           class="flex items-center gap-1.5 bg-slate-700 hover:bg-slate-600 px-3 py-1.5 rounded-md text-xs font-medium transition-colors">
          <i class="fas fa-chart-line"></i> Resumen Ejecutivo
        </a>
        <button onclick="window.print()"
                class="flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 px-3 py-1.5 rounded-md text-xs font-medium transition-colors">
          <i class="fas fa-print"></i> Imprimir
        </button>
      </div>
    </div>
  </div>
</header>

<main class="max-w-screen-xl mx-auto px-8 py-6 space-y-5">

  <!-- ── Métricas de Gestión ────────────────────────────────────────────────── -->
  <div class="metrics-bar grid grid-cols-3 gap-4">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex items-center gap-4">
      <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center flex-shrink-0">
        <i class="fas fa-check-circle text-emerald-500"></i>
      </div>
      <div>
        <p class="text-xs text-slate-500 uppercase tracking-wider font-medium">Tasa de Arribo</p>
        <p class="text-xl font-bold text-slate-900"><?php echo pct($total_con_arribo, $total_financiado); ?>%</p>
        <p class="text-xs text-slate-400">Unidades financiadas que llegaron</p>
      </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex items-center gap-4">
      <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
        <i class="fas fa-percentage text-blue-500"></i>
      </div>
      <div>
        <p class="text-xs text-slate-500 uppercase tracking-wider font-medium">Cartera Financiada</p>
        <p class="text-xl font-bold text-slate-900"><?php echo pct($total_financiado, $total_general); ?>%</p>
        <p class="text-xs text-slate-400">Del total con TASA activada</p>
      </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex items-center gap-4">
      <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center flex-shrink-0">
        <i class="fas fa-exclamation-triangle text-amber-500"></i>
      </div>
      <div>
        <p class="text-xs text-slate-500 uppercase tracking-wider font-medium">Pendiente sin Activar</p>
        <p class="text-xl font-bold text-slate-900"><?php echo pct($total_pendiente, $total_general); ?>%</p>
        <p class="text-xs text-slate-400">Del total con TASA pendiente de pago</p>
      </div>
    </div>
  </div>

  <!-- ── KPI Cards ─────────────────────────────────────────────────────────── -->
  <div class="grid grid-cols-4 gap-5">

    <!-- Pendiente Pago TASA -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <div class="h-1 bg-amber-500"></div>
      <div class="p-5">
        <div class="flex items-start justify-between">
          <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider leading-tight">Pendiente Pago TASA</p>
          <div class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center flex-shrink-0">
            <i class="fas fa-clock text-amber-500 text-sm"></i>
          </div>
        </div>
        <p class="text-2xl font-bold text-slate-900 mt-2 font-mono"><?php echo fmt($total_pendiente); ?></p>
        <p class="text-xs text-slate-400 mt-0.5"><?php echo fmtM($total_pendiente); ?></p>
        <div class="mt-3">
          <div class="flex justify-between text-xs text-slate-500 mb-1">
            <span>% del total</span>
            <span class="font-semibold text-amber-600"><?php echo pct($total_pendiente, $total_general); ?>%</span>
          </div>
          <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
            <div class="h-full bg-amber-500 rounded-full" style="width:<?php echo pct($total_pendiente, $total_general); ?>%"></div>
          </div>
        </div>
        <p class="text-xs text-slate-400 mt-2.5"><?php echo count($data_pendiente); ?> sucursales activas</p>
      </div>
    </div>

    <!-- En Viaje -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <div class="h-1 bg-blue-500"></div>
      <div class="p-5">
        <div class="flex items-start justify-between">
          <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider leading-tight">En Viaje</p>
          <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
            <i class="fas fa-truck text-blue-500 text-sm"></i>
          </div>
        </div>
        <p class="text-2xl font-bold text-slate-900 mt-2 font-mono"><?php echo fmt($total_en_viaje); ?></p>
        <p class="text-xs text-slate-400 mt-0.5"><?php echo fmtM($total_en_viaje); ?></p>
        <div class="mt-3">
          <div class="flex justify-between text-xs text-slate-500 mb-1">
            <span>% del total</span>
            <span class="font-semibold text-blue-600"><?php echo pct($total_en_viaje, $total_general); ?>%</span>
          </div>
          <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
            <div class="h-full bg-blue-500 rounded-full" style="width:<?php echo pct($total_en_viaje, $total_general); ?>%"></div>
          </div>
        </div>
        <p class="text-xs text-slate-400 mt-2.5"><?php echo count($data_en_viaje); ?> sucursales activas</p>
      </div>
    </div>

    <!-- Con Arribo -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <div class="h-1 bg-violet-500"></div>
      <div class="p-5">
        <div class="flex items-start justify-between">
          <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider leading-tight">Con Arribo</p>
          <div class="w-8 h-8 bg-violet-50 rounded-lg flex items-center justify-center flex-shrink-0">
            <i class="fas fa-warehouse text-violet-500 text-sm"></i>
          </div>
        </div>
        <p class="text-2xl font-bold text-slate-900 mt-2 font-mono"><?php echo fmt($total_con_arribo); ?></p>
        <p class="text-xs text-slate-400 mt-0.5"><?php echo fmtM($total_con_arribo); ?></p>
        <div class="mt-3">
          <div class="flex justify-between text-xs text-slate-500 mb-1">
            <span>% del total</span>
            <span class="font-semibold text-violet-600"><?php echo pct($total_con_arribo, $total_general); ?>%</span>
          </div>
          <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
            <div class="h-full bg-violet-500 rounded-full" style="width:<?php echo pct($total_con_arribo, $total_general); ?>%"></div>
          </div>
        </div>
        <p class="text-xs text-slate-400 mt-2.5"><?php echo count($data_con_arribo); ?> sucursales activas</p>
      </div>
    </div>

    <!-- Exposición Total (dark card) -->
    <div class="bg-slate-900 rounded-xl shadow-sm p-5 text-white">
      <div class="flex items-start justify-between">
        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider leading-tight">Exposición Total</p>
        <div class="w-8 h-8 bg-slate-700 rounded-lg flex items-center justify-center flex-shrink-0">
          <i class="fas fa-coins text-slate-300 text-sm"></i>
        </div>
      </div>
      <p class="text-2xl font-bold mt-2 font-mono"><?php echo fmt($total_general); ?></p>
      <p class="text-xs text-slate-400 mt-0.5"><?php echo fmtM($total_general); ?></p>
      <p class="text-xs text-slate-500 mt-1"><?php echo count($todas); ?> sucursales · <?php echo date('d/m/Y'); ?></p>
      <div class="mt-3 grid grid-cols-3 gap-1.5 text-center">
        <div class="bg-slate-800 rounded-md p-2">
          <p class="text-xs text-slate-400 leading-tight">Pend.</p>
          <p class="text-sm font-bold text-amber-400 mt-0.5"><?php echo pct($total_pendiente, $total_general); ?>%</p>
        </div>
        <div class="bg-slate-800 rounded-md p-2">
          <p class="text-xs text-slate-400 leading-tight">Viaje</p>
          <p class="text-sm font-bold text-blue-400 mt-0.5"><?php echo pct($total_en_viaje, $total_general); ?>%</p>
        </div>
        <div class="bg-slate-800 rounded-md p-2">
          <p class="text-xs text-slate-400 leading-tight">Arribo</p>
          <p class="text-sm font-bold text-violet-400 mt-0.5"><?php echo pct($total_con_arribo, $total_general); ?>%</p>
        </div>
      </div>
    </div>
  </div>

  <!-- ── Gráficos ───────────────────────────────────────────────────────────── -->
  <div class="chart-wrap grid grid-cols-5 gap-5">

    <!-- Gráfico de barras agrupadas -->
    <div class="col-span-3 bg-white rounded-xl shadow-sm border border-gray-200 p-5">
      <div class="flex items-start justify-between mb-4">
        <div>
          <h2 class="text-sm font-semibold text-slate-800">Saldo por Sucursal y Estado</h2>
          <p class="text-xs text-slate-500 mt-0.5">Comparativo de exposición financiera por categoría</p>
        </div>
        <span class="text-xs bg-gray-100 text-slate-500 px-2 py-1 rounded-md font-medium">Act. <?php echo $hora_actual; ?></span>
      </div>
      <div style="position:relative; height:230px;">
        <canvas id="barChart"></canvas>
      </div>
    </div>

    <!-- Gráfico de dona -->
    <div class="col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex flex-col">
      <div class="mb-4">
        <h2 class="text-sm font-semibold text-slate-800">Distribución por Categoría</h2>
        <p class="text-xs text-slate-500 mt-0.5">Composición del total de exposición</p>
      </div>
      <div class="flex-1 flex items-center justify-center gap-8">
        <div style="position:relative; width:155px; height:155px; flex-shrink:0;">
          <canvas id="donutChart"></canvas>
          <div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;pointer-events:none;">
            <span style="font-size:0.65rem;color:#94a3b8;font-family:Inter,sans-serif;">Total</span>
            <span style="font-size:0.8rem;font-weight:700;color:#1e293b;font-family:Inter,sans-serif;"><?php echo fmtM($total_general); ?></span>
          </div>
        </div>
        <div class="space-y-3.5">
          <div class="flex items-center gap-2.5">
            <div class="w-3 h-3 rounded-sm bg-amber-500 flex-shrink-0"></div>
            <div>
              <p class="text-xs text-slate-500 leading-tight">Pendiente TASA</p>
              <p class="text-sm font-bold text-slate-800"><?php echo pct($total_pendiente, $total_general); ?>%</p>
            </div>
          </div>
          <div class="flex items-center gap-2.5">
            <div class="w-3 h-3 rounded-sm bg-blue-500 flex-shrink-0"></div>
            <div>
              <p class="text-xs text-slate-500 leading-tight">En Viaje</p>
              <p class="text-sm font-bold text-slate-800"><?php echo pct($total_en_viaje, $total_general); ?>%</p>
            </div>
          </div>
          <div class="flex items-center gap-2.5">
            <div class="w-3 h-3 rounded-sm bg-violet-500 flex-shrink-0"></div>
            <div>
              <p class="text-xs text-slate-500 leading-tight">Con Arribo</p>
              <p class="text-sm font-bold text-slate-800"><?php echo pct($total_con_arribo, $total_general); ?>%</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ── Análisis por Asesor y Modelo ─────────────────────────────────────── -->
  <div class="grid grid-cols-2 gap-5">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
      <div class="flex items-start justify-between mb-4">
        <div>
          <h2 class="text-sm font-semibold text-slate-800">Exposición por Asesor</h2>
          <p class="text-xs text-slate-500 mt-0.5">Top 10 responsables por saldo consolidado</p>
        </div>
        <span class="text-xs bg-gray-100 text-slate-500 px-2 py-1 rounded-md font-medium">Ranking</span>
      </div>
      <table class="w-full text-sm">
        <thead>
          <tr class="text-xs text-slate-400 uppercase tracking-wide border-b border-gray-100">
            <th class="text-left py-2.5 font-medium pl-1">Asesor</th>
            <th class="text-right py-2.5 font-medium">Saldo</th>
            <th class="text-right py-2.5 font-medium">Unid.</th>
            <th class="text-right py-2.5 font-medium pr-1">% Total</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          <?php foreach ($data_asesor as $r):
            $s = (float)$r['Saldo'];
            $p = pct($s, $total_general); ?>
          <tr class="hover:bg-gray-50 transition-colors">
            <td class="py-2.5 text-slate-800 pl-1 font-medium"><?php echo htmlspecialchars($r['Nombre']); ?></td>
            <td class="py-2.5 text-right font-mono font-semibold text-slate-900"><?php echo fmt($s); ?></td>
            <td class="py-2.5 text-right text-slate-600"><?php echo (int)$r['Unidades']; ?></td>
            <td class="py-2.5 text-right pr-1 text-slate-600"><?php echo $p; ?>%</td>
          </tr>
          <?php endforeach; ?>
          <?php if (count($data_asesor) === 0): ?>
          <tr>
            <td colspan="4" class="py-6 text-center text-slate-400">Sin datos por asesor.</td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
      <div class="flex items-start justify-between mb-4">
        <div>
          <h2 class="text-sm font-semibold text-slate-800">Exposición por Modelo</h2>
          <p class="text-xs text-slate-500 mt-0.5">Top 10 modelos por saldo consolidado</p>
        </div>
        <span class="text-xs bg-gray-100 text-slate-500 px-2 py-1 rounded-md font-medium">Ranking</span>
      </div>
      <table class="w-full text-sm">
        <thead>
          <tr class="text-xs text-slate-400 uppercase tracking-wide border-b border-gray-100">
            <th class="text-left py-2.5 font-medium pl-1">Modelo</th>
            <th class="text-right py-2.5 font-medium">Saldo</th>
            <th class="text-right py-2.5 font-medium">Unid.</th>
            <th class="text-right py-2.5 font-medium pr-1">% Total</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          <?php foreach ($data_modelo as $r):
            $s = (float)$r['Saldo'];
            $p = pct($s, $total_general); ?>
          <tr class="hover:bg-gray-50 transition-colors">
            <td class="py-2.5 text-slate-800 pl-1 font-medium"><?php echo htmlspecialchars($r['Nombre']); ?></td>
            <td class="py-2.5 text-right font-mono font-semibold text-slate-900"><?php echo fmt($s); ?></td>
            <td class="py-2.5 text-right text-slate-600"><?php echo (int)$r['Unidades']; ?></td>
            <td class="py-2.5 text-right pr-1 text-slate-600"><?php echo $p; ?>%</td>
          </tr>
          <?php endforeach; ?>
          <?php if (count($data_modelo) === 0): ?>
          <tr>
            <td colspan="4" class="py-6 text-center text-slate-400">Sin datos por modelo.</td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ── Tablas detalladas ──────────────────────────────────────────────────── -->
  <div id="card-tabs-detalle" class="bg-white rounded-xl shadow-sm border border-gray-200">
    <!-- Tab nav -->
    <div class="border-b border-gray-200 px-5 flex items-center no-print">
      <button class="tab-btn active" data-tab="pendiente">
        <i class="fas fa-clock mr-1.5 text-amber-400"></i>Pendiente Pago TASA
      </button>
      <button class="tab-btn" data-tab="enviaje">
        <i class="fas fa-truck mr-1.5 text-blue-400"></i>En Viaje
      </button>
      <button class="tab-btn" data-tab="conarribo">
        <i class="fas fa-warehouse mr-1.5 text-violet-400"></i>Con Arribo
      </button>
      <button class="tab-btn" data-tab="todas">
        <i class="fas fa-layer-group mr-1.5 text-slate-400"></i>Todas (Total)
      </button>
    </div>

    <!-- ── Tab: Pendiente Pago TASA ────────────────────────────────────────── -->
    <div id="tab-pendiente" class="tab-pane p-5">
      <div class="print-show text-xs font-bold text-slate-600 uppercase tracking-wider pb-2 mb-3 border-b border-gray-200">
        <i class="fas fa-clock text-amber-500 mr-1"></i> Pendiente Pago TASA
      </div>
      <table class="w-full text-sm">
        <thead>
          <tr class="text-xs text-slate-400 uppercase tracking-wide border-b border-gray-100">
            <th class="text-left py-2.5 font-medium pl-1">Sucursal</th>
            <th class="text-right py-2.5 font-medium">Saldo</th>
            <th class="py-2.5 font-medium text-center" style="width:140px;">Distribución</th>
            <th class="text-right py-2.5 font-medium pr-4">% Cat.</th>
            <th class="text-center py-2.5 font-medium">Riesgo</th>
            <th class="text-center py-2.5 font-medium no-print">Detalle</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          <?php foreach ($data_pendiente as $r):
            $p = pct((float)$r['Saldo'], $total_pendiente); ?>
          <tr class="hover:bg-gray-50 transition-colors">
            <td class="py-3 font-medium text-slate-800 pl-1"><?php echo htmlspecialchars($r['Sucursal']); ?></td>
            <td class="py-3 text-right font-mono font-semibold text-slate-900"><?php echo fmt((float)$r['Saldo']); ?></td>
            <td class="py-3 px-3">
              <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-amber-400 rounded-full transition-all" style="width:<?php echo $p; ?>%"></div>
              </div>
            </td>
            <td class="py-3 text-right text-slate-500 pr-4"><?php echo $p; ?>%</td>
            <td class="py-3 text-center"><?php echo riskBadge((float)$r['Saldo'], $total_pendiente); ?></td>
            <td class="py-3 text-center no-print">
              <a href="/asignacion/costos_recursos_pendiente_pago.php?sucursalId=<?php echo (int)$r['IdSucursal']; ?>"
                 target="_blank"
                 class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 text-xs font-medium hover:underline">
                <i class="fas fa-file-pdf"></i> Ver
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr class="border-t-2 border-gray-200 bg-amber-50">
            <td class="py-3 font-bold text-slate-800 pl-1">Total DyV</td>
            <td class="py-3 text-right font-mono font-bold text-slate-900"><?php echo fmt($total_pendiente); ?></td>
            <td class="py-3 px-3">
              <div class="h-2 bg-amber-200 rounded-full"></div>
            </td>
            <td class="py-3 text-right font-bold text-slate-600 pr-4">100%</td>
            <td></td>
            <td class="py-3 text-center no-print">
              <a href="/asignacion/costos_recursos_pendiente_pago.php"
                 target="_blank"
                 class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 text-xs font-medium hover:underline">
                <i class="fas fa-file-pdf"></i> Todo DyV
              </a>
            </td>
          </tr>
        </tfoot>
      </table>
    </div>

    <!-- ── Tab: En Viaje ───────────────────────────────────────────────────── -->
    <div id="tab-enviaje" class="tab-pane hidden p-5">
      <div class="print-show text-xs font-bold text-slate-600 uppercase tracking-wider pb-2 mb-3 border-b border-gray-200">
        <i class="fas fa-truck text-blue-500 mr-1"></i> En Viaje
      </div>
      <table class="w-full text-sm">
        <thead>
          <tr class="text-xs text-slate-400 uppercase tracking-wide border-b border-gray-100">
            <th class="text-left py-2.5 font-medium pl-1">Sucursal</th>
            <th class="text-right py-2.5 font-medium">Saldo</th>
            <th class="py-2.5 font-medium text-center" style="width:140px;">Distribución</th>
            <th class="text-right py-2.5 font-medium pr-4">% Cat.</th>
            <th class="text-center py-2.5 font-medium">Riesgo</th>
            <th class="text-center py-2.5 font-medium no-print">Detalle</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          <?php foreach ($data_en_viaje as $r):
            $p = pct((float)$r['Saldo'], $total_en_viaje); ?>
          <tr class="hover:bg-gray-50 transition-colors">
            <td class="py-3 font-medium text-slate-800 pl-1"><?php echo htmlspecialchars($r['Sucursal']); ?></td>
            <td class="py-3 text-right font-mono font-semibold text-slate-900"><?php echo fmt((float)$r['Saldo']); ?></td>
            <td class="py-3 px-3">
              <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-blue-400 rounded-full transition-all" style="width:<?php echo $p; ?>%"></div>
              </div>
            </td>
            <td class="py-3 text-right text-slate-500 pr-4"><?php echo $p; ?>%</td>
            <td class="py-3 text-center"><?php echo riskBadge((float)$r['Saldo'], $total_en_viaje); ?></td>
            <td class="py-3 text-center no-print">
              <a href="/asignacion/costos_recursos_pendiente_en_viaje.php?sucursalId=<?php echo (int)$r['IdSucursal']; ?>"
                 target="_blank"
                 class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 text-xs font-medium hover:underline">
                <i class="fas fa-file-pdf"></i> Ver
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr class="border-t-2 border-gray-200 bg-blue-50">
            <td class="py-3 font-bold text-slate-800 pl-1">Total DyV</td>
            <td class="py-3 text-right font-mono font-bold text-slate-900"><?php echo fmt($total_en_viaje); ?></td>
            <td class="py-3 px-3">
              <div class="h-2 bg-blue-200 rounded-full"></div>
            </td>
            <td class="py-3 text-right font-bold text-slate-600 pr-4">100%</td>
            <td></td>
            <td class="py-3 text-center no-print">
              <a href="/asignacion/costos_recursos_pendiente_con_arribo.php"
                 target="_blank"
                 class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 text-xs font-medium hover:underline">
                <i class="fas fa-file-pdf"></i> Todo DyV
              </a>
            </td>
          </tr>
        </tfoot>
      </table>
    </div>

    <!-- ── Tab: Con Arribo ─────────────────────────────────────────────────── -->
    <div id="tab-conarribo" class="tab-pane hidden p-5">
      <div class="print-show text-xs font-bold text-slate-600 uppercase tracking-wider pb-2 mb-3 border-b border-gray-200">
        <i class="fas fa-warehouse text-violet-500 mr-1"></i> Con Arribo
      </div>
      <table class="w-full text-sm">
        <thead>
          <tr class="text-xs text-slate-400 uppercase tracking-wide border-b border-gray-100">
            <th class="text-left py-2.5 font-medium pl-1">Sucursal</th>
            <th class="text-right py-2.5 font-medium">Saldo</th>
            <th class="py-2.5 font-medium text-center" style="width:140px;">Distribución</th>
            <th class="text-right py-2.5 font-medium pr-4">% Cat.</th>
            <th class="text-center py-2.5 font-medium">Riesgo</th>
            <th class="text-center py-2.5 font-medium no-print">Detalle</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          <?php foreach ($data_con_arribo as $r):
            $p = pct((float)$r['Saldo'], $total_con_arribo); ?>
          <tr class="hover:bg-gray-50 transition-colors">
            <td class="py-3 font-medium text-slate-800 pl-1"><?php echo htmlspecialchars($r['Sucursal']); ?></td>
            <td class="py-3 text-right font-mono font-semibold text-slate-900"><?php echo fmt((float)$r['Saldo']); ?></td>
            <td class="py-3 px-3">
              <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-violet-400 rounded-full transition-all" style="width:<?php echo $p; ?>%"></div>
              </div>
            </td>
            <td class="py-3 text-right text-slate-500 pr-4"><?php echo $p; ?>%</td>
            <td class="py-3 text-center"><?php echo riskBadge((float)$r['Saldo'], $total_con_arribo); ?></td>
            <td class="py-3 text-center no-print">
              <a href="/asignacion/costos_recursos_pendiente_con_arribo.php?sucursalId=<?php echo (int)$r['IdSucursal']; ?>"
                 target="_blank"
                 class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 text-xs font-medium hover:underline">
                <i class="fas fa-file-pdf"></i> Ver
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr class="border-t-2 border-gray-200 bg-violet-50">
            <td class="py-3 font-bold text-slate-800 pl-1">Total DyV</td>
            <td class="py-3 text-right font-mono font-bold text-slate-900"><?php echo fmt($total_con_arribo); ?></td>
            <td class="py-3 px-3">
              <div class="h-2 bg-violet-200 rounded-full"></div>
            </td>
            <td class="py-3 text-right font-bold text-slate-600 pr-4">100%</td>
            <td></td>
            <td class="py-3 text-center no-print">
              <a href="/asignacion/costos_recursos_pendiente_con_arribo.php"
                 target="_blank"
                 class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 text-xs font-medium hover:underline">
                <i class="fas fa-file-pdf"></i> Todo DyV
              </a>
            </td>
          </tr>
        </tfoot>
      </table>
    </div>

    <!-- ── Tab: Todas ──────────────────────────────────────────────────────── -->
    <div id="tab-todas" class="tab-pane hidden p-5">
      <div class="print-show text-xs font-bold text-slate-600 uppercase tracking-wider pb-2 mb-3 border-b border-gray-200">
        <i class="fas fa-layer-group text-slate-500 mr-1"></i> Todas las Categorías (Total Consolidado)
      </div>
      <table class="w-full text-sm">
        <thead>
          <tr class="text-xs text-slate-400 uppercase tracking-wide border-b border-gray-100">
            <th class="text-left py-2.5 font-medium pl-1">Sucursal</th>
            <th class="text-right py-2.5 font-medium">Saldo Total</th>
            <th class="py-2.5 font-medium text-center" style="width:140px;">Distribución</th>
            <th class="text-right py-2.5 font-medium pr-4">% Total</th>
            <th class="text-center py-2.5 font-medium">Riesgo</th>
            <th class="text-center py-2.5 font-medium no-print">Detalle</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          <?php foreach ($todas as $r):
            $p = pct((float)$r['Saldo'], $total_general); ?>
          <tr class="hover:bg-gray-50 transition-colors">
            <td class="py-3 font-medium text-slate-800 pl-1"><?php echo htmlspecialchars($r['Sucursal']); ?></td>
            <td class="py-3 text-right font-mono font-semibold text-slate-900"><?php echo fmt((float)$r['Saldo']); ?></td>
            <td class="py-3 px-3">
              <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-slate-500 rounded-full transition-all" style="width:<?php echo $p; ?>%"></div>
              </div>
            </td>
            <td class="py-3 text-right text-slate-500 pr-4"><?php echo $p; ?>%</td>
            <td class="py-3 text-center"><?php echo riskBadge((float)$r['Saldo'], $total_general); ?></td>
            <td class="py-3 text-center no-print">
              <a href="/asignacion/costos_recursos_completa.php?sucursalId=<?php echo (int)$r['IdSucursal']; ?>"
                 target="_blank"
                 class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 text-xs font-medium hover:underline">
                <i class="fas fa-file-pdf"></i> Ver
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr class="border-t-2 border-gray-200 bg-slate-100">
            <td class="py-3 font-bold text-slate-800 pl-1">Total DyV</td>
            <td class="py-3 text-right font-mono font-bold text-slate-900"><?php echo fmt($total_general); ?></td>
            <td class="py-3 px-3">
              <div class="h-2 bg-slate-300 rounded-full"></div>
            </td>
            <td class="py-3 text-right font-bold text-slate-600 pr-4">100%</td>
            <td></td>
            <td class="py-3 text-center no-print">
              <a href="/asignacion/costos_recursos_completa.php"
                 target="_blank"
                 class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 text-xs font-medium hover:underline">
                <i class="fas fa-file-pdf"></i> Todo DyV
              </a>
            </td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

  <!-- Footer -->
  <div class="text-center text-xs text-slate-400 py-2 no-print">
    Dashboard generado el <?php echo $fecha_actual; ?> a las <?php echo $hora_actual; ?> · Derka y Vargas S.A.
  </div>

</main>

<script>
// ── Tabs ──────────────────────────────────────────────────────────────────────
document.querySelectorAll('.tab-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.add('hidden'));
    btn.classList.add('active');
    document.getElementById('tab-' + btn.dataset.tab).classList.remove('hidden');
  });
});

// Reubica la card de tabs debajo de las cards de totales (tercera fila visual).
const tabsCard = document.getElementById('card-tabs-detalle');
const chartWrap = document.querySelector('.chart-wrap');
if (tabsCard && chartWrap && chartWrap.parentNode) {
  chartWrap.parentNode.insertBefore(tabsCard, chartWrap);
}

// ── Chart.js ──────────────────────────────────────────────────────────────────
const labels = <?php echo json_encode($chart_labels, JSON_UNESCAPED_UNICODE); ?>;
const dataP  = <?php echo json_encode($chart_p);  ?>;
const dataEV = <?php echo json_encode($chart_ev); ?>;
const dataCA = <?php echo json_encode($chart_ca); ?>;
const totPend  = <?php echo (float)$total_pendiente;  ?>;
const totViaje = <?php echo (float)$total_en_viaje;   ?>;
const totArr   = <?php echo (float)$total_con_arribo; ?>;
const totGen   = <?php echo (float)$total_general;    ?>;

const fmtARS = n => '$ ' + Math.round(n).toLocaleString('es-AR');
const fmtMill = n => '$ ' + (n / 1e6).toLocaleString('es-AR', { minimumFractionDigits: 1, maximumFractionDigits: 1 }) + 'M';

Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
Chart.defaults.color = '#64748b';

// ── Barras agrupadas ──────────────────────────────────────────────────────────
new Chart(document.getElementById('barChart'), {
  type: 'bar',
  data: {
    labels,
    datasets: [
      {
        label: 'Pendiente TASA',
        data: dataP,
        backgroundColor: 'rgba(245,158,11,0.85)',
        borderRadius: { topLeft: 4, topRight: 4 },
        borderSkipped: false,
      },
      {
        label: 'En Viaje',
        data: dataEV,
        backgroundColor: 'rgba(59,130,246,0.85)',
        borderRadius: { topLeft: 4, topRight: 4 },
        borderSkipped: false,
      },
      {
        label: 'Con Arribo',
        data: dataCA,
        backgroundColor: 'rgba(139,92,246,0.85)',
        borderRadius: { topLeft: 4, topRight: 4 },
        borderSkipped: false,
      },
    ]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    interaction: { mode: 'index', intersect: false },
    plugins: {
      legend: {
        position: 'top',
        align: 'end',
        labels: { boxWidth: 10, boxHeight: 10, padding: 14, font: { size: 11 } }
      },
      tooltip: {
        callbacks: {
          label: ctx => `  ${ctx.dataset.label}: ${fmtARS(ctx.raw)}`,
          afterBody: items => {
            const total = items.reduce((s, i) => s + i.raw, 0);
            return [`  ─────────────────────────`, `  Sucursal total: ${fmtARS(total)}`];
          }
        }
      }
    },
    scales: {
      x: { grid: { display: false }, ticks: { font: { size: 11 } } },
      y: {
        border: { dash: [3, 3], color: 'transparent' },
        grid: { color: 'rgba(0,0,0,0.04)' },
        ticks: { font: { size: 10 }, callback: v => fmtMill(v) }
      }
    }
  }
});

// ── Dona ──────────────────────────────────────────────────────────────────────
new Chart(document.getElementById('donutChart'), {
  type: 'doughnut',
  data: {
    labels: ['Pendiente TASA', 'En Viaje', 'Con Arribo'],
    datasets: [{
      data: [totPend, totViaje, totArr],
      backgroundColor: [
        'rgba(245,158,11,0.9)',
        'rgba(59,130,246,0.9)',
        'rgba(139,92,246,0.9)',
      ],
      borderWidth: 0,
      hoverOffset: 8,
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    cutout: '73%',
    plugins: {
      legend: { display: false },
      tooltip: {
        callbacks: {
          label: ctx => `  ${ctx.label}: ${fmtARS(ctx.raw)}`,
          afterLabel: ctx => `  Participación: ${((ctx.raw / totGen) * 100).toFixed(1)}%`,
        }
      }
    }
  }
});
</script>
</body>
</html>
