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
	// Cargar opciones si aplica
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

$tipos = [1=>'Escala 1-10', 2=>'Sí/No', 3=>'Selección múltiple', 4=>'Lista Sí/No', 5=>'Texto libre'];

// Resumen por área para esta respuesta
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
$prom  = $cab['resultado_promedio'] !== null ? number_format($cab['resultado_promedio'], 1) : '-';
$prom_val = (float)$cab['resultado_promedio'];
if     ($prom_val >= 8)  $color_prom = '#1e8449';
elseif ($prom_val >= 6)  $color_prom = '#d68910';
else                     $color_prom = '#c0392b';
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
	<link href="../asignacion/css/iconos.css" rel="stylesheet">
</head>
<body>

<div class="enc-subpag-cabecera">
	<a class="btn-enc btn-enc-gris btn-enc-sm" href="index.php?sec=resultados">← Volver</a>
	<span class="enc-titulo">Resultado: <?php echo htmlspecialchars($cab['cliente']); ?></span>
	<a class="btn-enc btn-enc-gris btn-enc-sm" style="margin-left:auto;"
	   href="resultado_pdf.php?id=<?php echo $id_respuesta; ?>" target="_blank">
		<span class="icon-file-pdf-o"></span> PDF
	</a>
</div>

<div class="enc-subpag-contenido">

	<!-- Cabecera del resultado -->
	<div style="display:flex;gap:16px;flex-wrap:wrap;margin-bottom:16px;">
		<div class="enc-form-panel" style="min-width:260px;flex:2;">
			<h3 style="margin:0 0 10px;font-size:13px;color:#1a5276;">Datos del Cliente</h3>
			<div class="enc-dato-row"><span class="enc-dato-label">Cliente</span><span class="enc-dato-valor"><?php echo htmlspecialchars($cab['cliente']); ?></span></div>
			<div class="enc-dato-row"><span class="enc-dato-label">Vehículo</span><span class="enc-dato-valor"><?php echo htmlspecialchars($cab['grupo']); ?><?php if ($cab['modelo']) echo ' — '.htmlspecialchars($cab['modelo']); ?></span></div>
			<div class="enc-dato-row"><span class="enc-dato-label">Fecha entrega</span><span class="enc-dato-valor"><?php echo fechaLarga($cab['fec_entrega']); ?></span></div>
			<div class="enc-dato-row"><span class="enc-dato-label">Asesor</span><span class="enc-dato-valor"><?php echo htmlspecialchars($cab['asesor']); ?></span></div>
			<div class="enc-dato-row"><span class="enc-dato-label">Sucursal</span><span class="enc-dato-valor"><?php echo htmlspecialchars($cab['sucursal']); ?></span></div>
			<div class="enc-dato-row"><span class="enc-dato-label">Encuesta</span><span class="enc-dato-valor"><?php echo htmlspecialchars($cab['enc_nombre']); ?></span></div>
			<div class="enc-dato-row"><span class="enc-dato-label">Fecha respuesta</span><span class="enc-dato-valor"><?php echo cambiarFormatoFecha(substr($cab['fecha_completada'], 0, 10)); ?></span></div>
		</div>
		<div class="enc-form-panel" style="min-width:120px;flex:1;text-align:center;display:flex;flex-direction:column;justify-content:center;">
			<div class="enc-promedio-grande" style="color:<?php echo $color_prom; ?>;"><?php echo $prom; ?></div>
			<div class="enc-promedio-label">Resultado<br>(escala 1-10)</div>
		</div>
	</div>

	<!-- Resumen por área -->
	<?php if (!empty($areas_prom)): ?>
	<div style="margin-bottom:16px;">
		<div style="font-weight:bold;color:#1a5276;margin-bottom:8px;font-size:13px;">Resultado por Área</div>
		<div style="display:flex;gap:10px;flex-wrap:wrap;">
		<?php foreach ($areas_prom as $ap):
			$av = (float)$ap['promedio'];
			if     ($av >= 8) $tc = '#1e8449';
			elseif ($av >= 6) $tc = '#d68910';
			else              $tc = '#c0392b';
		?>
			<div style="background:#fff;border:1px solid #e0e0e0;border-top:3px solid <?php echo htmlspecialchars($ap['color']); ?>;border-radius:4px;padding:10px 14px;min-width:110px;text-align:center;">
				<div style="font-size:10px;color:<?php echo htmlspecialchars($ap['color']); ?>;font-weight:bold;margin-bottom:4px;">
					<?php echo htmlspecialchars($ap['area']); ?>
				</div>
				<div style="font-size:22px;font-weight:bold;color:<?php echo $tc; ?>;"><?php echo $ap['promedio']; ?></div>
				<div style="font-size:9px;color:#aaa;">/ 10</div>
			</div>
		<?php endforeach; ?>
		</div>
	</div>
	<?php endif; ?>

	<!-- Detalle pregunta por pregunta -->
	<div style="font-weight:bold;color:#1a5276;margin-bottom:8px;font-size:13px;">Respuestas</div>

	<?php foreach ($detalles as $d):
		$tipo = (int)$d['tipo_pregunta'];
	?>
	<div class="enc-detalle-preg">
		<div class="enc-detalle-preg-titulo">
			<?php echo $d['nro_orden']; ?>. <?php echo htmlspecialchars($d['texto_pregunta']); ?>
			<span class="enc-badge-tipo" style="margin-left:6px;"><?php echo $tipos[$tipo] ?? '?'; ?></span>
			<?php if (!$d['mostrada']): ?>
				<span class="enc-badge-tipo enc-badge-no-pondera">No mostrada</span>
			<?php elseif ($d['pondera']): ?>
				<span class="enc-badge-tipo enc-badge-pondera">Pondera</span>
			<?php endif; ?>
			<?php if ($d['area_nombre']): ?>
				<span class="enc-badge-tipo enc-badge-area"
				      style="background:<?php echo htmlspecialchars($d['area_color']); ?>22;color:<?php echo htmlspecialchars($d['area_color']); ?>;border-color:<?php echo htmlspecialchars($d['area_color']); ?>44;">
					<?php echo htmlspecialchars($d['area_nombre']); ?>
				</span>
			<?php endif; ?>
		</div>

		<?php if (!$d['mostrada']): ?>
			<div class="enc-detalle-respuesta" style="color:#aaa;font-style:italic;">Omitida por condición.</div>

		<?php elseif ($tipo == 1): ?>
			<div class="enc-detalle-respuesta">
				<span style="font-size:20px;font-weight:bold;color:<?php
					$v = (float)$d['respuesta_valor'];
					echo $v >= 8 ? '#1e8449' : ($v >= 6 ? '#d68910' : '#c0392b');
				?>;"><?php echo number_format($d['respuesta_valor'], 0); ?></span>
				<span style="color:#888;font-size:11px;"> / 10</span>
				<div class="enc-score-bar" style="max-width:250px;margin-top:5px;">
					<div class="enc-score-fill" style="width:<?php echo $d['respuesta_valor'] * 10; ?>%;"></div>
				</div>
			</div>

		<?php elseif ($tipo == 2): ?>
			<div class="enc-detalle-respuesta">
				<?php if ($d['respuesta_valor'] == 10): ?>
					<span style="color:#1e8449;font-weight:bold;font-size:14px;">✓ Sí</span>
				<?php else: ?>
					<span style="color:#c0392b;font-weight:bold;font-size:14px;">✗ No</span>
				<?php endif; ?>
			</div>

		<?php elseif ($tipo == 3): ?>
			<div class="enc-detalle-respuesta">
				<?php if (empty($d['opciones_resp'])): ?>
					<span style="color:#888;font-style:italic;">Ninguna opción seleccionada.</span>
				<?php else: ?>
					<ul style="margin:4px 0 0 18px;padding:0;">
					<?php foreach ($d['opciones_resp'] as $op): ?>
						<li style="font-size:12px;"><?php echo htmlspecialchars($op['texto_opcion']); ?></li>
					<?php endforeach; ?>
					</ul>
					<span style="color:#888;font-size:10px;">
						<?php echo count($d['opciones_resp']); ?> seleccionada(s)
						→ valor: <?php echo number_format($d['respuesta_valor'], 1); ?>/10
					</span>
				<?php endif; ?>
			</div>

		<?php elseif ($tipo == 4): ?>
			<div class="enc-detalle-respuesta">
				<?php if (empty($d['opciones_resp'])): ?>
					<span style="color:#888;font-style:italic;">Sin respuesta.</span>
				<?php else: ?>
					<table style="border-collapse:collapse;width:100%;max-width:400px;">
					<?php foreach ($d['opciones_resp'] as $op): ?>
						<tr>
							<td style="padding:2px 6px 2px 0;font-size:12px;"><?php echo htmlspecialchars($op['texto_opcion']); ?></td>
							<td style="padding:2px 0;font-size:12px;font-weight:bold;color:<?php echo $op['valor_elegido'] ? '#1e8449' : '#c0392b'; ?>;">
								<?php echo $op['valor_elegido'] ? 'Sí' : 'No'; ?>
							</td>
						</tr>
					<?php endforeach; ?>
					</table>
				<?php endif; ?>
			</div>

		<?php else: ?>
			<!-- Tipo 5: texto libre -->
			<div class="enc-detalle-respuesta" style="color:#333;font-style:italic;background:#f8f9fa;padding:8px;border-radius:4px;">
				<?php echo $d['respuesta_texto'] ? nl2br(htmlspecialchars($d['respuesta_texto'])) : '<span style="color:#aaa;">Sin comentarios.</span>'; ?>
			</div>
		<?php endif; ?>
	</div>
	<?php endforeach; ?>

</div><!-- /enc-subpag-contenido -->
</body>
</html>
