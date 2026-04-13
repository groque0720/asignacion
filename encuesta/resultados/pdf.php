<?php
@session_start();
if ($_SESSION["autentificado"] != "SI") { header("Location: ../../login"); exit(); }
include_once("../config.php");
if (!in_array($_SESSION["idperfil"], ENCUESTA_PERFILES)) { header("Location: ../../login"); exit(); }

require('../../asignacion/fpdf/fpdf.php');
include_once("../funciones/func_mysql.php");
conectar();

$id_respuesta = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id_respuesta <= 0) { header("Location: index.php?sec=resultados"); exit(); }

// ── Datos de cabecera ──────────────────────────────────────
$SQL_cab = "SELECT r.*,
					a.cliente, a.fec_entrega, a.chasis, a.nro_orden,
					u.nombre AS asesor,
					g.grupo AS grupo,
					m.modelo AS modelo,
					s.sucursal AS sucursal,
					e.nombre AS enc_nombre
			FROM enc_respuestas r
			JOIN enc_tokens     t  ON r.id_token      = t.id_token
			JOIN asignaciones   a  ON r.id_asignacion = a.id_unidad
			JOIN usuarios       u  ON a.id_asesor     = u.idusuario
			LEFT JOIN grupos    g  ON a.id_grupo      = g.idgrupo
			LEFT JOIN modelos   m  ON a.id_modelo     = m.idmodelo
			LEFT JOIN sucursales s ON a.id_sucursal   = s.idsucursal
			JOIN enc_encuestas  e  ON r.id_encuesta   = e.id_encuesta
			WHERE r.id_respuesta = $id_respuesta";
$res_cab = mysqli_query($con, $SQL_cab);
if (mysqli_num_rows($res_cab) == 0) { header("Location: index.php?sec=resultados"); exit(); }
$cab = mysqli_fetch_array($res_cab);

// ── Detalle por pregunta ───────────────────────────────────
$SQL_det = "SELECT d.*,
				   p.texto_pregunta, p.tipo_pregunta, p.pondera, p.nro_orden,
				   ar.nombre AS area_nombre, ar.color AS area_color
			FROM enc_respuestas_detalle d
			JOIN enc_preguntas p ON d.id_pregunta = p.id_pregunta
			LEFT JOIN enc_areas ar ON p.id_area = ar.id_area
			WHERE d.id_respuesta = $id_respuesta
			ORDER BY p.nro_orden ASC";
$res_det = mysqli_query($con, $SQL_det);
$detalles = [];
while ($d = mysqli_fetch_array($res_det)) {
	$d['opciones_resp'] = [];
	if (in_array($d['tipo_pregunta'], [3, 4])) {
		$SQL_op = "SELECT ro.valor_elegido, o.texto_opcion
				   FROM enc_respuestas_opciones ro
				   JOIN enc_opciones o ON ro.id_opcion = o.id_opcion
				   WHERE ro.id_detalle = {$d['id_detalle']} ORDER BY o.nro_orden ASC";
		$res_op = mysqli_query($con, $SQL_op);
		while ($op = mysqli_fetch_array($res_op)) $d['opciones_resp'][] = $op;
	}
	$detalles[] = $d;
}

// ── Resumen por área ───────────────────────────────────────
$SQL_areas = "SELECT ar.nombre AS area, ar.color,
					 ROUND(AVG(d.respuesta_valor), 1) AS promedio
			  FROM enc_respuestas_detalle d
			  JOIN enc_preguntas p  ON d.id_pregunta = p.id_pregunta
			  JOIN enc_areas ar     ON p.id_area = ar.id_area
			  WHERE d.id_respuesta = $id_respuesta AND d.mostrada = 1 AND p.pondera = 1
			  GROUP BY ar.id_area, ar.nombre, ar.color
			  ORDER BY ar.nro_orden ASC";
$res_areas = mysqli_query($con, $SQL_areas);
$areas_prom = [];
while ($a = mysqli_fetch_array($res_areas)) $areas_prom[] = $a;

// ── Helpers ────────────────────────────────────────────────

// Convierte UTF-8 a Latin-1
function pdf_text($str) {
	if ($str === null || $str === '') return '';
	$str = str_replace(['—', '–', '\u2014', '\u2013'], [' - ', '-', '-', '-'], $str);
	return utf8_decode($str);
}

