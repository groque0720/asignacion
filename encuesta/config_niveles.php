<?php
@session_start();
if ($_SESSION["autentificado"] != "SI") { header("Location: ../login"); exit(); }
include_once("config.php");
if (!in_array($_SESSION["idperfil"], ENCUESTA_PERFILES)) { header("Location: ../login"); exit(); }
include_once("funciones/func_mysql.php");
conectar();

$niveles = [];
$res = mysqli_query($con, "SELECT * FROM enc_niveles ORDER BY valor_desde DESC");
while ($n = mysqli_fetch_array($res)) $niveles[] = $n;
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Niveles de Resultado — Encuesta</title>
	<link rel="shortcut icon" type="image/x-icon" href="../asignacion/imagenes/favicon.ico" />
	<script src="js/jquery-2.1.3.min.js"></script>
	<script src="alertas_query/sweetalert-dev.js"></script>
	<link rel="stylesheet" href="alertas_query/sweetalert.css">
	<link rel="stylesheet" href="en_proceso/en_proceso.css">
	<link rel="stylesheet" href="css/encuesta_admin.css">
	<link href="../asignacion/css/iconos.css" rel="stylesheet">
</head>
<body>
<?php include('en_proceso/en_proceso.php'); ?>

<div class="enc-subpag-cabecera">
	<a class="btn-enc btn-enc-gris btn-enc-sm" href="index.php?sec=config">← Volver</a>
	<span class="enc-titulo">Niveles de Resultado</span>
	<button class="btn-enc btn-enc-azul btn-enc-sm" id="btn-nuevo-nivel" style="margin-left:auto;">
		<span class="icon-plus"></span> Nuevo Nivel
	</button>
</div>

<div class="enc-subpag-contenido">

<p style="color:#888;font-size:11px;margin-bottom:14px;">
	Definí rangos de score (escala 0–10) con un nombre y color. Al mostrar un resultado
	se elige automáticamente el nivel cuyo rango lo contiene.<br>
	Ejemplo: <em>score 8.6 → "Alta satisfacción" (9–10)</em>.
	Los rangos no deben solaparse.
</p>

<!-- FORMULARIO INLINE -->
<div class="enc-form-panel" id="panel-form-nivel" style="display:none;max-width:540px;margin-bottom:16px;">
	<h3 id="form-nivel-titulo" style="margin:0 0 12px;font-size:13px;color:#1a5276;">Nuevo Nivel</h3>
	<form id="frm_nivel">
		<input type="hidden" id="fn_id" name="id_nivel" value="0">
		<div class="enc-form-row">
			<label for="fn_nombre">Nombre *</label>
			<input type="text" id="fn_nombre" name="nombre" maxlength="80" required placeholder="Ej: Alta satisfacción">
		</div>
		<div class="enc-form-row" style="align-items:center;">
			<label>Rango *</label>
			<div style="display:flex;align-items:center;gap:10px;">
				<input type="number" id="fn_desde" name="valor_desde" min="0" max="10" step="0.1" required
				       style="width:70px;" placeholder="0">
				<span style="color:#888;font-size:12px;">hasta</span>
				<input type="number" id="fn_hasta" name="valor_hasta" min="0" max="10" step="0.1" required
				       style="width:70px;" placeholder="10">
				<span style="color:#aaa;font-size:11px;">(escala 0 – 10)</span>
			</div>
		</div>
		<div class="enc-form-row" style="align-items:center;">
			<label for="fn_color">Color</label>
			<div style="display:flex;align-items:center;gap:10px;">
				<input type="color" id="fn_color" name="color" value="#1e8449"
				       style="width:46px;height:30px;border:1px solid #ddd;border-radius:4px;cursor:pointer;padding:2px;">
				<span style="font-size:11px;color:#888;">Color del texto y badge de nivel.</span>
			</div>
		</div>
		<div class="enc-form-acciones">
			<button type="submit" class="btn-enc btn-enc-verde">
				<span class="icon-check-square-o"></span> Guardar
			</button>
			<button type="button" class="btn-enc btn-enc-gris" id="btn-cancelar-nivel">
				<span class="icon-times"></span> Cancelar
			</button>
		</div>
	</form>
</div>

<!-- LISTA DE NIVELES -->
<div id="lista-niveles">
<?php if (empty($niveles)): ?>
	<div class="enc-form-panel" style="text-align:center;color:#888;padding:24px;">
		No hay niveles definidos. Creá el primero con el botón "Nuevo Nivel".
	</div>
