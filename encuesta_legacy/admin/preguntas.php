<?php
@session_start();
if ($_SESSION["autentificado"] != "SI") { header("Location: ../../login"); exit(); }
include_once("../config.php");
if (!in_array($_SESSION["id"], ENCUESTA_USUARIOS_CONFIG)) { header("Location: ../../login"); exit(); }
include_once("../funciones/func_mysql.php");
conectar();

$id_encuesta = isset($_GET['id_encuesta']) ? (int)$_GET['id_encuesta'] : 0;
if ($id_encuesta <= 0) { header("Location: index.php?sec=config"); exit(); }

// Datos de la encuesta
$res_enc = mysqli_query($con, "SELECT * FROM enc_encuestas WHERE id_encuesta = $id_encuesta AND baja = 0");
if (mysqli_num_rows($res_enc) == 0) { header("Location: index.php?sec=config"); exit(); }
$encuesta = mysqli_fetch_array($res_enc);

// Preguntas ordenadas
$SQL_preg = "SELECT p.*,
					(SELECT COUNT(*) FROM enc_opciones o WHERE o.id_pregunta = p.id_pregunta AND o.baja = 0) AS cant_opciones,
					ar.nombre AS area_nombre,
					ar.color  AS area_color
			 FROM enc_preguntas p
			 LEFT JOIN enc_areas ar ON p.id_area = ar.id_area
			 WHERE p.id_encuesta = $id_encuesta AND p.baja = 0
			 ORDER BY p.nro_orden ASC";
$preguntas = mysqli_query($con, $SQL_preg);

