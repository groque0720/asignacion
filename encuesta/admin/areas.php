<?php
@session_start();
if ($_SESSION["autentificado"] != "SI") { header("Location: ../../login"); exit(); }
include_once("../config.php");
if (!in_array($_SESSION["idperfil"], ENCUESTA_PERFILES)) { header("Location: ../../login"); exit(); }
include_once("../funciones/func_mysql.php");
conectar();

$areas = [];
$res = mysqli_query($con, "SELECT * FROM enc_areas ORDER BY nro_orden ASC, id_area ASC");
while ($a = mysqli_fetch_array($res)) $areas[] = $a;
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Áreas — Encuesta</title>
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
	<span class="enc-titulo">Áreas Responsables</span>
	<button class="btn-enc btn-enc-azul btn-enc-sm" id="btn-nueva-area" style="margin-left:auto;">
		<span class="icon-plus"></span> Nueva Área
	</button>
</div>

<div class="enc-subpag-contenido">

<!-- FORMULARIO INLINE -->
<div class="enc-form-panel" id="panel-form-area" style="display:none;max-width:500px;margin-bottom:14px;">
	<h3 id="form-area-titulo" style="margin:0 0 12px;font-size:13px;color:#1a5276;">Nueva Área</h3>
	<form id="frm_area">
		<input type="hidden" id="fa_id" name="id_area" value="0">
		<div class="enc-form-row">
			<label for="fa_nombre">Nombre *</label>
			<input type="text" id="fa_nombre" name="nombre" maxlength="80" required placeholder="Ej: Entregas, Créditos...">
		</div>
		<div class="enc-form-row">
			<label for="fa_color">Color</label>
			<div style="display:flex;align-items:center;gap:10px;">
				<input type="color" id="fa_color" name="color" value="#607d8b" style="width:46px;height:30px;border:1px solid #ddd;border-radius:4px;cursor:pointer;padding:2px;">
				<span style="font-size:11px;color:#888;">Se usa para identificar el área en listas y resultados.</span>
			</div>
		</div>
		<div class="enc-form-acciones">
			<button type="submit" class="btn-enc btn-enc-verde">
				<span class="icon-check-square-o"></span> Guardar
			</button>
			<button type="button" class="btn-enc btn-enc-gris" id="btn-cancelar-area">
				<span class="icon-times"></span> Cancelar
			</button>
		</div>
	</form>
</div>

<!-- LISTA DE ÁREAS -->
<p style="color:#888;font-size:11px;margin-bottom:8px;">
	Las áreas se asignan a las preguntas para agrupar los resultados por responsable.
</p>

<div id="lista-areas">
<?php if (empty($areas)): ?>
	<div class="enc-form-panel" style="text-align:center;color:#888;padding:24px;">
		No hay áreas definidas. Creá la primera con el botón "Nueva Área".
	</div>
<?php else: ?>
	<?php foreach ($areas as $a): ?>
	<div class="enc-preg-item" id="area-item-<?php echo $a['id_area']; ?>">
		<div style="width:16px;height:16px;border-radius:50%;background:<?php echo htmlspecialchars($a['color']); ?>;flex-shrink:0;"></div>
		<div style="flex:1;font-size:13px;">
			<strong><?php echo htmlspecialchars($a['nombre']); ?></strong>
			<span style="font-size:11px;color:#aaa;margin-left:8px;"><?php echo htmlspecialchars($a['color']); ?></span>
		</div>
		<div class="enc-preg-acciones">
			<button class="btn-enc btn-enc-gris btn-enc-sm btn-orden-area"
			        data-id="<?php echo $a['id_area']; ?>" data-accion="subir" title="Subir">
				<span class="icon-chevron-up"></span>
			</button>
			<button class="btn-enc btn-enc-gris btn-enc-sm btn-orden-area"
			        data-id="<?php echo $a['id_area']; ?>" data-accion="bajar" title="Bajar">
				<span class="icon-chevron-down"></span>
			</button>
			<button class="btn-enc btn-enc-gris btn-enc-sm btn-editar-area"
			        data-id="<?php echo $a['id_area']; ?>"
			        data-nombre="<?php echo htmlspecialchars($a['nombre'], ENT_QUOTES); ?>"
			        data-color="<?php echo htmlspecialchars($a['color'], ENT_QUOTES); ?>">
				<span class="icon-tools"></span>
			</button>
			<button class="btn-enc btn-enc-rojo btn-enc-sm btn-baja-area"
			        data-id="<?php echo $a['id_area']; ?>"
			        data-nombre="<?php echo htmlspecialchars($a['nombre'], ENT_QUOTES); ?>">
				<span class="icon-times"></span>
			</button>
		</div>
	</div>
	<?php endforeach; ?>