// Hexadecimal #RRGGBB → [R, G, B]
function hex2rgb($hex) {
	$hex = ltrim($hex, '#');
	if (strlen($hex) === 3) $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
	return [hexdec(substr($hex,0,2)), hexdec(substr($hex,2,2)), hexdec(substr($hex,4,2))];
}

// RGB para colorear por score (lee enc_niveles, fallback hardcodeado)
function score_rgb($val) {
	return hex2rgb(get_nivel((float)$val)['color']);
}

// Etiqueta de texto según score (lee enc_niveles, fallback hardcodeado)
function score_label($val) {
	return pdf_text(get_nivel((float)$val)['nombre']);
}

// Mezcla un color RGB con blanco para suavizarlo (0=original, 1=blanco puro)
function soften_rgb($r, $g, $b, $mix = 0.35) {
	return [
		(int)($r + $mix * (255 - $r)),
		(int)($g + $mix * (255 - $g)),
		(int)($b + $mix * (255 - $b)),
	];
}

// ── Clase PDF ──────────────────────────────────────────────
class EncuestaPDF extends FPDF {
	var $titulo_enc   = '';
	var $cliente_nombre = '';

	function Header() {
		// Logo
		$logo = dirname(__FILE__) . '/../../asignacion/imagenes/logodyv_c.png';
		if (file_exists($logo)) {
			$this->Image($logo, 12, 5, 38);
		}

		// Título principal
		$this->SetTextColor(68, 110, 150);
		$this->SetFont('Arial', 'B', 12);
		$this->SetXY(55, 6);
		$this->Cell(0, 6, 'Encuesta de Satisfacci' . chr(243) . 'n 0km', 0, 1, 'L');

		// Subtítulo (nombre de la encuesta)
		$this->SetFont('Arial', '', 8);
		$this->SetTextColor(100, 120, 140);
		$this->SetX(55);
		$this->Cell(0, 5, pdf_text($this->titulo_enc), 0, 1, 'L');

		// Cliente en esquina derecha
		$this->SetFont('Arial', 'I', 7);
		$this->SetTextColor(140, 150, 165);
		$this->SetXY(10, 18);
		$this->Cell(190, 4, pdf_text($this->cliente_nombre), 0, 1, 'R');

		// Línea separadora
		$this->SetDrawColor(155, 188, 212);
		$this->SetLineWidth(0.5);
		$this->Line(10, 24, 200, 24);

		$this->Ln(5);
	}

	function Footer() {
		$this->SetY(-14);
		$this->SetDrawColor(225, 232, 240);
		$this->SetLineWidth(0.3);
		$this->Line(10, $this->GetY(), 200, $this->GetY());
		$this->Ln(2);
		$this->SetFont('Arial', '', 7);
		$this->SetTextColor(170, 170, 170);
		$this->Cell(95, 4, 'Generado el ' . date('d/m/Y') . ' a las ' . date('H:i'), 0, 0, 'L');
		$this->Cell(95, 4, 'P' . chr(225) . 'g. ' . $this->PageNo(), 0, 0, 'R');
	}
}

// ── Crear PDF ──────────────────────────────────────────────
$pdf = new EncuestaPDF('P', 'mm', 'A4');
$pdf->titulo_enc    = $cab['enc_nombre'];
$pdf->cliente_nombre = 'Cliente: ' . $cab['cliente'];
$pdf->SetMargins(10, 35, 10);
$pdf->SetAutoPageBreak(true, 18);
$pdf->AddPage();

$prom_val = (float)$cab['resultado_promedio'];
$prom_str = $cab['resultado_promedio'] !== null ? number_format($prom_val, 1) : '-';
$tipos    = [1 => 'Escala 1-10', 2 => 'S' . chr(237) . '/No',
             3 => 'Selecci' . chr(243) . 'n m' . chr(250) . 'ltiple',
             4 => 'Lista S' . chr(237) . '/No', 5 => 'Texto libre'];

// ═══════════════════════════════════════════════════════════
// SECCIÓN: DATOS DEL CLIENTE
// ═══════════════════════════════════════════════════════════

