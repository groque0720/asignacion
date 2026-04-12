<?php
// Encuesta pública — sin autenticación requerida
include_once("../funciones/func_mysql.php");
conectar();

$token = isset($_GET['t']) ? trim($_GET['t']) : '';
if ($token === '') {
	header("Location: expirada.php"); exit();
}

$token_esc = mysqli_real_escape_string($con, $token);
$SQL_tok = "SELECT t.*, e.nombre AS enc_nombre, e.mensaje_bienvenida,
					a.cliente, a.fec_entrega, g.grupo
			FROM enc_tokens t
			JOIN enc_encuestas e  ON t.id_encuesta   = e.id_encuesta
			JOIN asignaciones  a  ON t.id_asignacion = a.id_unidad
			LEFT JOIN grupos   g  ON a.id_grupo      = g.idgrupo
			WHERE t.token = '$token_esc'
			LIMIT 1";
$res_tok = mysqli_query($con, $SQL_tok);

if (mysqli_num_rows($res_tok) == 0) {
	header("Location: expirada.php"); exit();
}
$tok_data = mysqli_fetch_array($res_tok);

if ($tok_data['completada'] == 1) {
	header("Location: expirada.php?tipo=completada"); exit();
}

$id_encuesta = (int)$tok_data['id_encuesta'];
$id_token    = (int)$tok_data['id_token'];

// Cargar preguntas activas ordenadas
$SQL_preg = "SELECT * FROM enc_preguntas
			 WHERE id_encuesta = $id_encuesta AND baja = 0
			 ORDER BY nro_orden ASC";
$res_preg = mysqli_query($con, $SQL_preg);
$preguntas = [];
while ($p = mysqli_fetch_array($res_preg)) {
	// Cargar opciones si aplica
	$p['opciones'] = [];
	if (in_array($p['tipo_pregunta'], [3, 4])) {
		$res_op = mysqli_query($con, "SELECT * FROM enc_opciones WHERE id_pregunta = {$p['id_pregunta']} AND baja = 0 ORDER BY nro_orden ASC");
		while ($op = mysqli_fetch_array($res_op)) {
			$p['opciones'][] = $op;
		}
	}
	$preguntas[] = $p;
}

if (empty($preguntas)) {
	// Encuesta sin preguntas
	header("Location: expirada.php?tipo=sin_preguntas"); exit();
}

