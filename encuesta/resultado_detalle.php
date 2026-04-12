<?php
@session_start();
if ($_SESSION["autentificado"] != "SI") { header("Location: ../login"); exit(); }
include_once("config.php");
if (!in_array($_SESSION["idperfil"], ENCUESTA_PERFILES)) { header("Location: ../login"); exit(); }
include_once("funciones/func_mysql.php");
conectar();

$id_respuesta = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id_respuesta <= 0) { header("Location: index.php?sec=resultados"); exit(); }

// Cabecera de respuesta
$SQL_cab = "SELECT r.*,
					a.cliente, a.fec_entrega, a.chasis,
					u.nombre AS asesor,
					g.grupo AS grupo,
					m.modelo AS modelo,
					s.sucursal AS sucursal,
					e.nombre AS enc_nombre
			FROM enc_respuestas r
			JOIN enc_tokens     t  ON r.id_token      = t.id_token
			JOIN asignaciones   a  ON r.id_asignacion = a.id_unidad
			JOIN usuarios       u  ON a.id_asesor     = u.idusuario
			LEFT JOIN grupos    g  ON a.id_grupo      = g.idgrupo
			LEFT JOIN modelos   m  ON a.id_modelo     = m.idmodelo
			LEFT JOIN sucursales s ON a.id_sucursal   = s.idsucursal
			JOIN enc_encuestas  e  ON r.id_encuesta   = e.id_encuesta
			WHERE r.id_respuesta = $id_respuesta";
$res_cab = mysqli_query($con, $SQL_cab);
if (mysqli_num_rows($res_cab) == 0) { header("Location: index.php?sec=resultados"); exit(); }
$cab = mysqli_fetch_array($res_cab);

// Detalle por pregunta
$SQL_det = "SELECT d.*,
				   p.texto_pregunta, p.tipo_pregunta, p.pondera, p.nro_orden,
				   ar.nombre AS area_nombre, ar.color AS area_color
			FROM enc_respuestas_detalle d
			JOIN enc_preguntas p ON d.id_pregunta = p.id_pregunta
			LEFT JOIN enc_areas ar ON p.id_area = ar.id_area
			WHERE d.id_respuesta = $id_respuesta
			ORDER BY p.nro_orden ASC";
$res_det = mysqli_query($con, $SQL_det);
$detalles = [];
while ($d = mysqli_fetch_array($res_det)) {
	$d['opciones_resp'] = [];
	if (in_array($d['tipo_pregunta'], [3, 4])) {
		$SQL_op = "SELECT ro.valor_elegido, o.texto_opcion
				   FROM enc_respuestas_opciones ro
				   JOIN enc_opciones o ON ro.id_opcion = o.id_opcion
				   WHERE ro.id_detalle = {$d['id_detalle']}
				   ORDER BY o.nro_orden ASC";
		$res_op = mysqli_query($con, $SQL_op);
		while ($op = mysqli_fetch_array($res_op)) $d['opciones_resp'][] = $op;
	}
	$detalles[] = $d;
}

$tipos = [1=>'Escala 1-10', 2=>'Sí / No', 3=>'Selección múltiple', 4=>'Lista Sí/No', 5=>'Texto libre'];

// Resumen por área
$SQL_areas = "SELECT ar.nombre AS area, ar.color,
					 ROUND(AVG(d.respuesta_valor), 1) AS promedio
			  FROM enc_respuestas_detalle d
			  JOIN enc_preguntas p  ON d.id_pregunta = p.id_pregunta
			  JOIN enc_areas ar     ON p.id_area = ar.id_area
			  WHERE d.id_respuesta = $id_respuesta AND d.mostrada = 1 AND p.pondera = 1
			  GROUP BY ar.id_area, ar.nombre, ar.color
			  ORDER BY ar.nro_orden ASC";
$res_areas = mysqli_query($con, $SQL_areas);
$areas_prom = [];
while ($a = mysqli_fetch_array($res_areas)) $areas_prom[] = $a;

$prom     = $cab['resultado_promedio'] !== null ? number_format($cab['resultado_promedio'], 1) : '-';
$prom_val = (float)$cab['resultado_promedio'];
$nivel    = get_nivel($prom_val);
$color_prom = $nivel['color'];
$label_prom = $nivel['nombre'];

