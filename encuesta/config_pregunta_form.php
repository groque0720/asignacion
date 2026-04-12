<?php
@session_start();
if ($_SESSION["autentificado"] != "SI") { header("Location: ../login"); exit(); }
include_once("config.php");
if (!in_array($_SESSION["idperfil"], ENCUESTA_PERFILES)) { header("Location: ../login"); exit(); }
include_once("funciones/func_mysql.php");
conectar();

$id_encuesta = isset($_GET['id_encuesta']) ? (int)$_GET['id_encuesta'] : 0;
$id_pregunta = isset($_GET['id'])          ? (int)$_GET['id']          : 0;
if ($id_encuesta <= 0) { header("Location: index.php?sec=config"); exit(); }

// Datos de la encuesta
$res_enc = mysqli_query($con, "SELECT * FROM enc_encuestas WHERE id_encuesta = $id_encuesta AND baja = 0");
if (mysqli_num_rows($res_enc) == 0) { header("Location: index.php?sec=config"); exit(); }
$encuesta = mysqli_fetch_array($res_enc);

// Preguntas existentes para el selector de condición
$SQL_otras = "SELECT id_pregunta, nro_orden, texto_pregunta, tipo_pregunta
			  FROM enc_preguntas
			  WHERE id_encuesta = $id_encuesta AND baja = 0";
if ($id_pregunta > 0) $SQL_otras .= " AND id_pregunta != $id_pregunta";
$SQL_otras .= " ORDER BY nro_orden ASC";
$otras_preguntas = mysqli_query($con, $SQL_otras);

// Datos de la pregunta si es edición
$pregunta = null;
$opciones = [];
if ($id_pregunta > 0) {
	$res_p = mysqli_query($con, "SELECT * FROM enc_preguntas WHERE id_pregunta = $id_pregunta AND id_encuesta = $id_encuesta AND baja = 0");
	if (mysqli_num_rows($res_p) == 0) { header("Location: config_preguntas.php?id_encuesta=$id_encuesta"); exit(); }
	$pregunta = mysqli_fetch_array($res_p);

	$res_op = mysqli_query($con, "SELECT * FROM enc_opciones WHERE id_pregunta = $id_pregunta AND baja = 0 ORDER BY nro_orden ASC");
	while ($op = mysqli_fetch_array($res_op)) $opciones[] = $op;
}

$tipos_ponderables = [1, 2, 3]; // tipos que pueden tener condición como referencia

// Áreas disponibles
$areas = [];
$res_areas = mysqli_query($con, "SELECT * FROM enc_areas ORDER BY nro_orden ASC");
while ($a = mysqli_fetch_array($res_areas)) $areas[] = $a;
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $pregunta ? 'Editar Pregunta' : 'Nueva Pregunta'; ?> — <?php echo htmlspecialchars($encuesta['nombre']); ?></title>
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
	<a class="btn-enc btn-enc-gris btn-enc-sm" href="config_preguntas.php?id_encuesta=<?php echo $id_encuesta; ?>">← Volver</a>
	<span class="enc-titulo">
		<?php echo $pregunta ? 'Editar Pregunta' : 'Nueva Pregunta'; ?> —
		<span style="color:#888;"><?php echo htmlspecialchars($encuesta['nombre']); ?></span>
	</span>
</div>

<div class="enc-subpag-contenido">
<div class="enc-form-panel" style="max-width:700px;">