// Encabezado de sección
$pdf->SetFillColor(246, 249, 252);
$pdf->SetDrawColor(155, 188, 212);
$pdf->SetLineWidth(0.5);
$pdf->Rect(10, $pdf->GetY(), 190, 7, 'DF');
$pdf->SetFillColor(110, 152, 182);
$pdf->Rect(10, $pdf->GetY(), 3, 7, 'F');
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(68, 110, 150);
$pdf->SetX(15);
$pdf->Cell(0, 7, 'DATOS DEL CLIENTE', 0, 1, 'L');
$pdf->Ln(2);

// Grilla 2 columnas
$y_grid = $pdf->GetY();
$col_w  = 90;   // ancho de cada columna
$lbl_w  = 32;   // ancho de la etiqueta
$val_w  = $col_w - $lbl_w;
$row_h  = 6;

$campos_izq = [
	['Cliente',            pdf_text($cab['cliente'])],
	['Veh' . chr(237) . 'culo', pdf_text($cab['grupo'] . ($cab['modelo'] ? ' — ' . $cab['modelo'] : ''))],
	['Fecha entrega',      cambiarFormatoFecha($cab['fec_entrega'])],
];
$campos_der = [
	['Asesor',             pdf_text($cab['asesor'])],
	['Sucursal',           pdf_text($cab['sucursal'])],
	['Fecha respuesta',    cambiarFormatoFecha(substr($cab['fecha_completada'], 0, 10))],
];

for ($i = 0; $i < max(count($campos_izq), count($campos_der)); $i++) {
	$y_row = $y_grid + $i * $row_h;
	// Fondo alternado
	if ($i % 2 === 0) {
		$pdf->SetFillColor(248, 250, 252);
	} else {
		$pdf->SetFillColor(255, 255, 255);
	}
	$pdf->Rect(10, $y_row, 190, $row_h, 'F');

	// Columna izquierda
	if (isset($campos_izq[$i])) {
		$pdf->SetXY(12, $y_row + 0.8);
		$pdf->SetFont('Arial', '', 7.5);
		$pdf->SetTextColor(130, 130, 130);
		$pdf->Cell($lbl_w, $row_h - 1, $campos_izq[$i][0] . ':', 0, 0, 'L');
		$pdf->SetFont('Arial', 'B', 7.5);
		$pdf->SetTextColor(70, 72, 76);
		$pdf->Cell($val_w, $row_h - 1, $campos_izq[$i][1], 0, 0, 'L');
	}

	// Divisor vertical central
	$pdf->SetDrawColor(232, 237, 243);
	$pdf->SetLineWidth(0.2);
	$pdf->Line(105, $y_row, 105, $y_row + $row_h);

	// Columna derecha
	if (isset($campos_der[$i])) {
		$pdf->SetXY(107, $y_row + 0.8);
		$pdf->SetFont('Arial', '', 7.5);
		$pdf->SetTextColor(130, 130, 130);
		$pdf->Cell($lbl_w, $row_h - 1, $campos_der[$i][0] . ':', 0, 0, 'L');
		$pdf->SetFont('Arial', 'B', 7.5);
		$pdf->SetTextColor(70, 72, 76);
		$pdf->Cell($val_w, $row_h - 1, $campos_der[$i][1], 0, 0, 'L');
	}
}

// Borde exterior de la grilla
$pdf->SetDrawColor(225, 232, 240);
$pdf->SetLineWidth(0.3);
$grid_h = max(count($campos_izq), count($campos_der)) * $row_h;
$pdf->Rect(10, $y_grid, 190, $grid_h, 'D');

$pdf->SetXY(10, $y_grid + $grid_h);
$pdf->Ln(6);

// ═══════════════════════════════════════════════════════════
// SECCIÓN: RESULTADO GENERAL + ÁREAS (dos columnas)
// ═══════════════════════════════════════════════════════════

list($sr, $sg, $sb) = score_rgb($prom_val);

// Encabezado de sección
$pdf->SetFillColor(246, 249, 252);
$pdf->SetDrawColor(155, 188, 212);
$pdf->SetLineWidth(0.5);
$pdf->Rect(10, $pdf->GetY(), 190, 7, 'DF');
$pdf->SetFillColor(110, 152, 182);
$pdf->Rect(10, $pdf->GetY(), 3, 7, 'F');
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(68, 110, 150);
$pdf->SetX(15);
$pdf->Cell(0, 7, 'RESULTADO GENERAL', 0, 1, 'L');
$pdf->Ln(3);

