<?php
// ── Tab: Configuración de Encuestas ───────────────────────
$SQL = "SELECT * FROM enc_encuestas WHERE baja = 0 ORDER BY fecha_creacion DESC";
$encuestas = mysqli_query($con, $SQL);
?>

<div class="enc-sec-header">
	<span class="enc-sec-titulo"><span class="icon-cogs"></span> Encuestas</span>
	<a class="btn-enc btn-enc-gris btn-enc-sm" href="config_areas.php" style="margin-left:auto;">
		<span class="icon-tags"></span> Áreas
	</a>
	<button class="btn-enc btn-enc-azul" id="btn-nueva-encuesta" style="margin-left:8px;">
		<span class="icon-plus"></span> Nueva Encuesta
	</button>
</div>

<!-- FORMULARIO INLINE (oculto por defecto) -->
<div class="enc-form-panel" id="panel-form-encuesta" style="display:none;">
	<h3 id="form-enc-titulo">Nueva Encuesta</h3>
	<form id="frm_encuesta">
		<input type="hidden" id="fenc_id" name="id_encuesta" value="0">
		<div class="enc-form-row">
			<label for="fenc_nombre">Nombre *</label>
			<input type="text" id="fenc_nombre" name="nombre" maxlength="200" required>
		</div>
		<div class="enc-form-row">
			<label for="fenc_descripcion">Descripción</label>
			<textarea id="fenc_descripcion" name="descripcion" rows="2"></textarea>
		</div>
		<div class="enc-form-row">
			<label for="fenc_bienvenida">Mensaje de bienvenida</label>
			<textarea id="fenc_bienvenida" name="mensaje_bienvenida" rows="3"
				placeholder="Texto que verá el cliente antes de responder la primera pregunta..."></textarea>
		</div>
		<div class="enc-form-acciones">
			<button type="submit" class="btn-enc btn-enc-verde">
				<span class="icon-check-square-o"></span> Guardar
			</button>
			<button type="button" class="btn-enc btn-enc-gris" id="btn-cancelar-form-enc">
				<span class="icon-times"></span> Cancelar
			</button>
		</div>
	</form>
</div>

<!-- TABLA DE ENCUESTAS -->
<table class="enc-tabla" id="tabla-encuestas">
	<thead>
		<tr>
			<td width="5%">#</td>
			<td>Nombre</td>
			<td>Descripción</td>
			<td width="10%">Estado</td>
			<td width="22%"></td>
		</tr>
	</thead>
	<tbody id="tbody-encuestas">
	<?php
	$filas = 0;
	while ($enc = mysqli_fetch_array($encuestas)):
		$filas++;
	?>
		<tr id="enc-fila-<?php echo $enc['id_encuesta']; ?>">
			<td style="text-align:center;"><?php echo $enc['id_encuesta']; ?></td>
			<td><strong><?php echo htmlspecialchars($enc['nombre']); ?></strong></td>
			<td style="color:#666;"><?php echo htmlspecialchars($enc['descripcion'] ?? ''); ?></td>
			<td style="text-align:center;">
				<?php if ($enc['activa']): ?>
					<span class="badge-enc badge-enc-activa">Activa</span>
				<?php else: ?>
					<span class="badge-enc badge-enc-inactiva">Inactiva</span>
				<?php endif; ?>
			</td>
			<td class="celda-acciones">
				<?php if (!$enc['activa']): ?>
				<button class="btn-enc btn-enc-verde btn-enc-sm btn-activar"
				        data-id="<?php echo $enc['id_encuesta']; ?>"
				        title="Activar esta encuesta">
					<span class="icon-check-square-o"></span> Activar
				</button>
				<?php endif; ?>
				<a class="btn-enc btn-enc-azul btn-enc-sm"
				   href="config_preguntas.php?id_encuesta=<?php echo $enc['id_encuesta']; ?>">
					<span class="icon-tools"></span> Preguntas
				</a>
				<button class="btn-enc btn-enc-gris btn-enc-sm btn-editar-enc"
				        data-id="<?php echo $enc['id_encuesta']; ?>"
				        data-nombre="<?php echo htmlspecialchars($enc['nombre'], ENT_QUOTES); ?>"
				        data-descripcion="<?php echo htmlspecialchars($enc['descripcion'] ?? '', ENT_QUOTES); ?>"
				        data-bienvenida="<?php echo htmlspecialchars($enc['mensaje_bienvenida'] ?? '', ENT_QUOTES); ?>">
					<span class="icon-tools"></span> Editar
				</button>
				<?php if (!$enc['activa']): ?>
				<button class="btn-enc btn-enc-rojo btn-enc-sm btn-baja-enc"
				        data-id="<?php echo $enc['id_encuesta']; ?>"
				        title="Dar de baja">
					<span class="icon-times"></span>
				</button>
				<?php endif; ?>
			</td>
		</tr>
	<?php endwhile; ?>
	<?php if ($filas === 0): ?>
		<tr><td colspan="5" style="text-align:center;padding:20px;color:#888;">No hay encuestas creadas.</td></tr>
	<?php endif; ?>
	</tbody>