<form id="frm_pregunta">
	<input type="hidden" name="id_encuesta"  value="<?php echo $id_encuesta; ?>">
	<input type="hidden" name="id_pregunta"  value="<?php echo $id_pregunta; ?>">

	<!-- Texto de la pregunta -->
	<div class="enc-form-row">
		<label for="fp_texto">Pregunta *</label>
		<textarea id="fp_texto" name="texto_pregunta" rows="3" required
		><?php echo $pregunta ? htmlspecialchars($pregunta['texto_pregunta']) : ''; ?></textarea>
	</div>

	<!-- Tipo -->
	<div class="enc-form-row">
		<label for="fp_tipo">Tipo *</label>
		<select id="fp_tipo" name="tipo_pregunta">
			<option value="1" <?php if ($pregunta && $pregunta['tipo_pregunta']==1) echo 'selected'; ?>>Escala 1 a 10</option>
			<option value="2" <?php if ($pregunta && $pregunta['tipo_pregunta']==2) echo 'selected'; ?>>Sí / No</option>
			<option value="3" <?php if ($pregunta && $pregunta['tipo_pregunta']==3) echo 'selected'; ?>>Selección múltiple (checkboxes)</option>
			<option value="4" <?php if ($pregunta && $pregunta['tipo_pregunta']==4) echo 'selected'; ?>>Lista Sí/No (sub-ítems)</option>
			<option value="5" <?php if ($pregunta && $pregunta['tipo_pregunta']==5) echo 'selected'; ?>>Texto libre / Observaciones</option>
		</select>
	</div>

	<!-- Área responsable -->
	<div class="enc-form-row">
		<label for="fp_area">Área responsable</label>
		<div>
			<select id="fp_area" name="id_area">
				<option value="">— Sin área —</option>
				<?php foreach ($areas as $a): ?>
				<option value="<?php echo $a['id_area']; ?>"
				        data-color="<?php echo htmlspecialchars($a['color']); ?>"
				        <?php if ($pregunta && $pregunta['id_area'] == $a['id_area']) echo 'selected'; ?>>
					<?php echo htmlspecialchars($a['nombre']); ?>
				</option>
				<?php endforeach; ?>
			</select>
			<?php if (empty($areas)): ?>
			<p style="color:#888;font-size:10px;margin-top:3px;">
				No hay áreas definidas. <a href="config_areas.php" target="_blank">Crear áreas</a>.
			</p>
			<?php endif; ?>
		</div>
	</div>

	<!-- Pondera -->
	<div class="enc-form-row" id="row-pondera">
		<label>Pondera en promedio</label>
		<div>
			<label>
				<input type="checkbox" name="pondera" id="fp_pondera" value="1"
				<?php echo (!$pregunta || $pregunta['pondera']) ? 'checked' : ''; ?>>
				Sí, esta pregunta influye en el resultado final
			</label>
			<p style="color:#888;font-size:10px;margin-top:3px;">Los tipos "Lista Sí/No" y "Texto libre" no ponderan nunca.</p>
		</div>
	</div>

	<!-- Es observación -->
	<div class="enc-form-row" id="row-obs" style="display:none;">
		<label>Observaciones</label>
		<label>
			<input type="checkbox" name="es_observacion" id="fp_obs" value="1"
			<?php echo ($pregunta && $pregunta['es_observacion']) ? 'checked' : ''; ?>>
			Marcar como campo de comentarios/observaciones
		</label>
	</div>

	<!-- OPCIONES (para tipos 3 y 4) -->
	<div id="seccion-opciones" style="display:none;">
		<hr style="margin:14px 0;border-color:#eee;">
		<div style="font-weight:bold;color:#1a5276;margin-bottom:8px;">
			<span class="icon-tools"></span> Opciones
			<span id="label-tipo-opciones" style="font-weight:normal;color:#888;font-size:11px;"></span>
		</div>
		<div class="enc-opciones-lista" id="lista-opciones">
			<?php foreach ($opciones as $op): ?>
			<div class="enc-opcion-item" id="opcion-<?php echo $op['id_opcion']; ?>">
				<span class="enc-opcion-texto"><?php echo htmlspecialchars($op['texto_opcion']); ?></span>
				<button type="button" class="btn-enc btn-enc-rojo btn-enc-sm btn-del-opcion"
				        data-id="<?php echo $op['id_opcion']; ?>">
					<span class="icon-times"></span>
				</button>
			</div>
			<?php endforeach; ?>
		</div>
		<div class="enc-nueva-opcion">
			<input type="text" id="nueva-opcion-texto" placeholder="Texto de la opción..." maxlength="300">
			<button type="button" class="btn-enc btn-enc-azul btn-enc-sm" id="btn-agregar-opcion">
				<span class="icon-plus"></span> Agregar
			</button>
		</div>
		<p id="opciones-aviso" style="color:#c0392b;font-size:11px;margin-top:5px;display:none;">
			Guardá la pregunta primero para poder agregar opciones.
		</p>
	</div>

	<!-- CONDICIÓN -->
	<div id="seccion-condicion">
		<hr style="margin:14px 0;border-color:#eee;">
		<div style="font-weight:bold;color:#1a5276;margin-bottom:8px;">
			<span class="icon-filter"></span> Lógica Condicional
		</div>
		<div class="enc-form-row">
			<label>¿Tiene condición?</label>
			<label>
				<input type="checkbox" id="fp_tiene_cond" name="tiene_condicion" value="1"
				<?php echo ($pregunta && $pregunta['cond_id_preg_ref']) ? 'checked' : ''; ?>>
				Mostrar esta pregunta solo si se cumple una condición
			</label>
		</div>
		<div id="campos-condicion" style="display:none;">
			<div class="enc-form-row">
				<label>Mostrar si respuesta a</label>
				<select name="cond_id_preg_ref" id="fp_cond_preg">
					<option value="">— Seleccionar pregunta —</option>
					<?php
					mysqli_data_seek($otras_preguntas, 0);
					while ($op = mysqli_fetch_array($otras_preguntas)):
						if (!in_array($op['tipo_pregunta'], [1, 2, 3])) continue;
						$sel = ($pregunta && $pregunta['cond_id_preg_ref'] == $op['id_pregunta']) ? 'selected' : '';
					?>
					<option value="<?php echo $op['id_pregunta']; ?>" <?php echo $sel; ?>>
						#<?php echo $op['nro_orden']; ?> — <?php echo htmlspecialchars(substr($op['texto_pregunta'], 0, 60)); ?>
					</option>
					<?php endwhile; ?>
				</select>
			</div>
			<div class="enc-form-row">
				<label>Operador</label>
				<select name="cond_operador" id="fp_cond_op">
					<option value="<"  <?php if ($pregunta && $pregunta['cond_operador']=='<')  echo 'selected'; ?>>es menor que</option>
					<option value="<=" <?php if ($pregunta && $pregunta['cond_operador']=='<=') echo 'selected'; ?>>es menor o igual que</option>
					<option value="="  <?php if ($pregunta && $pregunta['cond_operador']=='=')  echo 'selected'; ?>>es igual a</option>
					<option value=">=" <?php if ($pregunta && $pregunta['cond_operador']=='>=') echo 'selected'; ?>>es mayor o igual que</option>
					<option value=">"  <?php if ($pregunta && $pregunta['cond_operador']=='>') echo 'selected'; ?>>es mayor que</option>
					<option value="!=" <?php if ($pregunta && $pregunta['cond_operador']=='!=') echo 'selected'; ?>>es distinto de</option>
				</select>
			</div>
			<div class="enc-form-row">
				<label>Valor</label>
				<input type="text" name="cond_valor" id="fp_cond_val" maxlength="50"
				       value="<?php echo $pregunta ? htmlspecialchars($pregunta['cond_valor'] ?? '') : ''; ?>"
				       placeholder="ej: 7">
			</div>
			<p style="color:#888;font-size:10px;margin-top:4px;">
				Ejemplo: si la pregunta anterior es escala 1-10 y querés mostrar esto solo cuando la nota es &lt; 7, seleccioná esa pregunta, "es menor que" y valor "7".
			</p>
		</div>
	</div>

	<div class="enc-form-acciones" style="margin-top:18px;">
		<button type="submit" class="btn-enc btn-enc-verde">
			<span class="icon-check-square-o"></span> Guardar Pregunta
		</button>
		<a href="config_preguntas.php?id_encuesta=<?php echo $id_encuesta; ?>" class="btn-enc btn-enc-gris">
			<span class="icon-times"></span> Cancelar
		</a>
	</div>