<?php endif; ?>
</div>

</div><!-- /enc-subpag-contenido -->
<div id="msg_respuesta" style="margin:10px 12px;font-size:12px;"></div>

<script>
function mostrarCargando()  { $("#enc_cargando").show(); }
function ocultarCargando()  { $("#enc_cargando").hide(); }

$(function(){

	// ── Mostrar form nueva área ──────────────────────────────
	$("#btn-nueva-area").click(function(){
		$("#form-area-titulo").text("Nueva Área");
		$("#fa_id").val(0);
		$("#frm_area")[0].reset();
		$("#fa_color").val("#607d8b");
		$("#panel-form-area").show();
		$("#fa_nombre").focus();
	});

	$("#btn-cancelar-area").click(function(){
		$("#panel-form-area").hide();
	});

	// ── Cargar para editar ───────────────────────────────────
	$(document).on("click", ".btn-editar-area", function(){
		var id    = $(this).data("id");
		var nom   = $(this).data("nombre");
		var color = $(this).data("color");
		$("#form-area-titulo").text("Editar Área");
		$("#fa_id").val(id);
		$("#fa_nombre").val(nom);
		$("#fa_color").val(color);
		$("#panel-form-area").show();
		$("#fa_nombre").focus();
		$("html, body").animate({scrollTop: 0}, 200);
	});

	// ── Guardar área ─────────────────────────────────────────
	$("#frm_area").submit(function(e){
		e.preventDefault();
		mostrarCargando();
		$.ajax({
			url: "../ajax/area_guardar.php",
			type: "POST",
			data: $(this).serialize(),
			success: function(resp){
				ocultarCargando();
				if (resp.indexOf("ok") === 0) {
					location.reload();
				} else {
					swal("Error", resp, "error");
				}
			},
			error: function(){
				ocultarCargando();
				swal("Error", "Error al guardar.", "error");
			}
		});
	});

	// ── Reordenar área ───────────────────────────────────────
	$(document).on("click", ".btn-orden-area", function(){
		var id     = $(this).data("id");
		var accion = $(this).data("accion");
		$.post("../ajax/area_orden.php", {id_area: id, accion: accion}, function(resp){
			if (resp == "ok") location.reload();
			else swal("Error", resp, "error");
		});
	});

	// ── Dar de baja área ─────────────────────────────────────
	$(document).on("click", ".btn-baja-area", function(){
		var id  = $(this).data("id");
		var nom = $(this).data("nombre");
		swal({
			title: "Eliminar área",
			text: "¿Eliminar el área \"" + nom + "\"? Las preguntas asignadas a ella quedarán sin área.",
			type: "warning",
			showCancelButton: true,
			confirmButtonText: "Sí, eliminar",
			cancelButtonText: "Cancelar"
		}, function(){
			mostrarCargando();
			$.post("../ajax/area_baja.php", {id_area: id}, function(resp){
				ocultarCargando();
				if (resp == "ok") {
					$("#area-item-" + id).fadeOut(200, function(){ $(this).remove(); });
				} else {
					swal("Error", resp, "error");
				}
			});
		});
	});

});
</script>
</body>
</html>