$tipos = [
	1 => 'Escala 1-10',
	2 => 'Sí / No',
	3 => 'Selección múltiple',
	4 => 'Lista Sí/No',
	5 => 'Texto libre',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Preguntas — <?php echo htmlspecialchars($encuesta['nombre']); ?></title>
	<link rel="shortcut icon" type="image/x-icon" href="../../asignacion/imagenes/favicon.ico" />
	<script src="../js/jquery-2.1.3.min.js"></script>
	<script src="../alertas_query/sweetalert-dev.js"></script>
	<link rel="stylesheet" href="../alertas_query/sweetalert.css">
	<link rel="stylesheet" href="../en_proceso/en_proceso.css">
	<link rel="stylesheet" href="../css/encuesta_admin.css">
	<link href="../../asignacion/css/iconos.css" rel="stylesheet">
</head>
<body>
<?php include('../en_proceso/en_proceso.php'); ?>

<div class="enc-subpag-cabecera">
	<a class="btn-enc btn-enc-gris btn-enc-sm" href="../index.php?sec=config">← Volver</a>
	<span class="enc-titulo">
		Preguntas: <strong><?php echo htmlspecialchars($encuesta['nombre']); ?></strong>
	</span>
	<?php if ($encuesta['activa']): ?>
		<span class="badge-enc badge-enc-activa" style="margin-left:8px;">Activa</span>
	<?php endif; ?>
	<a class="btn-enc btn-enc-azul btn-enc-sm" style="margin-left:auto;"
	   href="pregunta_form.php?id_encuesta=<?php echo $id_encuesta; ?>">
		<span class="icon-plus"></span> Nueva Pregunta
	</a>
</div>

<div class="enc-subpag-contenido">

<?php
$cant_preg = 0;
$preg_arr  = [];
while ($p = mysqli_fetch_array($preguntas)) {
	$preg_arr[] = $p;
	$cant_preg++;
}
?>

<?php if ($cant_preg == 0): ?>
	<div class="enc-form-panel" style="text-align:center;color:#888;padding:30px;">
		No hay preguntas todavía.
		<a href="pregunta_form.php?id_encuesta=<?php echo $id_encuesta; ?>" class="btn-enc btn-enc-azul" style="margin-top:10px;display:inline-block;">
			<span class="icon-plus"></span> Agregar primera pregunta
		</a>
	</div>
<?php else: ?>
	<p style="color:#888;font-size:11px;margin-bottom:8px;"><?php echo $cant_preg; ?> pregunta(s) configurada(s).</p>
	<div id="lista-preguntas">
	<?php foreach ($preg_arr as $p): ?>
	<div class="enc-preg-item" id="preg-item-<?php echo $p['id_pregunta']; ?>">
		<div class="enc-preg-nro"><?php echo $p['nro_orden']; ?></div>
		<div style="flex:1;">
			<div class="enc-preg-texto"><?php echo htmlspecialchars($p['texto_pregunta']); ?></div>
			<div class="enc-preg-badges">
				<span class="enc-badge-tipo"><?php echo $tipos[$p['tipo_pregunta']] ?? '?'; ?></span>
				<?php if ($p['pondera']): ?>
					<span class="enc-badge-tipo enc-badge-pondera">Pondera</span>
				<?php else: ?>
					<span class="enc-badge-tipo enc-badge-no-pondera">No pondera</span>
				<?php endif; ?>
				<?php if ($p['es_observacion']): ?>
					<span class="enc-badge-tipo" style="background:#f8e6ff;color:#6c3483;">Observación</span>
				<?php endif; ?>
				<?php if ($p['cant_opciones'] > 0): ?>
					<span class="enc-badge-tipo" style="background:#fef9e7;color:#7d6608;"><?php echo $p['cant_opciones']; ?> opción(es)</span>
				<?php endif; ?>
				<?php if ($p['cond_id_preg_ref']): ?>
					<span class="enc-badge-tipo enc-badge-cond">Condicional</span>
				<?php endif; ?>
				<?php if ($p['area_nombre']): ?>
					<span class="enc-badge-tipo enc-badge-area"
					      style="background:<?php echo htmlspecialchars($p['area_color']); ?>22;color:<?php echo htmlspecialchars($p['area_color']); ?>;border-color:<?php echo htmlspecialchars($p['area_color']); ?>44;">
						<?php echo htmlspecialchars($p['area_nombre']); ?>
					</span>
				<?php endif; ?>
			</div>
		</div>
		<div class="enc-preg-acciones">
			<?php if ($p['nro_orden'] > 1): ?>
			<button class="btn-enc btn-enc-gris btn-enc-sm btn-subir"
			        data-id="<?php echo $p['id_pregunta']; ?>"
			        data-encuesta="<?php echo $id_encuesta; ?>"
			        title="Subir">↑</button>
			<?php endif; ?>
			<?php if ($p['nro_orden'] < $cant_preg): ?>
			<button class="btn-enc btn-enc-gris btn-enc-sm btn-bajar"
			        data-id="<?php echo $p['id_pregunta']; ?>"
			        data-encuesta="<?php echo $id_encuesta; ?>"
			        title="Bajar">↓</button>
			<?php endif; ?>
			<a class="btn-enc btn-enc-azul btn-enc-sm"
			   href="pregunta_form.php?id_encuesta=<?php echo $id_encuesta; ?>&id=<?php echo $p['id_pregunta']; ?>">
				<span class="icon-tools"></span>
			</a>
			<button class="btn-enc btn-enc-rojo btn-enc-sm btn-baja-preg"
			        data-id="<?php echo $p['id_pregunta']; ?>">
				<span class="icon-times"></span>
			</button>
		</div>
	</div>
	<?php endforeach; ?>
	</div>
<?php endif; ?>

</div><!-- /enc-subpag-contenido -->
<div id="msg_respuesta" style="margin:10px 12px;font-size:12px;"></div>

<script>
function mostrarCargando()  { $("#enc_cargando").show(); }
function ocultarCargando()  { $("#enc_cargando").hide(); }

$(function(){

	// ── Subir pregunta ───────────────────────────────────────
	$(document).on("click", ".btn-subir", function(){
		var id  = $(this).data("id");
		var enc = $(this).data("encuesta");
		mostrarCargando();
		$.post("../ajax/pregunta_orden.php",
			{id_pregunta: id, id_encuesta: enc, accion: "subir"},
			function(r){ ocultarCargando(); if(r=="ok") location.reload(); else swal("Error",r,"error"); }
		);
	});

	// ── Bajar pregunta ───────────────────────────────────────
	$(document).on("click", ".btn-bajar", function(){
		var id  = $(this).data("id");
		var enc = $(this).data("encuesta");
		mostrarCargando();
		$.post("../ajax/pregunta_orden.php",
			{id_pregunta: id, id_encuesta: enc, accion: "bajar"},
			function(r){ ocultarCargando(); if(r=="ok") location.reload(); else swal("Error",r,"error"); }
		);
	});

	// ── Dar de baja pregunta ─────────────────────────────────
	$(document).on("click", ".btn-baja-preg", function(){
		var id = $(this).data("id");
		swal({
			title: "Eliminar pregunta",
			text: "¿Dar de baja esta pregunta? Esta acción no se puede deshacer.",
			type: "warning",
			showCancelButton: true,
			confirmButtonText: "Sí, eliminar",
			cancelButtonText: "Cancelar"
		}, function(){
			mostrarCargando();
			$.post("../ajax/pregunta_baja.php", {id_pregunta: id}, function(r){
				ocultarCargando();
				if (r=="ok") location.reload();
				else swal("Error", r, "error");
			});
		});
	});

});
</script>
</body>
</html>