</form>

</div><!-- /enc-form-panel -->
</div><!-- /enc-subpag-contenido -->
<div id="msg_respuesta" style="margin:10px 12px;font-size:12px;"></div>

<script>
var id_pregunta_actual = <?php echo $id_pregunta; ?>;
var id_encuesta_actual = <?php echo $id_encuesta; ?>;

function mostrarCargando()  { $("#enc_cargando").show(); }
function ocultarCargando()  { $("#enc_cargando").hide(); }

$(function(){

	// ── Cambio de tipo de pregunta ───────────────────────────
	function actualizarTipo() {
		var tipo = parseInt($("#fp_tipo").val());

		// Pondera
		if (tipo == 4 || tipo == 5) {
			$("#fp_pondera").prop("checked", false).prop("disabled", true);
		} else {
			$("#fp_pondera").prop("disabled", false);
		}
		// Observación
		if (tipo == 5) { $("#row-obs").show(); } else { $("#row-obs").hide(); }

		// Opciones
		if (tipo == 3 || tipo == 4) {
			$("#seccion-opciones").show();
			if (tipo == 3) {
				$("#label-tipo-opciones").text("(opciones para checkbox — marcar una o varias)");
			} else {
				$("#label-tipo-opciones").text("(sub-ítems donde el cliente responde Sí o No)");
			}
			if (id_pregunta_actual == 0) {
				$("#opciones-aviso").show();
				$("#lista-opciones, .enc-nueva-opcion").hide();
			} else {
				$("#opciones-aviso").hide();
				$("#lista-opciones, .enc-nueva-opcion").show();
			}
		} else {
			$("#seccion-opciones").hide();
		}
	}

	$("#fp_tipo").change(actualizarTipo);
	actualizarTipo();

	// ── Condición ────────────────────────────────────────────
	function actualizarCondicion() {
		if ($("#fp_tiene_cond").is(":checked")) {
			$("#campos-condicion").show();
		} else {
			$("#campos-condicion").hide();
			$("#fp_cond_preg").val("");
			$("#fp_cond_val").val("");
		}
	}
	$("#fp_tiene_cond").change(actualizarCondicion);
	actualizarCondicion();

	// ── Guardar pregunta ─────────────────────────────────────
	$("#frm_pregunta").submit(function(e){
		e.preventDefault();
		mostrarCargando();
		$.ajax({
			url: "config_pregunta_guardar.php",
			type: "POST",
			data: $(this).serialize(),
			success: function(resp){
				ocultarCargando();
				if (resp.indexOf("ok:") === 0) {
					// resp = "ok:ID"
					var nuevo_id = parseInt(resp.split(":")[1]);
					if (id_pregunta_actual == 0) {
						// Redirigir a edición para poder agregar opciones
						window.location = "config_pregunta_form.php?id_encuesta=" + id_encuesta_actual + "&id=" + nuevo_id;
					} else {
						window.location = "config_preguntas.php?id_encuesta=" + id_encuesta_actual;
					}
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

	// ── Agregar opción ───────────────────────────────────────
	$("#btn-agregar-opcion").click(function(){
		var texto = $.trim($("#nueva-opcion-texto").val());
		if (!texto) { swal("Atención", "Ingresá el texto de la opción.", "warning"); return; }
		mostrarCargando();
		$.post("config_opcion_guardar.php",
			{id_pregunta: id_pregunta_actual, texto_opcion: texto},
			function(resp){
				ocultarCargando();
				if (resp.indexOf("ok:") === 0) {
					var id_op = parseInt(resp.split(":")[1]);
					var html = '<div class="enc-opcion-item" id="opcion-'+id_op+'">' +
						'<span class="enc-opcion-texto">'+$("<div>").text(texto).html()+'</span>' +
						'<button type="button" class="btn-enc btn-enc-rojo btn-enc-sm btn-del-opcion" data-id="'+id_op+'">' +
						'<span class="icon-times"></span></button></div>';
					$("#lista-opciones").append(html);
					$("#nueva-opcion-texto").val("").focus();
				} else {
					swal("Error", resp, "error");
				}
			}
		);
	});

	// ── Enter en campo opción ────────────────────────────────
	$("#nueva-opcion-texto").keypress(function(e){
		if (e.which == 13) { e.preventDefault(); $("#btn-agregar-opcion").click(); }
	});

	// ── Eliminar opción ──────────────────────────────────────
	$(document).on("click", ".btn-del-opcion", function(){
		var id_op = $(this).data("id");
		var $item = $(this).closest(".enc-opcion-item");
		swal({
			title: "Eliminar opción",
			text: "¿Eliminar esta opción?",
			type: "warning",
			showCancelButton: true,
			confirmButtonText: "Sí",
			cancelButtonText: "No"
		}, function(){
			mostrarCargando();
			$.post("config_opcion_baja.php", {id_opcion: id_op}, function(r){
				ocultarCargando();
				if (r=="ok") $item.remove();
				else swal("Error", r, "error");
			});
		});
	});

});
</script>
</body>
</html>