function score_color($v) {
	$n = get_nivel($v);
	return $n['color'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Detalle — <?php echo htmlspecialchars($cab['cliente']); ?></title>
	<link rel="shortcut icon" type="image/x-icon" href="../asignacion/imagenes/favicon.ico" />
	<script src="js/jquery-2.1.3.min.js"></script>
	<link rel="stylesheet" href="en_proceso/en_proceso.css">
	<link rel="stylesheet" href="css/encuesta_admin.css">
	<link rel="stylesheet" href="css/resultado_detalle.css">
	<link href="../asignacion/css/iconos.css" rel="stylesheet">
</head>
<body>

<div class="enc-subpag-cabecera">
	<a class="btn-enc btn-enc-gris btn-enc-sm" href="javascript:history.back()">← Volver</a>
	<span class="enc-titulo">Resultado: <?php echo htmlspecialchars($cab['cliente']); ?></span>
	<a class="btn-enc btn-enc-gris btn-enc-sm" style="margin-left:auto;"
	   href="resultado_pdf.php?id=<?php echo $id_respuesta; ?>" target="_blank">
		<span class="icon-file-pdf-o"></span> PDF
	</a>
</div>

<div class="rd-contenido">

<!-- ═══ CABECERA: DATOS + SCORE ══════════════════════════════════ -->
<div class="rd-top">

	<!-- Datos del cliente -->
	<div class="rd-card rd-datos">
		<div class="rd-section-title">Datos del Cliente</div>
		<table class="rd-datos-tabla">
			<tr><td class="rd-lbl">Cliente</td>      <td class="rd-val"><?php echo htmlspecialchars($cab['cliente']); ?></td></tr>
			<tr><td class="rd-lbl">Vehículo</td>     <td class="rd-val"><?php echo htmlspecialchars($cab['grupo']); ?><?php if ($cab['modelo']) echo ' &mdash; '.htmlspecialchars($cab['modelo']); ?></td></tr>
			<tr><td class="rd-lbl">Fecha entrega</td><td class="rd-val"><?php echo fechaLarga($cab['fec_entrega']); ?></td></tr>
			<tr><td class="rd-lbl">Asesor</td>       <td class="rd-val"><?php echo htmlspecialchars($cab['asesor']); ?></td></tr>
			<tr><td class="rd-lbl">Sucursal</td>     <td class="rd-val"><?php echo htmlspecialchars($cab['sucursal']); ?></td></tr>
			<tr><td class="rd-lbl">Encuesta</td>     <td class="rd-val"><?php echo htmlspecialchars($cab['enc_nombre']); ?></td></tr>
			<tr><td class="rd-lbl">Respondida</td>   <td class="rd-val"><?php echo cambiarFormatoFecha(substr($cab['fecha_completada'], 0, 10)); ?></td></tr>
		</table>
	</div>

	<!-- Score general -->
	<div class="rd-card rd-score-card">
		<div class="rd-score-num" style="color:<?php echo $color_prom; ?>"><?php echo $prom; ?></div>
		<div class="rd-score-divider">/ 10</div>
		<div class="rd-score-label" style="color:<?php echo $color_prom; ?>"><?php echo $label_prom; ?></div>
	</div>

</div><!-- /rd-top -->

<!-- ═══ ÁREAS ════════════════════════════════════════════════════ -->
<?php if (!empty($areas_prom)): ?>
<div class="rd-section-title" style="margin:0 0 10px;">Resultado por Área</div>
<div class="rd-areas">
<?php foreach ($areas_prom as $ap):
	$av = (float)$ap['promedio'];
	$tc = score_color($av);
?>
	<div class="rd-area-card" style="border-top-color:<?php echo htmlspecialchars($ap['color']); ?>">
		<div class="rd-area-nombre" style="color:<?php echo htmlspecialchars($ap['color']); ?>"><?php echo htmlspecialchars($ap['area']); ?></div>
		<div class="rd-area-score" style="color:<?php echo $tc; ?>"><?php echo $ap['promedio']; ?></div>
		<div class="rd-area-bar-bg">
			<div class="rd-area-bar-fill" style="width:<?php echo ($av/10*100); ?>%;background:<?php echo $tc; ?>"></div>
		</div>
		<div class="rd-area-sub">/ 10</div>
	</div>
<?php endforeach; ?>
</div>
<?php endif; ?>

<!-- ═══ PREGUNTAS ════════════════════════════════════════════════ -->
<div class="rd-section-title" style="margin:6px 0 10px;">Respuestas</div>

<?php foreach ($detalles as $d):
	$tipo       = (int)$d['tipo_pregunta'];
	$area_color = $d['area_color'] ?: '#cbd5e0';
	$area_nombre= $d['area_nombre'];
?>
<div class="rd-preg" style="border-left-color:<?php echo htmlspecialchars($area_color); ?>">

	<!-- Cabecera de pregunta -->
	<div class="rd-preg-head">
		<div class="rd-preg-texto">
			<span class="rd-preg-num"><?php echo $d['nro_orden']; ?>.</span>
			<?php echo htmlspecialchars($d['texto_pregunta']); ?>
		</div>
		<?php if ($d['mostrada'] && in_array($tipo, [1,2,3])): ?>
		<div class="rd-preg-result">
			<?php if ($tipo == 1 || $tipo == 3): ?>
				<?php $v = (float)$d['respuesta_valor']; ?>
				<span class="rd-result-num" style="color:<?php echo score_color($v); ?>"><?php echo number_format($v, 0); ?></span>
				<span class="rd-result-sub">/ 10</span>
			<?php elseif ($tipo == 2): ?>
				<?php if ($d['respuesta_valor'] == 10): ?>
					<span class="rd-badge-si">Sí</span>
				<?php else: ?>
					<span class="rd-badge-no">No</span>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	</div>

	<!-- Meta: tipo · pondera · área -->
	<div class="rd-preg-meta">
		<span class="rd-meta-tipo"><?php echo $tipos[$tipo] ?? '?'; ?></span>
		<?php if (!$d['mostrada']): ?>
			<span class="rd-meta-tag rd-meta-omitida">Omitida</span>
		<?php elseif ($d['pondera']): ?>
			<span class="rd-meta-tag rd-meta-pondera">Pondera</span>
		<?php endif; ?>
		<?php if ($area_nombre): ?>
			<span class="rd-meta-tag rd-meta-area" style="background:<?php echo htmlspecialchars($area_color); ?>18;color:<?php echo htmlspecialchars($area_color); ?>;border-color:<?php echo htmlspecialchars($area_color); ?>55;">
				<?php echo htmlspecialchars($area_nombre); ?>
			</span>
		<?php endif; ?>
	</div>

	<!-- Cuerpo de respuesta -->
	<?php if (!$d['mostrada']): ?>
		<!-- nada extra -->

	<?php elseif ($tipo == 1): ?>
		<?php $v = (float)$d['respuesta_valor']; ?>
		<div class="rd-bar-wrap">
			<div class="rd-bar-bg">
				<div class="rd-bar-fill" style="width:<?php echo $v*10; ?>%;background:<?php echo score_color($v); ?>"></div>
			</div>
		</div>

	<?php elseif ($tipo == 3): ?>
		<div class="rd-cuerpo">
			<?php if (empty($d['opciones_resp'])): ?>
				<span class="rd-vacio">Ninguna opción seleccionada.</span>
			<?php else: ?>
				<ul class="rd-opciones-lista">
				<?php foreach ($d['opciones_resp'] as $op): ?>
					<li><?php echo htmlspecialchars($op['texto_opcion']); ?></li>
				<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

	<?php elseif ($tipo == 4): ?>
		<div class="rd-cuerpo">
			<?php if (empty($d['opciones_resp'])): ?>
				<span class="rd-vacio">Sin respuesta.</span>
			<?php else: ?>
				<table class="rd-lista-sino">
				<?php foreach ($d['opciones_resp'] as $j => $op): ?>
					<tr class="<?php echo $j%2==0?'par':'impar'; ?>">
						<td class="rd-lista-texto"><?php echo htmlspecialchars($op['texto_opcion']); ?></td>
						<td class="rd-lista-badge">
							<?php if ($op['valor_elegido']): ?>
								<span class="rd-badge-si">Sí</span>
							<?php else: ?>
								<span class="rd-badge-no">No</span>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</table>
			<?php endif; ?>
		</div>

	<?php elseif ($tipo == 5): ?>
		<div class="rd-texto-libre">
			<?php echo $d['respuesta_texto'] ? nl2br(htmlspecialchars($d['respuesta_texto'])) : '<span class="rd-vacio">Sin comentarios.</span>'; ?>
		</div>

	<?php endif; ?>
</div>
<?php endforeach; ?>

</div><!-- /rd-contenido -->
</body>
</html>
