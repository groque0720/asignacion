<?php
@session_start();
if ($_SESSION["autentificado"] != "SI") {
	header("Location: ../login"); exit();
}
include_once("config.php");
if (!in_array($_SESSION["idperfil"], ENCUESTA_PERFILES)) {
	header("Location: ../login"); exit();
}
include_once("funciones/func_mysql.php");
conectar();

$id_unidad = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id_unidad <= 0) {
	header("Location: index.php?sec=entregas"); exit();
}

// Traer datos de la entrega
$SQL = "SELECT a.*, u.nombre AS asesor, g.grupo AS grupo, m.modelo AS modelo, s.sucursal AS sucursal
		FROM asignaciones a
		JOIN  usuarios u   ON a.id_asesor   = u.idusuario
		LEFT JOIN grupos g   ON a.id_grupo  = g.idgrupo
		LEFT JOIN modelos m  ON a.id_modelo = m.idmodelo
		LEFT JOIN sucursales s ON a.id_sucursal = s.idsucursal
		WHERE a.id_unidad = $id_unidad
		  AND a.entregada = 1
		  AND a.borrar    = 0
		  AND a.guardado  = 1
		LIMIT 1";
$res  = mysqli_query($con, $SQL);
$unidad = mysqli_fetch_array($res);
if (!$unidad) {
	header("Location: index.php?sec=entregas"); exit();
}

// Verificar que hay una encuesta activa
$SQL_enc = "SELECT id_encuesta, nombre FROM enc_encuestas WHERE activa = 1 AND baja = 0 LIMIT 1";
$res_enc  = mysqli_query($con, $SQL_enc);
$encuesta_activa = mysqli_fetch_array($res_enc);

