<?php
// =====================================================================
// Historial de cambios de una unidad.
// Lee `auditoria_unidades` (1 fila = 1 Guardar) y decodea el JSON
// de la columna `movimiento` para mostrar el delta.
// Recibe ?id_unidad=X. Solo lectura.
// =====================================================================

include("funciones/func_mysql.php");
conectar();
mysqli_query($con, "SET NAMES 'utf8'");

@session_start();
if (!isset($_SESSION["autentificado"]) || $_SESSION["autentificado"] != "SI") {
	header("Location: ../login");
	exit();
}

$id_unidad = isset($_GET['id_unidad']) ? (int)$_GET['id_unidad'] : 0;

if ($id_unidad <= 0) {
	echo "<p style='padding:20px;color:#c0392b;'>Falta el parámetro id_unidad.</p>";
	exit();
}

// ──────────────── Cabecera de la unidad ────────────────
$SQL = "SELECT a.id_unidad, a.nro_unidad, a.chasis, a.cliente,
               g.grupo AS modelo, m.modelo AS version, s.sucres AS sucursal
        FROM asignaciones a
        LEFT JOIN grupos g     ON g.idgrupo   = a.id_grupo
        LEFT JOIN modelos m    ON m.idmodelo  = a.id_modelo
        LEFT JOIN sucursales s ON s.idsucursal = a.id_sucursal
        WHERE a.id_unidad = $id_unidad";
$res = mysqli_query($con, $SQL);
$unidad = $res ? mysqli_fetch_assoc($res) : null;

if (!$unidad) {
	echo "<p style='padding:20px;color:#c0392b;'>Unidad inexistente.</p>";
	exit();
}

// ──────────────── Cachés FK → nombre ────────────────
function cargar_cache($con, $sql, $key, $val) {
	$out = [];
	$rs = mysqli_query($con, $sql);
	if ($rs) {
		while ($r = mysqli_fetch_assoc($rs)) {
			$out[(string)$r[$key]] = $r[$val];
		}
	}
	return $out;
}
$caches = [
	'id_negocio'           => cargar_cache($con, "SELECT id_negocio, negocio FROM negocios", 'id_negocio', 'negocio'),
	'id_mes'               => cargar_cache($con, "SELECT idmes, mes FROM meses", 'idmes', 'mes'),
	'id_grupo'             => cargar_cache($con, "SELECT idgrupo, grupo FROM grupos", 'idgrupo', 'grupo'),
	'id_modelo'            => cargar_cache($con, "SELECT idmodelo, modelo FROM modelos", 'idmodelo', 'modelo'),
	'id_color'             => cargar_cache($con, "SELECT idcolor, color FROM colores", 'idcolor', 'color'),
	'id_sucursal'          => cargar_cache($con, "SELECT idsucursal, sucres FROM sucursales", 'idsucursal', 'sucres'),
	'id_asesor'            => cargar_cache($con, "SELECT idusuario, nombre FROM usuarios", 'idusuario', 'nombre'),
	'id_estado_entrega'    => cargar_cache($con, "SELECT id_estado_entrega, estado_unidad FROM entregas_estados_unidad", 'id_estado_entrega', 'estado_unidad'),
	'id_ubicacion_entrega' => cargar_cache($con, "SELECT id_ubicacion_entrega, ubicacion_entrega FROM entregas_ubicaciones", 'id_ubicacion_entrega', 'ubicacion_entrega'),
];
$caches['id_ubicacion'] = $caches['id_sucursal'];
$caches['color_uno']    = $caches['id_color'];
$caches['color_dos']    = $caches['id_color'];
$caches['color_tres']   = $caches['id_color'];

$booleanos = [
	'estado_tasa'        => ['0' => 'No Confirmada', '1' => 'Confirmada'],
	'estado_reserva'     => ['0' => 'No Confirmada', '1' => 'Confirmada'],
	'reservada'          => ['0' => 'No', '1' => 'Sí'],
	'reserva'            => ['0' => 'No', '1' => 'Sí'],
	'cancelada'          => ['0' => 'No', '1' => 'Sí'],
	'entregada'          => ['0' => 'No', '1' => 'Sí'],
	'pagado'             => ['0' => 'No', '1' => 'Sí'],
	'no_disponible'      => ['0' => 'Disponible', '1' => 'No disponible'],
	'borrar'             => ['0' => 'Activo', '1' => 'Borrado lógico'],
	'reventa'            => ['0' => 'No', '1' => 'Sí'],
	'servicio_conectado' => ['0' => 'No', '1' => 'Sí'],
	'con_encuesta'       => ['0' => 'Sin encuesta', '1' => 'Encuesta pendiente', '2' => 'Encuesta completada'],
];