<?php else: ?>
	<?php foreach ($niveles as $n): ?>
	<div class="enc-preg-item" id="nivel-item-<?php echo $n['id_nivel']; ?>">
		<!-- Preview de color -->
		<div style="width:14px;height:14px;border-radius:50%;background:<?php echo htmlspecialchars($n['color']); ?>;flex-shrink:0;"></div>
		<!-- Nombre y rango -->
		<div style="flex:1;font-size:13px;">
			<strong style="color:<?php echo htmlspecialchars($n['color']); ?>">
				<?php echo htmlspecialchars($n['nombre']); ?>
			</strong>
			<span style="font-size:11px;color:#aaa;margin-left:8px;">
				Score <?php echo number_format($n['valor_desde'],1); ?>
				&ndash;
				<?php echo number_format($n['valor_hasta'],1); ?>
			</span>
		</div>
		<!-- Mini barra de rango -->
		<div style="width:120px;height:8px;background:#eee;border-radius:4px;overflow:hidden;flex-shrink:0;">
			<div style="height:100%;border-radius:4px;background:<?php echo htmlspecialchars($n['color']); ?>;
			            margin-left:<?php echo $n['valor_desde']*10; ?>%;
			            width:<?php echo ($n['valor_hasta']-$n['valor_desde'])*10; ?>%;"></div>
		</div>
		<!-- Acciones -->
		<div class="enc-preg-acciones">
			<button class="btn-enc btn-enc-gris btn-enc-sm btn-editar-nivel"
			        data-id="<?php echo $n['id_nivel']; ?>"
			        data-nombre="<?php echo htmlspecialchars($n['nombre'], ENT_QUOTES); ?>"
			        data-desde="<?php echo $n['valor_desde']; ?>"
			        data-hasta="<?php echo $n['valor_hasta']; ?>"
			        data-color="<?php echo htmlspecialchars($n['color'], ENT_QUOTES); ?>">
				<span class="icon-tools"></span>
			</button>
			<button class="btn-enc btn-enc-rojo btn-enc-sm btn-baja-nivel"
			        data-id="<?php echo $n['id_nivel']; ?>"
			        data-nombre="<?php echo htmlspecialchars($n['nombre'], ENT_QUOTES); ?>">
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

	$("#btn-nuevo-nivel").click(function(){
		$("#form-nivel-titulo").text("Nuevo Nivel");
		$("#fn_id").val(0);
		$("#frm_nivel")[0].reset();
		$("#fn_color").val("#1e8449");
		$("#panel-form-nivel").show();
		$("#fn_nombre").focus();
	});

	$("#btn-cancelar-nivel").click(function(){
		$("#panel-form-nivel").hide();
	});

	$(document).on("click", ".btn-editar-nivel", function(){
		$("#form-nivel-titulo").text("Editar Nivel");
		$("#fn_id").val($(this).data("id"));
		$("#fn_nombre").val($(this).data("nombre"));
		$("#fn_desde").val($(this).data("desde"));
		$("#fn_hasta").val($(this).data("hasta"));
		$("#fn_color").val($(this).data("color"));
		$("#panel-form-nivel").show();
		$("#fn_nombre").focus();
		$("html, body").animate({scrollTop: 0}, 200);
	});

	$("#frm_nivel").submit(function(e){
		e.preventDefault();
		var desde = parseFloat($("#fn_desde").val());
		var hasta = parseFloat($("#fn_hasta").val());
		if (desde >= hasta) {
			swal("Error", "El valor 'desde' debe ser menor que 'hasta'.", "error");
			return;
		}
		mostrarCargando();
		$.ajax({
			url: "config_nivel_guardar.php",
			type: "POST",
			data: $(this).serialize(),
			success: function(resp){
				ocultarCargando();
				if (resp.indexOf("ok") === 0) location.reload();
				else swal("Error", resp, "error");
			},
			error: function(){ ocultarCargando(); swal("Error", "Error al guardar.", "error"); }
		});
	});

	$(document).on("click", ".btn-baja-nivel", function(){
		var id  = $(this).data("id");
		var nom = $(this).data("nombre");
		swal({
			title: "Eliminar nivel",
			text: "¿Eliminar el nivel \"" + nom + "\"?",
			type: "warning",
			showCancelButton: true,
			confirmButtonText: "Sí, eliminar",
			cancelButtonText: "Cancelar"
		}, function(){
			mostrarCargando();
			$.post("config_nivel_baja.php", {id_nivel: id}, function(resp){
				ocultarCargando();
				if (resp == "ok") $("#nivel-item-" + id).fadeOut(200, function(){ $(this).remove(); });
				else swal("Error", resp, "error");
			});
		});
	});

});
</script>
</body>
</html>