$sec_y     = $pdf->GetY();
$col_izq_w = 72;           // columna izquierda (score)
$col_der_x = 10 + $col_izq_w + 8;  // columna derecha (áreas)
$col_der_w = 190 - $col_izq_w - 8; // 110mm

// ── Columna izquierda: score ──────────────────────────────
// Número grande centrado
$pdf->SetFont('Arial', 'B', 42);
$pdf->SetTextColor($sr, $sg, $sb);
$pdf->SetXY(10, $sec_y + 2);
$pdf->Cell($col_izq_w, 15, $prom_str, 0, 0, 'C');

// "/ 10" centrado debajo
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(160, 160, 160);
$pdf->SetXY(10, $sec_y + 17);
$pdf->Cell($col_izq_w, 5, '/ 10', 0, 0, 'C');

// Etiqueta de nivel
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor($sr, $sg, $sb);
$pdf->SetXY(10, $sec_y + 23);
$pdf->Cell($col_izq_w, 5, score_label($prom_val), 0, 0, 'C');

// Barra de progreso
$bar_x = 18;
$bar_y = $sec_y + 30;
$bar_w = $col_izq_w - 16;
$pdf->SetFillColor(210, 215, 220);
$pdf->Rect($bar_x, $bar_y, $bar_w, 2.5, 'F');
list($ssr, $ssg, $ssb) = soften_rgb($sr, $sg, $sb);
$pdf->SetFillColor($ssr, $ssg, $ssb);
$pdf->Rect($bar_x, $bar_y, ($prom_val / 10) * $bar_w, 2.5, 'F');

// ── Columna derecha: áreas ────────────────────────────────
if (!empty($areas_prom)) {
	$area_label_w = 34;
	$area_bar_w   = 52;
	$area_val_w   = 18;
	$area_h       = 6.5;

	// Título pequeño de la columna
	$pdf->SetFont('Arial', 'B', 7);
	$pdf->SetTextColor(110, 110, 110);
	$pdf->SetXY($col_der_x, $sec_y);
	$pdf->Cell($col_der_w, 5, 'POR ' . chr(193) . 'REA', 0, 0, 'L');

	foreach ($areas_prom as $i => $ap) {
		$av = (float)$ap['promedio'];
		list($vr, $vg, $vb) = score_rgb($av);
		list($cr, $cg, $cb) = $ap['color'] ? hex2rgb($ap['color']) : [90, 132, 168];
		$row_y = $sec_y + 5 + $i * $area_h;

		// Fondo alternado neutro
		$bg = $i % 2 === 0 ? 248 : 255;
		$pdf->SetFillColor($bg, $bg, $bg);
		$pdf->Rect($col_der_x, $row_y, $col_der_w, $area_h, 'F');

		// Cuadrado de color del área
		$pdf->SetFillColor($cr, $cg, $cb);
		$pdf->Rect($col_der_x + 1, $row_y + 2, 3, 3, 'F');

		// Nombre del área
		$pdf->SetXY($col_der_x + 6, $row_y + 0.8);
		$pdf->SetFont('Arial', 'B', 7.5);
		$pdf->SetTextColor($cr, $cg, $cb);
		$pdf->Cell($area_label_w, $area_h - 1.5, pdf_text($ap['area']), 0, 0, 'L');

		// Barra fondo (gris)
		$bx = $col_der_x + 6 + $area_label_w;
		$pdf->SetFillColor(222, 226, 232);
		$pdf->Rect($bx, $row_y + 2, $area_bar_w, 3, 'F');

		// Barra rellena (color del score, suavizado)
		list($svr, $svg, $svb) = soften_rgb($vr, $vg, $vb);
		$pdf->SetFillColor($svr, $svg, $svb);
		$pdf->Rect($bx, $row_y + 2, ($av / 10) * $area_bar_w, 3, 'F');

		// Valor numérico
		$pdf->SetFont('Arial', 'B', 8);
		$pdf->SetTextColor($vr, $vg, $vb);
		$pdf->SetXY($bx + $area_bar_w + 3, $row_y + 0.8);
		$av_str = (floor($av) == $av) ? number_format($av, 0) : number_format($av, 1);
		$pdf->Cell($area_val_w, $area_h - 1.5, $av_str . '/10', 0, 0, 'L');
	}

	// Borde exterior de la tabla de áreas
	$areas_h = count($areas_prom) * $area_h;
	$pdf->SetDrawColor(225, 232, 240);
	$pdf->SetLineWidth(0.2);
	$pdf->Rect($col_der_x, $sec_y + 5, $col_der_w, $areas_h, 'D');

	$end_y = $sec_y + max(36, 5 + $areas_h);
} else {
	$end_y = $sec_y + 36;
}