$etiquetas = [
	'nro_unidad'           => 'Nro Unidad',
	'chasis'               => 'Chasis',
	'nro_orden'            => 'Nro Orden',
	'interno'              => 'Interno',
	'patente'              => 'Patente',
	'id_grupo'             => 'Modelo',
	'id_modelo'            => 'Versión',
	'id_color'             => 'Color',
	'color_uno'            => 'Color 1',
	'color_dos'            => 'Color 2',
	'color_tres'           => 'Color 3',
	'id_sucursal'          => 'Sucursal Destino',
	'id_ubicacion'         => 'Ubicación',
	'id_ubicacion_entrega' => 'Ubic. Entrega',
	'estado_tasa'          => 'Estado TASA',
	'estado_reserva'       => 'Estado Reserva',
	'reservada'            => 'Reservada',
	'reserva'              => 'Reserva',
	'cancelada'            => 'Cancelada',
	'entregada'            => 'Entregada',
	'pagado'               => 'Pagado',
	'no_disponible'        => 'Disponibilidad',
	'borrar'               => 'Borrar',
	'reventa'              => 'Reventa',
	'servicio_conectado'   => 'Serv. Conectado',
	'fec_playa'            => 'Fec. Playa',
	'fec_despacho'         => 'Fec. Despacho',
	'fec_arribo'           => 'Fec. Arribo',
	'fec_reserva'          => 'Fec. Reserva',
	'fec_limite'           => 'Fec. Lim. Cancelación',
	'fec_cancelacion'      => 'Fec. Cancelación',
	'fec_entrega'          => 'Fec. Entrega',
	'fec_inscripcion'      => 'Fec. Inscripción',
	'fec_pedido'           => 'Fec. Pedido',
	'costo'                => 'Costo',
	'cliente'              => 'Cliente',
	'id_asesor'            => 'Asesor',
	'id_negocio'           => 'Negocio',
	'id_mes'               => 'Mes',
	'año'                  => 'Año',
	'nro_remito'           => 'Nro Remito',
	'observacion'          => 'Observación',
	'hora'                 => 'Hora Reserva',
	'id_estado_entrega'    => 'Estado Entrega',
	'hora_pedido'          => 'Hora Pedido',
	'con_encuesta'         => 'Encuesta',
];

function fmt_valor($campo, $valor, $caches, $booleanos) {
	if ($valor === null || $valor === '') {
		return "<span style='color:#999;font-style:italic;'>(vacío)</span>";
	}
	$str = (string)$valor;
	if (isset($booleanos[$campo]) && isset($booleanos[$campo][$str])) {
		return htmlspecialchars($booleanos[$campo][$str]);
	}
	if (isset($caches[$campo]) && isset($caches[$campo][$str])) {
		return htmlspecialchars($caches[$campo][$str]) . " <span style='color:#888;font-size:0.85em;'>(#$str)</span>";
	}
	if ($campo === 'costo' && is_numeric($valor)) {
		return '$ ' . number_format((float)$valor, 0, ',', '.');
	}
	return htmlspecialchars($str);
}

// ──────────────── Carga registros (1 por Guardar) ────────────────
$SQL = "SELECT id_audit, fecha, hora, usuario, origen, cant_campos, movimiento
        FROM auditoria_unidades
        WHERE id_unidad = $id_unidad
        ORDER BY id_audit DESC";