</table>

<script>
$(function(){

	// ── Mostrar / ocultar formulario ────────────────────────
	$("#btn-nueva-encuesta").click(function(){
		$("#panel-form-encuesta").show();
		$("#form-enc-titulo").text("Nueva Encuesta");
		$("#fenc_id").val(0);
		$("#frm_encuesta")[0].reset();
		$("html, body").animate({scrollTop: 0}, 200);
	});

	$("#btn-cancelar-form-enc").click(function(){
		$("#panel-form-encuesta").hide();
	});

	// ── Cargar datos para editar ────────────────────────────
	$(document).on("click", ".btn-editar-enc", function(){
		var id   = $(this).data("id");
		var nom  = $(this).data("nombre");
		var desc = $(this).data("descripcion");
		var bien = $(this).data("bienvenida");
		$("#panel-form-encuesta").show();
		$("#form-enc-titulo").text("Editar Encuesta");
		$("#fenc_id").val(id);
		$("#fenc_nombre").val(nom);
		$("#fenc_descripcion").val(desc);
		$("#fenc_bienvenida").val(bien);
		$("html, body").animate({scrollTop: 0}, 200);
	});

	// ── Guardar encuesta (nueva / editar) ───────────────────
	$("#frm_encuesta").submit(function(e){
		e.preventDefault();
		mostrarCargando();
		$.ajax({
			url: "config_encuesta_guardar.php",
			type: "POST",
			data: $(this).serialize(),
			success: function(resp){
				ocultarCargando();
				if (resp == "ok") {
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

	// ── Activar encuesta ────────────────────────────────────
	$(document).on("click", ".btn-activar", function(){
		var id = $(this).data("id");
		swal({
			title: "Activar encuesta",
			text: "Al activar esta encuesta se desactivarán las demás. ¿Continuar?",
			type: "warning",
			showCancelButton: true,
			confirmButtonText: "Sí, activar",
			cancelButtonText: "Cancelar"
		}, function(){
			mostrarCargando();
			$.post("config_encuesta_activar.php", {id_encuesta: id}, function(resp){
				ocultarCargando();
				if (resp == "ok") { location.reload(); }
				else { swal("Error", resp, "error"); }
			});
		});
	});

	// ── Dar de baja encuesta ────────────────────────────────
	$(document).on("click", ".btn-baja-enc", function(){
		var id = $(this).data("id");
		swal({
			title: "Dar de baja",
			text: "¿Eliminar esta encuesta? Solo se pueden eliminar encuestas inactivas.",
			type: "warning",
			showCancelButton: true,
			confirmButtonText: "Sí, eliminar",
			cancelButtonText: "Cancelar"
		}, function(){
			mostrarCargando();
			$.post("config_encuesta_baja.php", {id_encuesta: id}, function(resp){
				ocultarCargando();
				if (resp == "ok") { location.reload(); }
				else { swal("Error", resp, "error"); }
			});
		});
	});

});
</script>