// Línea divisora vertical entre columnas
$pdf->SetDrawColor(228, 233, 238);
$pdf->SetLineWidth(0.3);
$pdf->Line($col_der_x - 4, $sec_y, $col_der_x - 4, $end_y);

$pdf->SetXY(10, $end_y);
$pdf->Ln(6);

// ═══════════════════════════════════════════════════════════
// SECCIÓN: RESPUESTAS POR PREGUNTA
// ═══════════════════════════════════════════════════════════

$pdf->SetFillColor(246, 249, 252);
$pdf->SetDrawColor(155, 188, 212);
$pdf->SetLineWidth(0.5);
$pdf->Rect(10, $pdf->GetY(), 190, 7, 'DF');
$pdf->SetFillColor(110, 152, 182);
$pdf->Rect(10, $pdf->GetY(), 3, 7, 'F');
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(68, 110, 150);
$pdf->SetX(15);
$pdf->Cell(0, 7, 'RESPUESTAS POR PREGUNTA', 0, 1, 'L');
$pdf->Ln(3);

foreach ($detalles as $d) {
	if (!$d['mostrada']) continue;
	$tipo = (int)$d['tipo_pregunta'];

	if ($pdf->GetY() > 245) $pdf->AddPage();

	$q_x       = 10;
	$q_inner_x = 14;
	$q_w       = 186;

	// Color del área (borde izquierdo)
	list($ar, $ag, $ab) = [90, 132, 168];
	if ($d['area_color']) list($ar, $ag, $ab) = hex2rgb($d['area_color']);

	// ── Resultado para mostrar a la derecha (tipos 1, 2, 3) ──
	$result_txt = '';
	$result_w   = 22;
	$result_rgb = [80, 80, 80]; // color por defecto
	if ($d['mostrada']) {
		if ($tipo == 1) {
			$rv = (float)$d['respuesta_valor'];
			$result_txt = number_format($rv, 0) . '/10';
			$result_rgb = score_rgb($rv);
		} elseif ($tipo == 2) {
			$es_si = ((float)$d['respuesta_valor'] == 10);
			$result_txt = $es_si ? 'S' . chr(237) : 'No';
			$result_rgb = $es_si ? [30, 132, 73] : [180, 60, 50];
		} elseif ($tipo == 3) {
			$rv = (float)$d['respuesta_valor'];
			$result_txt = number_format($rv, 1) . '/10';
			$result_rgb = score_rgb($rv);
		}
	}
	$show_result = ($result_txt !== '');
	$txt_w = $q_w - $result_w - 2; // siempre reservar columna derecha para alinear

	// ── Encabezado: texto de la pregunta ─────────────────────
	$hdr_y      = $pdf->GetY();
	$area_sufijo = $d['area_nombre'] ? '  ' . chr(150) . '  ' . pdf_text($d['area_nombre']) : '';
	$texto_preg = $d['nro_orden'] . '. ' . pdf_text($d['texto_pregunta']) . $area_sufijo;

	$pdf->SetXY($q_inner_x, $hdr_y);
	$pdf->SetFillColor(247, 248, 250);
	$pdf->SetFont('Arial', '', 8.5);
	$pdf->SetTextColor(55, 58, 62);
	$pdf->MultiCell($txt_w, 5.5, $texto_preg, 0, 'L', true);
	$hdr_end_y = $pdf->GetY();
	$hdr_h     = $hdr_end_y - $hdr_y;

	// ── Resultado a la derecha (en el espacio que dejó MultiCell) ──
	if ($show_result) {
		$res_x = $q_inner_x + $txt_w + 2;
		$res_w = $result_w - 2;
		// Fondo igual al header
		$pdf->SetFillColor(247, 248, 250);
		$pdf->Rect($res_x, $hdr_y, $res_w, $hdr_h, 'F');
		// Valor en color del nivel
		$pdf->SetFont('Arial', 'B', 9);
		$pdf->SetTextColor($result_rgb[0], $result_rgb[1], $result_rgb[2]);
		$pdf->SetXY($res_x + 1, $hdr_y + ($hdr_h / 2) - 2.5);
		$pdf->Cell($res_w - 2, 5, $result_txt, 0, 0, 'C');
		$pdf->SetXY($q_inner_x, $hdr_end_y);
	}

	$pdf->SetXY($q_inner_x, $hdr_end_y);

	// Borde izquierdo de color — cubre header
	$pdf->SetFillColor($ar, $ag, $ab);
	$pdf->Rect($q_x, $hdr_y, 3, $pdf->GetY() - $hdr_y, 'F');

	// ── Cuerpo: solo tipo 4 (lista) y tipo 5 (texto libre) ───
	$body_y = $pdf->GetY();

	if ($tipo == 4) {
		// Lista Sí/No — se mantiene completa
		$pdf->SetXY($q_inner_x, $body_y);
		if (empty($d['opciones_resp'])) {
			$pdf->SetFont('Arial', 'I', 8);
			$pdf->SetTextColor(160, 160, 160);
			$pdf->Cell($q_w, 6, 'Sin respuesta.', 0, 1, 'L', true);
		} else {
			foreach ($d['opciones_resp'] as $j => $op) {
				$ry = $pdf->GetY();
				// Fondo alternado: B&W safe (blanco / gris muy claro)
				$bg = $j % 2 === 0 ? 248 : 255;
				$pdf->SetFillColor($bg, $bg, $bg);
				$pdf->Rect($q_inner_x, $ry, $q_w, 5, 'F');
				$pdf->SetFont('Arial', '', 8);
				$pdf->SetTextColor(62, 65, 68);
				$pdf->SetXY($q_inner_x + 2, $ry + 0.5);
				$pdf->Cell($q_w - 22, 4, pdf_text($op['texto_opcion']), 0, 0, 'L');
				$badge_bx = $q_inner_x + $q_w - 18;
				$pdf->SetFont('Arial', 'B', 7);
				if ($op['valor_elegido']) {
					$pdf->SetFillColor(220, 242, 231); // verde claro
					$pdf->Rect($badge_bx, $ry + 0.8, 16, 3.5, 'F');
					$pdf->SetTextColor(48, 148, 90);   // verde oscuro
					$pdf->SetXY($badge_bx, $ry + 0.8);
					$pdf->Cell(16, 3.5, 'S' . chr(237), 0, 0, 'C');
				} else {
					$pdf->SetFillColor(248, 205, 200); // rojo claro
					$pdf->Rect($badge_bx, $ry + 0.8, 16, 3.5, 'F');
					$pdf->SetTextColor(195, 80, 68);   // rojo oscuro
					$pdf->SetXY($badge_bx, $ry + 0.8);
					$pdf->Cell(16, 3.5, 'No', 0, 0, 'C');
				}
				$pdf->SetXY($q_inner_x, $ry + 5);
			}
		}
		// Borde izquierdo extendido al cuerpo
		$pdf->SetFillColor($ar, $ag, $ab);
		$pdf->Rect($q_x, $body_y, 3, $pdf->GetY() - $body_y, 'F');

	} elseif ($tipo == 5) {
		// Texto libre
		$txt = $d['respuesta_texto'] ? $d['respuesta_texto'] : '(sin comentarios)';
		$pdf->SetXY($q_inner_x, $body_y);
		$pdf->SetFillColor(250, 250, 250);
		$pdf->SetFont('Arial', 'I', 8.5);
		$pdf->SetTextColor(60, 60, 60);
		$pdf->MultiCell($q_w, 5, pdf_text($txt), 0, 'L', true);
		// Borde izquierdo extendido al cuerpo
		$pdf->SetFillColor($ar, $ag, $ab);
		$pdf->Rect($q_x, $body_y, 3, $pdf->GetY() - $body_y, 'F');
	}

	$pdf->Ln(4);
}

// ── Salida ─────────────────────────────────────────────────
$nombre_archivo = 'encuesta_' . $id_respuesta . '_' . preg_replace('/[^a-z0-9]/i', '_', $cab['cliente']) . '.pdf';
$pdf->Output('I', $nombre_archivo);
?>
