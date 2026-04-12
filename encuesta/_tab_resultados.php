<?php
// ── Tab: Resultados ────────────────────────────────────────
$SQL = "SELECT
			r.id_respuesta,
			r.fecha_completada,
			r.resultado_promedio,
			a.cliente,
			a.fec_entrega,
			a.id_unidad,
			u.nombre   AS asesor,
			g.grupo    AS grupo,
			s.sucursal AS sucursal,
			e.nombre   AS encuesta
		FROM enc_respuestas r
		JOIN enc_tokens     t  ON r.id_token       = t.id_token
		JOIN asignaciones   a  ON r.id_asignacion  = a.id_unidad
		JOIN usuarios       u  ON a.id_asesor       = u.idusuario
		LEFT JOIN grupos    g  ON a.id_grupo        = g.idgrupo
		LEFT JOIN sucursales s ON a.id_sucursal     = s.idsucursal
		JOIN enc_encuestas  e  ON r.id_encuesta     = e.id_encuesta
		ORDER BY r.fecha_completada DESC
		LIMIT 200";
$resultados = mysqli_query($con, $SQL);

// Promedio global
$SQL_prom = "SELECT AVG(resultado_promedio) AS prom_global, COUNT(*) AS total FROM enc_respuestas";
$res_prom = mysqli_query($con, $SQL_prom);
$prom_data = mysqli_fetch_array($res_prom);
$prom_global = $prom_data['prom_global'] !== null ? number_format($prom_data['prom_global'], 1) : '-';
$total_resp  = $prom_data['total'];

// Promedio por área (global, todas las respuestas)
$SQL_prom_areas = "SELECT ar.nombre AS area, ar.color,
						  ROUND(AVG(d.respuesta_valor), 1) AS promedio,
						  COUNT(DISTINCT r.id_respuesta) AS total_resp
				   FROM enc_areas ar
				   JOIN enc_preguntas p  ON p.id_area = ar.id_area AND p.baja = 0
				   JOIN enc_respuestas_detalle d ON d.id_pregunta = p.id_pregunta AND d.mostrada = 1
				   JOIN enc_respuestas r ON d.id_respuesta = r.id_respuesta
				   WHERE p.pondera = 1
				   GROUP BY ar.id_area, ar.nombre, ar.color
				   ORDER BY ar.nro_orden ASC";
$res_prom_areas = mysqli_query($con, $SQL_prom_areas);
$areas_stats = [];
while ($a = mysqli_fetch_array($res_prom_areas)) $areas_stats[] = $a;
?>

<div class="enc-sec-header">
	<span class="enc-sec-titulo"><span class="icon-line-chart"></span> Resultados de Encuestas</span>
</div>

<!-- RESUMEN GLOBAL -->
<div style="display:flex;gap:16px;margin-bottom:14px;flex-wrap:wrap;">
	<div class="enc-form-panel" style="text-align:center;min-width:150px;padding:16px;">
		<div class="enc-promedio-grande"><?php echo $prom_global; ?></div>
		<div class="enc-promedio-label">Promedio general<br>(escala 1-10)</div>
	</div>
	<div class="enc-form-panel" style="text-align:center;min-width:150px;padding:16px;">
		<div class="enc-promedio-grande" style="color:#1e8449;"><?php echo $total_resp; ?></div>
		<div class="enc-promedio-label">Encuestas<br>completadas</div>
	</div>
</div>

<!-- PROMEDIOS POR ÁREA -->
<?php if (!empty($areas_stats)): ?>
<div style="margin-bottom:14px;">
	<div style="font-size:11px;color:#888;margin-bottom:6px;font-weight:bold;">PROMEDIO POR ÁREA</div>
	<div style="display:flex;gap:10px;flex-wrap:wrap;">
	<?php foreach ($areas_stats as $as):
		$av = (float)$as['promedio'];
		if     ($av >= 8) $tc = '#1e8449';
		elseif ($av >= 6) $tc = '#d68910';
		else              $tc = '#c0392b';
	?>
		<div class="enc-form-panel" style="padding:10px 14px;min-width:110px;text-align:center;border-top:3px solid <?php echo htmlspecialchars($as['color']); ?>;">
			<div style="font-size:10px;color:<?php echo htmlspecialchars($as['color']); ?>;font-weight:bold;margin-bottom:4px;">
				<?php echo htmlspecialchars($as['area']); ?>
			</div>
			<div style="font-size:22px;font-weight:bold;color:<?php echo $tc; ?>;"><?php echo $as['promedio']; ?></div>
			<div style="font-size:9px;color:#aaa;">/ 10 &nbsp;·&nbsp; <?php echo $as['total_resp']; ?> resp.</div>
		</div>
	<?php endforeach; ?>
	</div>
</div>
<?php endif; ?>

<!-- TABLA RESULTADOS -->
<table class="enc-tabla">
	<thead>
		<tr>
			<td>Fecha respuesta</td>
			<td>Cliente</td>
			<td>Grupo</td>
			<td>Asesor</td>
			<td>Sucursal</td>
			<td>Encuesta</td>
			<td width="8%">Resultado</td>
			<td width="12%"></td>
		</tr>
	</thead>
	<tbody>
	<?php
	$filas = 0;
	while ($r = mysqli_fetch_array($resultados)):
		$filas++;
		$prom = $r['resultado_promedio'] !== null ? number_format($r['resultado_promedio'], 1) : '-';
		$prom_val = (float)$r['resultado_promedio'];
		if     ($prom_val >= 8)  $color_prom = '#1e8449';
		elseif ($prom_val >= 6)  $color_prom = '#d68910';
		else                     $color_prom = '#c0392b';
	?>
		<tr>
			<td><?php echo cambiarFormatoFecha(substr($r['fecha_completada'], 0, 10)); ?></td>
			<td><?php echo htmlspecialchars($r['cliente']); ?></td>
			<td><?php echo htmlspecialchars($r['grupo']); ?></td>
			<td><?php echo htmlspecialchars($r['asesor']); ?></td>
			<td><?php echo htmlspecialchars($r['sucursal']); ?></td>
			<td><?php echo htmlspecialchars($r['encuesta']); ?></td>
			<td style="text-align:center;font-weight:bold;color:<?php echo $color_prom; ?>;">
				<?php echo $prom; ?>
			</td>
			<td class="celda-acciones">
				<a class="btn-enc btn-enc-azul btn-enc-sm"
				   href="resultado_detalle.php?id=<?php echo (int)$r['id_respuesta']; ?>">
					<span class="icon-search"></span> Detalle
				</a>
				<a class="btn-enc btn-enc-gris btn-enc-sm"
				   href="resultado_pdf.php?id=<?php echo (int)$r['id_respuesta']; ?>"
				   target="_blank">
					<span class="icon-file-pdf-o"></span>
				</a>
			</td>
		</tr>
	<?php endwhile; ?>
	<?php if ($filas === 0): ?>
		<tr><td colspan="8" style="text-align:center;padding:20px;color:#888;">Aún no hay encuestas completadas.</td></tr>
	<?php endif; ?>
	</tbody>
</table>