$rs = mysqli_query($con, $SQL);
$registros = [];
if ($rs) {
	while ($r = mysqli_fetch_assoc($rs)) {
		$r['cambios'] = json_decode($r['movimiento'], true) ?: [];
		$registros[] = $r;
	}
}
$total = count($registros);
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<title>Historial Unidad <?php echo (int)$unidad['nro_unidad']; ?></title>
	<link rel="shortcut icon" type="image/x-icon" href="imagenes/favicon.ico" />
	<link rel="stylesheet" href="css/roquesystem.css">
	<style>
		body { font-family: Arial, Helvetica, sans-serif; margin: 0; background: #f4f6f8; color: #222; }
		.cab { background: #1a4b7d; color: #fff; padding: 14px 22px; }
		.cab h1 { margin: 0; font-size: 18px; }
		.cab .meta { font-size: 13px; opacity: .9; margin-top: 4px; }
		.cont { padding: 18px 22px; max-width: 1100px; }
		.btn-volver { display: inline-block; padding: 6px 14px; background: #555; color: #fff; text-decoration: none; border-radius: 4px; font-size: 13px; }
		.btn-volver:hover { background: #333; }
		.evento { background: #fff; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,.07); margin-bottom: 14px; overflow: hidden; }
		.evento-cab { background: #2c6ba8; color: #fff; padding: 9px 14px; font-size: 13px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; }
		.evento-cab .izq { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; }
		.evento-cab .fechahora { font-weight: 600; }
		.evento-cab .usuario { background: rgba(255,255,255,.18); padding: 2px 8px; border-radius: 12px; font-size: 12px; }
		.evento-cab .origen { color: rgba(255,255,255,.75); font-size: 11px; font-style: italic; }
		.evento-cab .cant { background: #fff; color: #2c6ba8; padding: 2px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; }
		.evento-tabla { width: 100%; border-collapse: collapse; }
		.evento-tabla td { padding: 7px 14px; border-bottom: 1px solid #f0f0f0; font-size: 13px; vertical-align: top; }
		.evento-tabla tr:last-child td { border-bottom: none; }
		.evento-tabla tr:hover td { background: #fafcff; }
		.campo { font-weight: 600; color: #1a4b7d; white-space: nowrap; width: 200px; }
		.cambio-anterior { color: #c0392b; }
		.cambio-actual { color: #1e8449; font-weight: 600; }
		.flecha { color: #888; padding: 0 6px; width: 20px; text-align: center; }
		.vacio { padding: 40px; text-align: center; color: #888; background: #fff; border-radius: 6px; }
	</style>
</head>
<body>
	<div class="cab">
		<h1>Historial de cambios — Unidad N° <?php echo htmlspecialchars($unidad['nro_unidad']); ?></h1>
		<div class="meta">
			<?php echo htmlspecialchars(trim($unidad['modelo'].' '.$unidad['version'])); ?>
			<?php if (!empty($unidad['chasis'])): ?> · Chasis: <?php echo htmlspecialchars($unidad['chasis']); ?><?php endif; ?>
			<?php if (!empty($unidad['cliente'])): ?> · Cliente: <?php echo htmlspecialchars($unidad['cliente']); ?><?php endif; ?>
			<?php if (!empty($unidad['sucursal'])): ?> · Sucursal: <?php echo htmlspecialchars($unidad['sucursal']); ?><?php endif; ?>
			· <?php echo $total; ?> guardado<?php echo $total === 1 ? '' : 's'; ?>
		</div>
	</div>
	<div class="cont">
		<p><a href="javascript:window.close();" class="btn-volver">Cerrar</a></p>

		<?php if ($total === 0): ?>
			<div class="vacio">Esta unidad no tiene cambios registrados.</div>
		<?php else: ?>
			<?php foreach ($registros as $r): ?>
				<div class="evento">
					<div class="evento-cab">
						<div class="izq">
							<span class="fechahora">
								<?php echo date('d/m/Y', strtotime($r['fecha'])); ?>
								<?php echo substr($r['hora'], 0, 5); ?>
							</span>
							<span class="usuario"><?php echo htmlspecialchars($r['usuario']); ?></span>
							<?php if (!empty($r['origen'])): ?>
								<span class="origen"><?php echo htmlspecialchars($r['origen']); ?></span>
							<?php endif; ?>
						</div>
						<span class="cant"><?php echo (int)$r['cant_campos']; ?> campo<?php echo $r['cant_campos'] == 1 ? '' : 's'; ?></span>
					</div>
					<?php if (!empty($r['cambios'])): ?>
					<table class="evento-tabla">
						<tbody>
							<?php foreach ($r['cambios'] as $c): ?>
								<tr>
									<td class="campo"><?php echo htmlspecialchars($etiquetas[$c['campo']] ?? $c['campo']); ?></td>
									<td class="cambio-anterior"><?php echo fmt_valor($c['campo'], $c['antes'], $caches, $booleanos); ?></td>
									<td class="flecha">→</td>
									<td class="cambio-actual"><?php echo fmt_valor($c['campo'], $c['despues'], $caches, $booleanos); ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
</body>
</html>