$total_preg      = count($preguntas);
$tiene_bienvenida = !empty(trim($tok_data['mensaje_bienvenida'] ?? ''));
$tipo_labels = [
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
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<title><?php echo htmlspecialchars($tok_data['enc_nombre']); ?></title>
	<link rel="shortcut icon" type="image/x-icon" href="../../asignacion/imagenes/favicon.ico" />
	<link rel="stylesheet" href="../css/encuesta_publica.css">
	<script src="../js/jquery-2.1.3.min.js"></script>
</head>
<body>

<!-- BARRA DE PROGRESO -->
<div class="enc-progreso-wrap">
	<div class="enc-progreso-fill" id="barra-progreso" style="width:0%"></div>
</div>
<div class="enc-progreso-contador" id="contador-texto">
	<?php echo $tiene_bienvenida ? '' : '1 / '.$total_preg; ?>
</div>

<!-- FORMULARIO OCULTO para envío -->
<form id="frm-encuesta" action="../ajax/respuesta_guardar.php" method="POST" style="display:none;">
	<input type="hidden" name="token"       value="<?php echo htmlspecialchars($token); ?>">
	<input type="hidden" name="id_token"    value="<?php echo $id_token; ?>">
	<input type="hidden" name="id_encuesta" value="<?php echo $id_encuesta; ?>">
	<input id="campo-respuestas-json" type="hidden" name="respuestas_json" value="">
</form>

<!-- CONTENIDO PRINCIPAL -->
<div class="enc-pub-contenedor">

	<?php if ($tiene_bienvenida): ?>
	<!-- SLIDE 0: BIENVENIDA -->
	<div class="enc-slide activo" id="slide-bienvenida">
		<div class="enc-bienvenida">
			<div class="enc-bienvenida-icono">
			<img src="../asignacion/imagenes/logodyv_c.png" alt="Derka y Vargas S.A.">
		</div>
			<h2><?php echo htmlspecialchars($tok_data['enc_nombre']); ?></h2>
			<p><?php echo nl2br(htmlspecialchars($tok_data['mensaje_bienvenida'])); ?></p>
			<p style="margin-top:12px;font-size:12px;color:#888;">
				<?php echo htmlspecialchars($tok_data['cliente']); ?> ·
				<?php echo htmlspecialchars($tok_data['grupo']); ?>
			</p>
		</div>
	</div>
	<?php endif; ?>

	<!-- SLIDES DE PREGUNTAS -->
	<?php foreach ($preguntas as $idx => $p):
		$nro    = $idx + 1;
		$id_p   = $p['id_pregunta'];
		$tipo   = (int)$p['tipo_pregunta'];
		$cond   = $p['cond_id_preg_ref'] ? json_encode([
			'ref'  => (int)$p['cond_id_preg_ref'],
			'op'   => $p['cond_operador'],
			'val'  => $p['cond_valor'],
		]) : 'null';
	?>
	<div class="enc-slide"
	     id="slide-<?php echo $id_p; ?>"
	     data-id="<?php echo $id_p; ?>"
	     data-nro="<?php echo $nro; ?>"
	     data-tipo="<?php echo $tipo; ?>"
	     data-pondera="<?php echo $p['pondera']; ?>"
	     data-condicion='<?php echo $cond; ?>'>

		<div class="enc-pregunta-card">
			<div class="enc-pregunta-nro">Pregunta <?php echo $nro; ?> de <?php echo $total_preg; ?></div>
			<div class="enc-pregunta-texto"><?php echo htmlspecialchars($p['texto_pregunta']); ?></div>

			<?php if ($tipo == 1): ?>
			<!-- ── Escala 1-10 ─────────────────────────────── -->
			<div class="enc-escala" id="esc-<?php echo $id_p; ?>">
				<?php for ($v = 1; $v <= 10; $v++): ?>
				<button type="button" class="enc-escala-btn" data-id="<?php echo $id_p; ?>" data-val="<?php echo $v; ?>">
					<?php echo $v; ?>
				</button>
				<?php endfor; ?>
			</div>
			<div class="enc-escala-labels">
				<span>Muy malo</span><span>Excelente</span>
			</div>

			<?php elseif ($tipo == 2): ?>
			<!-- ── Sí / No simple ──────────────────────────── -->
			<div class="enc-sino-wrap" id="sino-<?php echo $id_p; ?>">
				<button type="button" class="enc-sino-btn" data-id="<?php echo $id_p; ?>" data-val="1">
					Sí <span class="enc-sino-label">de acuerdo</span>
				</button>
				<button type="button" class="enc-sino-btn" data-id="<?php echo $id_p; ?>" data-val="0">
					No <span class="enc-sino-label">en desacuerdo</span>
				</button>
			</div>

			<?php elseif ($tipo == 3): ?>
			<!-- ── Selección múltiple ──────────────────────── -->
			<div class="enc-multiple-lista" id="multi-<?php echo $id_p; ?>">
				<?php foreach ($p['opciones'] as $op): ?>
				<div class="enc-multiple-item" data-id="<?php echo $id_p; ?>" data-opcion="<?php echo $op['id_opcion']; ?>">
					<div class="enc-multiple-check">✓</div>
					<span class="enc-multiple-label"><?php echo htmlspecialchars($op['texto_opcion']); ?></span>
				</div>
				<?php endforeach; ?>
			</div>
			<?php if (empty($p['opciones'])): ?>
				<p style="color:#aaa;font-style:italic;font-size:13px;">Sin opciones configuradas.</p>
			<?php endif; ?>

			<?php elseif ($tipo == 4): ?>
			<!-- ── Lista Sí/No ─────────────────────────────── -->
			<div class="enc-listasino" id="lsino-<?php echo $id_p; ?>">
				<?php foreach ($p['opciones'] as $op): ?>
				<div class="enc-listasino-item">
					<span class="enc-listasino-label"><?php echo htmlspecialchars($op['texto_opcion']); ?></span>
					<div class="enc-listasino-btns">
						<button type="button" class="enc-listasino-btn si"
						        data-id="<?php echo $id_p; ?>" data-opcion="<?php echo $op['id_opcion']; ?>" data-val="1">Sí</button>
						<button type="button" class="enc-listasino-btn no"
						        data-id="<?php echo $id_p; ?>" data-opcion="<?php echo $op['id_opcion']; ?>" data-val="0">No</button>
					</div>
				</div>
				<?php endforeach; ?>
			</div>

			<?php else: ?>
			<!-- ── Texto libre (tipo 5) ────────────────────── -->
			<textarea class="enc-textarea" id="txt-<?php echo $id_p; ?>"
			          placeholder="Escribí tu comentario aquí..." rows="4"
			          data-id="<?php echo $id_p; ?>"></textarea>

			<?php endif; ?>

			<div class="enc-error-msg" id="err-<?php echo $id_p; ?>"></div>
		</div>
	</div>
	<?php endforeach; ?>

</div><!-- /enc-pub-contenedor -->

<!-- NAVEGACIÓN INFERIOR -->
<div class="enc-pub-nav">
	<button class="enc-btn-nav enc-btn-anterior" id="btn-anterior" disabled>← Anterior</button>
	<button class="enc-btn-nav enc-btn-siguiente" id="btn-siguiente">Siguiente →</button>
</div>

<script>
// ── Datos de preguntas para lógica condicional ─────────────
var preguntas = <?php
	$preg_js = [];
	foreach ($preguntas as $p) {
		$preg_js[] = [
			'id'         => (int)$p['id_pregunta'],
			'tipo'       => (int)$p['tipo_pregunta'],
			'pondera'    => (int)$p['pondera'],
			'condicion'  => $p['cond_id_preg_ref'] ? [
				'ref' => (int)$p['cond_id_preg_ref'],
				'op'  => $p['cond_operador'],
				'val' => $p['cond_valor'],
			] : null,
			'cant_opciones' => count($p['opciones']),
		];
	}
	echo json_encode($preg_js);
?>;

var tiene_bienvenida = <?php echo $tiene_bienvenida ? 'true' : 'false'; ?>;
var total_preg = <?php echo $total_preg; ?>;

// Estado de respuestas: {id_pregunta: valor}
// Para tipo 3: {id_pregunta: {opciones: [id_op1, id_op2, ...]}}
// Para tipo 4: {id_pregunta: {opciones: [{id: X, val: 1/0}, ...]}}
var respuestas = {};

// Índice actual: -1 = bienvenida, 0..N-1 = preguntas
var idx_actual = tiene_bienvenida ? -1 : 0;

// ── Evaluar condición de una pregunta ──────────────────────
function evaluarCondicion(preg) {
	if (!preg.condicion) return true;
	var cond = preg.condicion;
	var ref_id = cond.ref;
	var op     = cond.op;
	var val    = parseFloat(cond.val);

	if (!(ref_id in respuestas)) return false; // referencia sin respuesta = no mostrar
	var r = respuestas[ref_id];
	var rv;
	if (typeof r === 'object' && r.valor !== undefined) {
		rv = parseFloat(r.valor);
	} else {
		rv = parseFloat(r);
	}
	if (isNaN(rv)) return false;
	switch(op) {
		case '<':  return rv <  val;
		case '<=': return rv <= val;
		case '=':  return rv == val;
		case '>=': return rv >= val;
		case '>':  return rv >  val;
		case '!=': return rv != val;
	}
	return true;
}

// ── Calcular lista de índices visibles ─────────────────────
function indicesVisibles() {
	var vis = [];
	for (var i = 0; i < preguntas.length; i++) {
		if (evaluarCondicion(preguntas[i])) vis.push(i);
	}
	return vis;
}

// ── Actualizar barra de progreso y contador ────────────────
function actualizarProgreso() {
	var vis = indicesVisibles();
	var pos_visible = vis.indexOf(idx_actual);
	var total_visible = vis.length;
	var pct, texto;
	if (idx_actual == -1) {
		pct = 0;
		texto = '';
	} else {
		pct  = Math.round((pos_visible + 1) / total_visible * 100);
		texto = (pos_visible + 1) + ' / ' + total_visible;
	}
	$("#barra-progreso").css("width", pct + "%");
	$("#contador-texto").text(texto);
}

// ── Mostrar slide por índice ───────────────────────────────
function mostrarSlide(new_idx) {
	$(".enc-slide").removeClass("activo");
	if (new_idx == -1) {
		$("#slide-bienvenida").addClass("activo");
		$("#btn-anterior").prop("disabled", true);
		$("#btn-siguiente").text("Comenzar →").removeClass("enc-btn-finalizar").addClass("enc-btn-siguiente");
	} else {
		var preg = preguntas[new_idx];
		$("#slide-" + preg.id).addClass("activo");

		// Anterior
		var vis = indicesVisibles();
		var pos_visible = vis.indexOf(new_idx);
		$("#btn-anterior").prop("disabled", pos_visible == 0 && !tiene_bienvenida);

		// Siguiente / Finalizar
		var ultimo = (pos_visible == vis.length - 1);
		if (ultimo) {
			$("#btn-siguiente").text("Finalizar ✓").removeClass("enc-btn-siguiente").addClass("enc-btn-finalizar");
		} else {
			$("#btn-siguiente").text("Siguiente →").removeClass("enc-btn-finalizar").addClass("enc-btn-siguiente");
		}
	}
	actualizarProgreso();
}

// ── Obtener respuesta del slide actual ─────────────────────
function obtenerRespuesta(idx) {
	if (idx < 0) return {ok: true}; // bienvenida, sin respuesta
	var preg = preguntas[idx];
	var id   = preg.id;
	var tipo = preg.tipo;
	var err  = "";

	if (tipo == 1) {
		var sel = $("#esc-" + id + " .enc-escala-btn.elegido");
		if (sel.length == 0) { err = "Seleccioná una opción."; }
		else { respuestas[id] = {tipo: 1, valor: parseFloat(sel.data("val"))}; }

	} else if (tipo == 2) {
		var sel = $("#sino-" + id + " .enc-sino-btn.elegido");
		if (sel.length == 0) { err = "Seleccioná Sí o No."; }
		else { respuestas[id] = {tipo: 2, valor: parseFloat(sel.data("val"))}; }

	} else if (tipo == 3) {
		var ops = [];
		$("#multi-" + id + " .enc-multiple-item.elegido").each(function(){
			ops.push(parseInt($(this).data("opcion")));
		});
		// Múltiple no es obligatoria (puede ir sin marcar)
		respuestas[id] = {tipo: 3, opciones: ops, total: preg.cant_opciones};

	} else if (tipo == 4) {
		var ops = [];
		var falta = false;
		$("#lsino-" + id + " .enc-listasino-item").each(function(){
			var si_btn = $(this).find(".enc-listasino-btn.si");
			var no_btn = $(this).find(".enc-listasino-btn.no");
			var id_op  = si_btn.data("opcion");
			if (si_btn.hasClass("elegido")) { ops.push({id: id_op, val: 1}); }
			else if (no_btn.hasClass("elegido")) { ops.push({id: id_op, val: 0}); }
			else { falta = true; }
		});
		if (falta) { err = "Respondé todas las opciones."; }
		else { respuestas[id] = {tipo: 4, opciones: ops}; }

	} else if (tipo == 5) {
		var txt = $.trim($("#txt-" + id).val());
		respuestas[id] = {tipo: 5, texto: txt};
	}

	if (err) {
		$("#err-" + id).text(err);
		return {ok: false};
	}
	$("#err-" + id).text("");
	return {ok: true};
}

// ── Siguiente ──────────────────────────────────────────────
$("#btn-siguiente").click(function(){
	// Validar actual
	var result = obtenerRespuesta(idx_actual);
	if (!result.ok) return;

	var vis = indicesVisibles();

	if (idx_actual == -1) {
		// Desde bienvenida → primera pregunta visible
		if (vis.length > 0) { idx_actual = vis[0]; mostrarSlide(idx_actual); }
		return;
	}

	var pos = vis.indexOf(idx_actual);
	if (pos == vis.length - 1) {
		// FINALIZAR
		enviarEncuesta();
	} else {
		idx_actual = vis[pos + 1];
		mostrarSlide(idx_actual);
	}
});

// ── Anterior ───────────────────────────────────────────────
$("#btn-anterior").click(function(){
	if (idx_actual == -1) return;
	var vis = indicesVisibles();
	var pos = vis.indexOf(idx_actual);
	if (pos == 0) {
		if (tiene_bienvenida) { idx_actual = -1; mostrarSlide(-1); }
	} else {
		idx_actual = vis[pos - 1];
		mostrarSlide(idx_actual);
	}
});

// ── Escala: selección ──────────────────────────────────────
$(document).on("click", ".enc-escala-btn", function(){
	var id = $(this).data("id");
	$("#esc-"+id+" .enc-escala-btn").removeClass("elegido");
	$(this).addClass("elegido");
	$("#err-"+id).text("");
});

// ── Sí/No: selección ───────────────────────────────────────
$(document).on("click", ".enc-sino-btn", function(){
	var id = $(this).data("id");
	$("#sino-"+id+" .enc-sino-btn").removeClass("elegido");
	$(this).addClass("elegido");
	$("#err-"+id).text("");
});

// ── Múltiple: toggle ───────────────────────────────────────
$(document).on("click", ".enc-multiple-item", function(){
	$(this).toggleClass("elegido");
	var id = $(this).data("id");
	$("#err-"+id).text("");
});

// ── Lista si/no ────────────────────────────────────────────
$(document).on("click", ".enc-listasino-btn", function(){
	var id_op = $(this).data("opcion");
	$(this).closest(".enc-listasino-item").find(".enc-listasino-btn").removeClass("elegido");
	$(this).addClass("elegido");
	var id = $(this).data("id");
	$("#err-"+id).text("");
});

// ── Enviar encuesta ────────────────────────────────────────
function enviarEncuesta() {
	// Marcar preguntas no mostradas (omitidas por condición)
	var vis = indicesVisibles();
	for (var i = 0; i < preguntas.length; i++) {
		var preg = preguntas[i];
		if (vis.indexOf(i) == -1 && !(preg.id in respuestas)) {
			respuestas[preg.id] = {tipo: preg.tipo, omitida: true};
		}
	}
	$("#campo-respuestas-json").val(JSON.stringify(respuestas));
	$("#frm-encuesta").submit();
}

// ── Inicialización ─────────────────────────────────────────
$(function(){
	mostrarSlide(idx_actual);
	// Enter en textarea no debe enviar
	$(".enc-textarea").keydown(function(e){
		if (e.which == 13 && !e.shiftKey) e.stopPropagation();
	});
});
</script>

</body>
</html>
