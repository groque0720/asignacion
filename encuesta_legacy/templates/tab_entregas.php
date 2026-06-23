<?php
// ── Tab: Lista de Entregas ─────────────────────────────────
$where_suc = $filtro_sucursal > 0 ? "AND a.id_sucursal = $filtro_sucursal" : "";

$SQL = "SELECT
			a.id_unidad,
			a.fec_entrega,
			a.cliente,
			a.chasis,
			a.nro_orden,
			a.con_encuesta,
			u.nombre   AS asesor,
			g.grupo    AS grupo,
			m.modelo   AS modelo,
			s.sucursal AS sucursal,
			er.id_respuesta,
			er.resultado_promedio
		FROM asignaciones a
		JOIN  usuarios   u ON a.id_asesor    = u.idusuario
		LEFT JOIN grupos g ON a.id_grupo     = g.idgrupo
		LEFT JOIN modelos m ON a.id_modelo   = m.idmodelo
		LEFT JOIN sucursales s ON a.id_sucursal = s.idsucursal
		LEFT JOIN enc_respuestas er ON er.id_asignacion = a.id_unidad
		WHERE a.entregada = 1
		  AND a.borrar    = 0
		  AND a.guardado  = 1
		  AND a.fec_entrega >= '".ENCUESTA_FECHA_DESDE."'
		  $where_suc
		ORDER BY a.fec_entrega DESC
		LIMIT 200";
$entregas = mysqli_query($con, $SQL);

$tipos_estado = [
	0 => ['label' => 'Sin generar', 'class' => 'badge-enc-sin'],
	1 => ['label' => 'Pendiente',   'class' => 'badge-enc-pendiente'],
	2 => ['label' => 'Completada',  'class' => 'badge-enc-completa'],
];
?>

<div class="enc-sec-header">
	<span class="enc-sec-titulo"><span class="icon-auto"></span> Entregas con Encuesta Pendiente</span>
	<div style="margin-left:auto;display:flex;align-items:center;gap:8px;">
		<label for="filtro_suc_entregas" style="font-size:12px;color:#555;">Sucursal:</label>
		<select id="filtro_suc_entregas" class="enc-select-filtro">
			<option value="0" <?php if ($filtro_sucursal == 0) echo 'selected'; ?>>Todas</option>
			<?php foreach ($sucursales_list as $s): ?>
			<option value="<?php echo $s['idsucursal']; ?>" <?php if ($filtro_sucursal == $s['idsucursal']) echo 'selected'; ?>>
				<?php echo htmlspecialchars($s['sucursal']); ?>
			</option>
			<?php endforeach; ?>
		</select>
	</div>
</div>
<script>
$("#filtro_suc_entregas").on("change", function(){
	window.location = "index.php?sec=entregas&suc=" + $(this).val();
});
</script>

<table class="enc-tabla">
	<thead>
		<tr>
			<td>Fecha entrega</td>
			<td>Cliente</td>
			<td>Grupo / Modelo</td>
			<td>Asesor</td>
			<td>Sucursal</td>
			<td>Estado encuesta</td>
			<td></td>
		</tr>
	</thead>
	<tbody>
	<?php
	$total = 0;
	while ($e = mysqli_fetch_array($entregas)):
		$total++;
		$estado = isset($tipos_estado[$e['con_encuesta']]) ? $tipos_estado[$e['con_encuesta']] : $tipos_estado[0];
	?>
		<tr>
			<td><?php echo cambiarFormatoFecha($e['fec_entrega']); ?></td>
			<td><?php echo htmlspecialchars($e['cliente']); ?></td>
			<td>
				<?php echo htmlspecialchars($e['grupo']); ?>
				<?php if ($e['modelo']) echo '<br><span style="color:#888;font-size:10px;">'.htmlspecialchars($e['modelo']).'</span>'; ?>
			</td>
			<td><?php echo htmlspecialchars($e['asesor']); ?></td>
			<td><?php echo htmlspecialchars($e['sucursal']); ?></td>
			<td style="text-align:center;">
				<span class="badge-enc <?php echo $estado['class']; ?>"><?php echo $estado['label']; ?></span>
				<?php if ($e['con_encuesta'] == 2 && $e['resultado_promedio'] !== null):
					$prom = (float)$e['resultado_promedio'];
					if ($prom >= 8)      $color_prom = '#1e8449';
					elseif ($prom >= 6)  $color_prom = '#d68910';
					else                 $color_prom = '#c0392b';
				?>
				<span style="margin-left:5px;font-weight:bold;font-size:12px;color:<?php echo $color_prom; ?>">
					<?php echo number_format($prom, 1); ?>
				</span>
				<?php endif; ?>
			</td>
			<td class="celda-acciones">
				<?php if ($e['con_encuesta'] == 2 && $e['id_respuesta']): ?>
				<a class="btn-enc btn-enc-verde btn-enc-sm"
				   href="resultados/detalle.php?id=<?php echo (int)$e['id_respuesta']; ?>"
				   title="Ver resultado de la encuesta">
					<span class="icon-line-chart"></span> Resultado
				</a>
				<?php else: ?>
				<a class="btn-enc btn-enc-azul btn-enc-sm"
				   href="admin/puente.php?id=<?php echo (int)$e['id_unidad']; ?>"
				   title="Ver página de encuesta">
					<span class="icon-search"></span> Ver
				</a>
				<?php endif; ?>
			</td>
		</tr>
	<?php endwhile; ?>
	<?php if ($total === 0): ?>
		<tr><td colspan="7" style="text-align:center;padding:20px;color:#888;">No hay entregas registradas.</td></tr>
	<?php endif; ?>
	</tbody>
</table>
<p style="color:#888;font-size:10px;margin-top:6px;">Mostrando hasta 200 entregas. Desde <?php echo ENCUESTA_FECHA_DESDE; ?>.</p>
