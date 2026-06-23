<?php
/* Resultados · Detalle de una respuesta de encuesta de usados. */
require __DIR__ . '/config/config_app.php';     // auth + $con
require __DIR__ . '/funciones/consulta.php';     // eu_utf8, eu_nivel

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$resp = $id > 0 ? mysqli_fetch_assoc(mysqli_query($con,
    "SELECT r.*, t.fecha_respuesta, e.nombre AS encuesta_nombre
     FROM encu_respuestas r
     JOIN encu_tokens t ON t.id_token = r.id_token
     JOIN encu_encuestas e ON e.id_encuesta = r.id_encuesta
     WHERE r.id_respuesta = $id LIMIT 1")) : null;

if (!$resp) {
    $title = 'Resultado no encontrado';
    ob_start(); $extraHead = '';
    echo '<!doctype html><html lang="es">'; include __DIR__ . '/../comun/head.php';
    echo '<body class="bg-gray-100 min-h-screen flex items-center justify-center text-slate-500">'
       . '<div class="text-center"><i class="fas fa-circle-question text-3xl mb-2"></i><p>No se encontró el resultado solicitado.</p>'
       . '<a href="dashboard.php" class="text-blue-600 hover:underline text-sm">Volver al dashboard</a></div></body></html>';
    exit();
}

$id_unidad = (int)$resp['id_asignacion'];
$unidad = mysqli_fetch_assoc(mysqli_query($con,
    "SELECT cliente, vehiculo, `año` AS anio, km, dominio, fec_entrega, asesor_venta, id_sucursal
     FROM view_asignaciones_usados_entregadas WHERE id_unidad = $id_unidad LIMIT 1"));

$prom  = $resp['resultado_promedio'] !== null ? (float)$resp['resultado_promedio'] : null;
$nivel = $prom !== null ? eu_nivel($prom) : null;

// Preguntas + respuesta (detalle) + opciones
$preguntas = [];
$qp = mysqli_query($con,
    "SELECT p.id_pregunta, p.nro_orden, p.texto_pregunta, p.tipo_pregunta, p.pondera,
            a.nombre AS area_nombre, a.color AS area_color,
            d.respuesta_valor, d.respuesta_texto, d.mostrada
     FROM encu_preguntas p
     LEFT JOIN encu_areas a ON a.id_area = p.id_area
     LEFT JOIN encu_respuestas_detalle d ON d.id_pregunta = p.id_pregunta AND d.id_respuesta = {$id}
     WHERE p.id_encuesta = {$resp['id_encuesta']} AND p.baja = 0
     ORDER BY p.nro_orden ASC");
while ($p = mysqli_fetch_assoc($qp)) {
    $preguntas[] = $p;
}

// Opciones respondidas, indexadas por pregunta
$opcsPorPreg = [];
$qo = mysqli_query($con,
    "SELECT d.id_pregunta, o.texto_opcion, ro.valor_elegido
     FROM encu_respuestas_detalle d
     JOIN encu_respuestas_opciones ro ON ro.id_detalle = d.id_detalle
     JOIN encu_opciones o ON o.id_opcion = ro.id_opcion
     WHERE d.id_respuesta = $id ORDER BY o.nro_orden ASC, o.id_opcion ASC");
while ($o = mysqli_fetch_assoc($qo)) {
    $opcsPorPreg[(int)$o['id_pregunta']][] = $o;
}

// Promedios por área (sólo ponderadas mostradas con valor)
$areaAgg = [];
foreach ($preguntas as $p) {
    if ((int)$p['pondera'] === 1 && (int)$p['mostrada'] === 1 && $p['respuesta_valor'] !== null && $p['area_nombre']) {
        $k = $p['area_nombre'];
        if (!isset($areaAgg[$k])) $areaAgg[$k] = ['suma' => 0, 'n' => 0, 'color' => $p['area_color']];
        $areaAgg[$k]['suma'] += (float)$p['respuesta_valor'];
        $areaAgg[$k]['n']++;
    }
}

function fmtFecha($f) { if (!$f || $f === '0000-00-00') return '—'; $p = explode('-', substr($f,0,10)); return count($p)===3 ? "$p[2]/$p[1]/$p[0]" : $f; }
$tipoLabel = [1=>'Escala 1-10',2=>'Sí / No',3=>'Selección múltiple',4=>'Lista Sí/No',5=>'Texto libre'];
$title = 'Resultado · ' . ($unidad ? eu_utf8($unidad['cliente']) : 'Encuesta');
$extraHead = '';
?>
<!doctype html>
<html lang="es">
<?php include __DIR__ . '/../comun/head.php'; ?>
<body class="bg-gray-100 min-h-screen text-slate-800">
  <header class="bg-slate-900 text-white shadow-lg sticky top-0 z-30">
    <div class="max-w-[1000px] mx-auto px-6 py-3 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <a href="dashboard.php" class="w-9 h-9 bg-slate-700 hover:bg-slate-600 rounded-lg flex items-center justify-center" title="Volver"><i class="fas fa-arrow-left text-sm"></i></a>
        <div><h1 class="text-sm font-bold leading-tight">Resultado de encuesta</h1>
        <p class="text-slate-400 text-xs">Usados · Derka y Vargas S.A.</p></div>
      </div>
      <a href="pdf.php?id=<?= $id ?>" target="_blank" class="flex items-center gap-1.5 bg-red-600 hover:bg-red-700 px-3 py-1.5 rounded-md text-xs font-medium">
        <i class="fas fa-file-pdf"></i> PDF
      </a>
    </div>
  </header>

  <main class="max-w-[1000px] mx-auto px-6 py-6 space-y-5">
    <!-- Cabecera datos + promedio -->
    <div class="grid md:grid-cols-3 gap-5">
      <div class="md:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <h2 class="text-lg font-bold text-slate-900"><?= htmlspecialchars(eu_utf8($unidad['cliente'] ?? '—')) ?></h2>
        <p class="text-slate-600"><?= htmlspecialchars(eu_utf8($unidad['vehiculo'] ?? '')) ?>
          <span class="text-slate-400 text-sm">
            <?php if ($unidad && $unidad['anio']) echo ' · '.(int)$unidad['anio']; ?>
            <?php if ($unidad && $unidad['dominio']) echo ' · '.htmlspecialchars($unidad['dominio']); ?>
          </span>
        </p>
        <div class="grid grid-cols-2 gap-3 mt-4 text-sm">
          <div><span class="text-slate-400">Asesor</span><br><span class="font-medium"><?= htmlspecialchars(eu_utf8($unidad['asesor_venta'] ?? '—')) ?></span></div>
          <div><span class="text-slate-400">Entrega</span><br><span class="font-medium"><?= fmtFecha($unidad['fec_entrega'] ?? '') ?></span></div>
          <div><span class="text-slate-400">Encuesta</span><br><span class="font-medium"><?= htmlspecialchars(eu_utf8($resp['encuesta_nombre'])) ?></span></div>
          <div><span class="text-slate-400">Respondida</span><br><span class="font-medium"><?= fmtFecha($resp['fecha_respuesta']) ?></span></div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex flex-col items-center justify-center">
        <p class="text-xs text-slate-400 uppercase tracking-wider mb-1">Promedio</p>
        <p class="text-5xl font-extrabold num" style="color:<?= $nivel ? $nivel['color'] : '#94a3b8' ?>"><?= $prom !== null ? number_format($prom,1) : '—' ?></p>
        <?php if ($nivel): ?>
        <span class="mt-2 px-3 py-1 rounded-full text-xs font-semibold text-white" style="background:<?= $nivel['color'] ?>"><?= htmlspecialchars($nivel['nombre']) ?></span>
        <?php endif; ?>
      </div>
    </div>

    <!-- Resumen por área -->
    <?php if ($areaAgg): ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
      <h3 class="text-sm font-bold text-slate-700 mb-3"><i class="fas fa-tags mr-1 text-slate-400"></i>Promedio por área</h3>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <?php foreach ($areaAgg as $nombre => $a): $pa = $a['n'] ? $a['suma']/$a['n'] : 0; ?>
        <div class="rounded-lg border border-gray-100 p-3">
          <div class="flex items-center gap-1.5 mb-1">
            <span class="w-2.5 h-2.5 rounded-full" style="background:<?= $a['color'] ?: '#607d8b' ?>"></span>
            <span class="text-xs text-slate-500 truncate"><?= htmlspecialchars(eu_utf8($nombre)) ?></span>
          </div>
          <p class="text-xl font-bold num text-slate-900"><?= number_format($pa,1) ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- Respuestas -->
    <div class="space-y-3">
      <?php foreach ($preguntas as $i => $p):
        $tipo = (int)$p['tipo_pregunta']; $mostrada = (int)$p['mostrada'];
        $val  = $p['respuesta_valor'] !== null ? (float)$p['respuesta_valor'] : null; ?>
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="flex items-center gap-2 flex-wrap mb-1.5">
          <span class="text-[11px] font-semibold px-2 py-0.5 rounded bg-slate-100 text-slate-600"><?= $tipoLabel[$tipo] ?? '' ?></span>
          <?php if ($p['area_nombre']): ?><span class="text-[11px] font-semibold px-2 py-0.5 rounded text-white" style="background:<?= $p['area_color'] ?: '#607d8b' ?>"><?= htmlspecialchars(eu_utf8($p['area_nombre'])) ?></span><?php endif; ?>
        </div>
        <p class="font-medium text-slate-900 mb-2"><?= htmlspecialchars(eu_utf8($p['texto_pregunta'])) ?></p>

        <?php if ($mostrada === 0): ?>
          <p class="text-sm text-slate-400 italic"><i class="fas fa-minus mr-1"></i>No correspondía (omitida por condición)</p>

        <?php elseif ($tipo === 1): ?>
          <div class="flex items-center gap-3">
            <div class="flex-1 bg-slate-100 rounded-full h-2.5 overflow-hidden max-w-xs">
              <div class="h-full rounded-full bg-blue-600" style="width:<?= $val !== null ? ($val*10) : 0 ?>%"></div>
            </div>
            <span class="font-bold text-slate-900 num"><?= $val !== null ? number_format($val,0) : '—' ?>/10</span>
          </div>

        <?php elseif ($tipo === 2): ?>
          <?php $si = ($val !== null && $val >= 10); ?>
          <span class="inline-block px-3 py-1 rounded-lg text-sm font-semibold <?= $si ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' ?>">
            <i class="fas <?= $si ? 'fa-check' : 'fa-xmark' ?> mr-1"></i><?= $si ? 'Sí' : 'No' ?>
          </span>

        <?php elseif ($tipo === 3): ?>
          <div class="flex flex-wrap gap-1.5">
            <?php foreach (($opcsPorPreg[(int)$p['id_pregunta']] ?? []) as $o): ?>
              <span class="text-xs px-2 py-1 rounded bg-blue-50 text-blue-700"><i class="fas fa-check mr-1"></i><?= htmlspecialchars(eu_utf8($o['texto_opcion'])) ?></span>
            <?php endforeach; ?>
            <?php if (empty($opcsPorPreg[(int)$p['id_pregunta']])): ?><span class="text-sm text-slate-400">Sin selección</span><?php endif; ?>
          </div>

        <?php elseif ($tipo === 4): ?>
          <div class="space-y-1">
            <?php foreach (($opcsPorPreg[(int)$p['id_pregunta']] ?? []) as $o): $ok=(int)$o['valor_elegido']===1; ?>
              <div class="flex items-center justify-between text-sm border-b border-gray-50 py-1">
                <span class="text-slate-600"><?= htmlspecialchars(eu_utf8($o['texto_opcion'])) ?></span>
                <span class="font-semibold <?= $ok ? 'text-emerald-600' : 'text-red-500' ?>"><?= $ok ? 'Sí' : 'No' ?></span>
              </div>
            <?php endforeach; ?>
          </div>

        <?php elseif ($tipo === 5): ?>
          <?php $txt = trim((string)$p['respuesta_texto']); ?>
          <p class="text-sm text-slate-700 bg-slate-50 rounded-lg p-3 <?= $txt==='' ? 'text-slate-400 italic' : '' ?>"><?= $txt!=='' ? nl2br(htmlspecialchars(eu_utf8($txt))) : 'Sin comentarios' ?></p>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
      <?php if (empty($preguntas)): ?><p class="text-center text-slate-400 py-6">Esta encuesta no tiene preguntas.</p><?php endif; ?>
    </div>
  </main>
</body>
</html>