// Buscar token existente
$token_data  = null;
$link_encuesta = null;
$SQL_tok = "SELECT * FROM enc_tokens WHERE id_asignacion = $id_unidad LIMIT 1";
$res_tok = mysqli_query($con, $SQL_tok);
if (mysqli_num_rows($res_tok) > 0) {
	$token_data = mysqli_fetch_array($res_tok);
	$link_encuesta = BASE_URL_ENCUESTA . '?t=' . $token_data['token'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Encuesta — <?php echo htmlspecialchars($unidad['cliente']); ?></title>
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

<!-- CABECERA SIMPLIFICADA -->
<div class="enc-subpag-cabecera">
	<a class="btn-enc btn-enc-gris btn-enc-sm" href="index.php?sec=entregas">
		← Volver a Entregas
	</a>
	<span class="enc-titulo">Página de Encuesta</span>
	<span class="enc-usuario" style="margin-left:auto;">
		<span class="icon-user"></span> <?php echo htmlspecialchars($_SESSION["usuario"]); ?>
	</span>
</div>

<div class="enc-subpag-contenido">
<div class="enc-puente-card">

	<h2><span class="icon-user"></span> <?php echo htmlspecialchars($unidad['cliente']); ?></h2>

	<div class="enc-dato-row">
		<span class="enc-dato-label">Fecha de entrega</span>
		<span class="enc-dato-valor"><?php echo fechaLarga($unidad['fec_entrega']); ?></span>
	</div>
	<div class="enc-dato-row">
		<span class="enc-dato-label">Vehículo</span>
		<span class="enc-dato-valor">
			<?php echo htmlspecialchars($unidad['grupo']); ?>
			<?php if ($unidad['modelo']) echo ' — ' . htmlspecialchars($unidad['modelo']); ?>
		</span>
	</div>
	<?php if ($unidad['chasis']): ?>
	<div class="enc-dato-row">
		<span class="enc-dato-label">Chasis</span>
		<span class="enc-dato-valor"><?php echo htmlspecialchars($unidad['chasis']); ?></span>
	</div>
	<?php endif; ?>
	<div class="enc-dato-row">
		<span class="enc-dato-label">Asesor</span>
		<span class="enc-dato-valor"><?php echo htmlspecialchars($unidad['asesor']); ?></span>
	</div>
	<div class="enc-dato-row">
		<span class="enc-dato-label">Sucursal</span>
		<span class="enc-dato-valor"><?php echo htmlspecialchars($unidad['sucursal']); ?></span>
	</div>
	<div class="enc-dato-row">
		<span class="enc-dato-label">Estado encuesta</span>
		<span class="enc-dato-valor">
			<?php
			$estados = [0 => 'Sin generar', 1 => 'Pendiente', 2 => 'Completada'];
			$clases  = [0 => 'badge-enc-sin', 1 => 'badge-enc-pendiente', 2 => 'badge-enc-completa'];
			$est = $unidad['con_encuesta'];
			echo '<span class="badge-enc '.$clases[$est].'">'.$estados[$est].'</span>';
			?>
		</span>
	</div>

	<?php if (!$encuesta_activa): ?>
	<!-- Sin encuesta activa -->
	<div style="background:#fef9e7;border:1px solid #f9ca24;border-radius:6px;padding:12px;margin-top:16px;color:#856404;">
		<strong>Atención:</strong> No hay ninguna encuesta activa.
		Configurá una encuesta desde la sección <a href="index.php?sec=config" style="color:#1a5276;">Configurar Encuesta</a>.
	</div>

	<?php elseif ($unidad['con_encuesta'] == 2): ?>
	<!-- Ya completada -->
	<div style="background:#eafaf1;border:1px solid #82e0aa;border-radius:6px;padding:12px;margin-top:16px;color:#1e8449;">
		<strong>Encuesta completada.</strong> El cliente ya respondió esta encuesta.
		<a href="index.php?sec=resultados" style="color:#1a5276;">Ver resultados</a>.
	</div>

	<?php else: ?>
	<!-- Tiene encuesta activa y no completada -->
	<div id="zona-token">
		<?php if ($token_data): ?>
			<!-- Token ya existe -->
			<div class="enc-link-box" id="texto-link"><?php echo $link_encuesta; ?></div>

			<div class="enc-acciones-puente">
				<button class="btn-enc btn-enc-azul" id="btn-copiar-link" data-link="<?php echo htmlspecialchars($link_encuesta, ENT_QUOTES); ?>">
					<span class="icon-download-1"></span> Copiar link
				</button>
				<a class="btn-enc btn-enc-verde" href="<?php echo htmlspecialchars($link_encuesta, ENT_QUOTES); ?>" target="_blank">
					<span class="icon-search"></span> Abrir encuesta
				</a>
			</div>

			<div class="enc-qr-container" id="qr-container">
				<p style="color:#888;font-size:11px;margin-bottom:8px;">Escaneá para completar</p>
				<?php
				$qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=260x260&data=" . urlencode($link_encuesta) . "&color=1a5276";
				?>
				<img src="<?php echo $qr_url; ?>" alt="QR Encuesta"
				     onerror="this.style.display='none';document.getElementById('qr-error').style.display='block'">
				<div id="qr-error" style="display:none;color:#888;font-size:11px;margin-top:6px;">
					QR no disponible (verificar conexión a internet).
				</div>
				<br>
				<a class="btn-enc btn-enc-gris btn-enc-sm" style="margin-top:8px;display:inline-block;"
				   href="<?php echo $qr_url; ?>" download="qr_encuesta_<?php echo $id_unidad; ?>.png" target="_blank">
					<span class="icon-download"></span> Descargar QR
				</a>
			</div>

		<?php else: ?>
			<!-- Generar token -->
			<div id="zona-sin-token">
				<p style="color:#888;margin-top:16px;font-size:12px;">
					<span class="icon-bell"></span>
					Encuesta activa: <strong><?php echo htmlspecialchars($encuesta_activa['nombre']); ?></strong>
				</p>
				<p style="color:#888;font-size:12px;">No se generó el link todavía. Cargando...</p>
			</div>
			<div id="zona-con-token" style="display:none;">
				<div class="enc-link-box" id="texto-link"></div>
				<div class="enc-acciones-puente">
					<button class="btn-enc btn-enc-azul" id="btn-copiar-link" data-link="">
						<span class="icon-download-1"></span> Copiar link
					</button>
					<a class="btn-enc btn-enc-verde" href="#" id="btn-abrir-encuesta" target="_blank">
						<span class="icon-search"></span> Abrir encuesta
					</a>
				</div>
				<div class="enc-qr-container" id="qr-container" style="display:none;">
					<p style="color:#888;font-size:11px;margin-bottom:8px;">Escaneá para completar</p>
					<img id="qr-img" src="" alt="QR Encuesta">
				</div>
			</div>
		<?php endif; ?>
	</div>
	<?php endif; ?>

</div><!-- /enc-puente-card -->
</div><!-- /enc-subpag-contenido -->

<div id="msg_respuesta" style="margin:10px 0 0 12px;font-size:12px;"></div>

<script>
function mostrarCargando()  { $("#enc_cargando").show(); }
function ocultarCargando()  { $("#enc_cargando").hide(); }

$(function(){

	// ── Copiar link al portapapeles ─────────────────────────
	$(document).on("click", "#btn-copiar-link", function(){
		var link = $(this).data("link");
		if (navigator.clipboard) {
			navigator.clipboard.writeText(link).then(function(){
				swal("Copiado", "El link fue copiado al portapapeles.", "success");
			});
		} else {
			// Fallback para browsers sin clipboard API
			var ta = $("<textarea>").val(link).appendTo("body").select();
			document.execCommand("copy");
			ta.remove();
			swal("Copiado", "El link fue copiado al portapapeles.", "success");
		}
	});

	<?php if (!$token_data && $encuesta_activa && $unidad['con_encuesta'] != 2): ?>
	// ── Auto-generar token al cargar ────────────────────────
	mostrarCargando();
	$.post("puente_generar_token.php",
		{id_asignacion: <?php echo $id_unidad; ?>},
		function(resp){
			ocultarCargando();
			var data = $.parseJSON(resp);
			if (data.ok) {
				var link = data.link;
				var qr   = "https://api.qrserver.com/v1/create-qr-code/?size=260x260&data=" + encodeURIComponent(link) + "&color=1a5276";
				$("#texto-link").text(link);
				$("#btn-copiar-link").data("link", link);
				$("#btn-abrir-encuesta").attr("href", link);
				$("#qr-img").attr("src", qr);
				$("#zona-sin-token").hide();
				$("#zona-con-token").show();
				$("#qr-container").show();
			} else {
				$("#zona-sin-token").html('<p style="color:#c0392b;">'+data.msg+'</p>');
			}
		}
	);
	<?php endif; ?>

});
</script>

</body>
</html>
