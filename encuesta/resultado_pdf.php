<?php
@session_start();
if ($_SESSION["autentificado"] != "SI") { header("Location: ../login"); exit(); }
include_once("config.php");
if (!in_array($_SESSION["idperfil"], ENCUESTA_PERFILES)) { header("Location: ../login"); exit(); }

require('../asignacion/fpdf/fpdf.php');
include_once("funciones/func_mysql.php");
conectar();

$id_respuesta = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id_respuesta <= 0) { header("Location: index.php?sec=resultados"); exit(); }

// Datos de cabecera
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

// Detalle
$SQL_det = "SELECT d.*,
				   p.texto_pregunta, p.tipo_pregunta, p.pondera, p.nro_orden,
				   ar.nombre AS area_nombre
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

// ── Crear PDF ───────────────────────────────────────────────
class EncuestaPDF extends FPDF {
	var $titulo_enc = '';
	function Header() {
		// Logo
		$logo = dirname(__FILE__) . '/../asignacion/imagenes/logodyv_c.png';
		if (file_exists($logo)) {
			$this->Image($logo, 10, 6, 40);
		}
		$this->SetFont('Arial', 'B', 13);
		$this->SetTextColor(26, 82, 118);
		$this->SetXY(55, 8);
		$this->Cell(0, 6, 'Encuesta de Satisfacci' . chr(243) . 'n 0km', 0, 1, 'L');
		$this->SetFont('Arial', '', 9);
		$this->SetTextColor(100, 100, 100);
		$this->SetX(55);
		$this->Cell(0, 5, pdf_text($this->titulo_enc), 0, 1, 'L');
		$this->Ln(2);
		$this->SetDrawColor(26, 82, 118);
		$this->SetLineWidth(0.5);
		$this->Line(10, $this->GetY(), 200, $this->GetY());
		$this->Ln(3);
	}
	function Footer() {
		$this->SetY(-12);
		$this->SetFont('Arial', 'I', 7);
		$this->SetTextColor(150, 150, 150);
		$this->Cell(0, 5, 'P' . chr(225) . 'g. ' . $this->PageNo(), 0, 0, 'C');
	}
}

$pdf = new EncuestaPDF('P', 'mm', 'A4');
$pdf->titulo_enc = $cab['enc_nombre'];
$pdf->SetMargins(10, 28, 10);
$pdf->SetAutoPageBreak(true, 15);
$pdf->AddPage();

$prom = $cab['resultado_promedio'] !== null ? number_format($cab['resultado_promedio'], 1) : '-';

// ── Datos del cliente ──────────────────────────────────────
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor(26, 82, 118);
$pdf->Cell(0, 6, 'Datos del Cliente', 0, 1);
$pdf->SetLineWidth(0.2);
$pdf->SetDrawColor(200, 200, 200);

// Convierte UTF-8 a Latin-1 y reemplaza caracteres fuera del charset
function pdf_text($str) {
	$str = str_replace(['—', '–'], [' - ', '-'], $str);
	return utf8_decode($str);
}

function fila_dato($pdf, $label, $valor) {
	$pdf->SetFont('Arial', '', 9);
	$pdf->SetTextColor(120, 120, 120);
	$pdf->Cell(42, 5, $label, 'B', 0);           // label: ya en Latin-1 (usa chr())
	$pdf->SetTextColor(50, 50, 50);
	$pdf->Cell(0, 5, pdf_text($valor), 'B', 1);  // valor: UTF-8 de la BD
}

fila_dato($pdf, 'Cliente',         $cab['cliente']);
fila_dato($pdf, 'Veh' . chr(237) . 'culo', $cab['grupo'] . ($cab['modelo'] ? ' — ' . $cab['modelo'] : ''));
fila_dato($pdf, 'Fecha de entrega', cambiarFormatoFecha($cab['fec_entrega']));
fila_dato($pdf, 'Asesor',          $cab['asesor']);
fila_dato($pdf, 'Sucursal',        $cab['sucursal']);
fila_dato($pdf, 'Fecha respuesta', cambiarFormatoFecha(substr($cab['fecha_completada'], 0, 10)));

$pdf->Ln(5);

// ── Resultado promedio ─────────────────────────────────────
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor(26, 82, 118);
$pdf->Cell(0, 6, 'Resultado General', 0, 1);

$prom_val = (float)$cab['resultado_promedio'];
if ($prom_val >= 8)       { $pdf->SetTextColor(30, 132, 73); }
elseif ($prom_val >= 6)   { $pdf->SetTextColor(214, 137, 16); }
else                      { $pdf->SetTextColor(192, 57, 43); }
$pdf->SetFont('Arial', 'B', 28);
$pdf->Cell(0, 12, $prom . ' / 10', 0, 1, 'C');
$pdf->SetTextColor(120, 120, 120);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 4, '(promedio de preguntas ponderadas)', 0, 1, 'C');
$pdf->Ln(4);

// ── Respuestas ─────────────────────────────────────────────
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor(26, 82, 118);
$pdf->Cell(0, 6, 'Respuestas por Pregunta', 0, 1);
$pdf->SetDrawColor(200, 200, 200);

$tipos = [1=>'Escala 1-10', 2=>'S' . chr(237) . '/No', 3=>'Selecci' . chr(243) . 'n m' . chr(250) . 'ltiple', 4=>'Lista S' . chr(237) . '/No', 5=>'Texto libre'];

foreach ($detalles as $d) {
	$tipo = (int)$d['tipo_pregunta'];

	// Título de la pregunta
	$pdf->SetFont('Arial', 'B', 9);
	$pdf->SetTextColor(50, 50, 50);
	$pdf->SetFillColor(240, 246, 251);
	$texto_preg = $d['nro_orden'] . '. ' . $d['texto_pregunta'];
	$pdf->MultiCell(0, 5, pdf_text($texto_preg), 'LTR', 'L', true);

	// Tipo + pondera + área
	$pdf->SetFont('Arial', 'I', 7);
	$pdf->SetTextColor(130, 130, 130);
	$sep = ' ' . chr(150) . ' '; // guión medio en Latin-1
	$tipo_label = isset($tipos[$tipo]) ? $tipos[$tipo] : '?';
	$pond_label = ($d['pondera'] && $d['mostrada']) ? $sep . 'Pondera' : ($d['mostrada'] ? '' : $sep . 'No mostrada');
	$area_label = $d['area_nombre'] ? $sep . pdf_text($d['area_nombre']) : '';
	$pdf->Cell(0, 4, $tipo_label . $pond_label . $area_label, 'LR', 1, 'L', true);

	// Respuesta
	$pdf->SetFont('Arial', '', 9);
	$pdf->SetFillColor(255, 255, 255);

	if (!$d['mostrada']) {
		$pdf->SetTextColor(170, 170, 170);
		$pdf->Cell(0, 5, 'Omitida por condici' . chr(243) . 'n.', 'LBR', 1, 'L');

	} elseif ($tipo == 1) {
		$pdf->SetTextColor(50, 50, 50);
		$rv = number_format($d['respuesta_valor'], 0);
		$pdf->Cell(0, 5, 'Calificaci' . chr(243) . 'n: ' . $rv . ' / 10', 'LBR', 1, 'L');

	} elseif ($tipo == 2) {
		$pdf->SetTextColor(50, 50, 50);
		$str = ((float)$d['respuesta_valor'] == 10) ? 'S' . chr(237) : 'No';
		$pdf->Cell(0, 5, $str, 'LBR', 1, 'L');

	} elseif ($tipo == 3) {
		if (empty($d['opciones_resp'])) {
			$pdf->SetTextColor(170, 170, 170);
			$pdf->Cell(0, 5, 'Ninguna opci' . chr(243) . 'n seleccionada.', 'LBR', 1, 'L');
		} else {
			$pdf->SetTextColor(50, 50, 50);
			foreach ($d['opciones_resp'] as $op) {
				$pdf->Cell(4, 5, chr(149), 0, 0);
				$pdf->Cell(0, 5, pdf_text($op['texto_opcion']), 0, 1);
			}
			$pdf->SetTextColor(120, 120, 120);
			$pdf->SetFont('Arial', 'I', 8);
			$pdf->Cell(0, 4, 'Valor: ' . number_format($d['respuesta_valor'], 1) . '/10', 'LBR', 1, 'L');
		}

	} elseif ($tipo == 4) {
		if (empty($d['opciones_resp'])) {
			$pdf->SetTextColor(170, 170, 170);
			$pdf->Cell(0, 5, 'Sin respuesta.', 'LBR', 1, 'L');
		} else {
			foreach ($d['opciones_resp'] as $op) {
				$pdf->SetTextColor(50, 50, 50);
				$val_str = $op['valor_elegido'] ? 'S' . chr(237) : 'No';
				$pdf->Cell(130, 4, pdf_text($op['texto_opcion']), 0, 0);
				$pdf->SetFont('Arial', 'B', 9);
				$color = $op['valor_elegido'] ? [30, 132, 73] : [192, 57, 43];
				$pdf->SetTextColor($color[0], $color[1], $color[2]);
				$pdf->Cell(0, 4, $val_str, 0, 1);
				$pdf->SetFont('Arial', '', 9);
			}
			$pdf->Cell(0, 1, '', 'LBR', 1);
		}

	} else {
		// Texto libre
		$txt = $d['respuesta_texto'] ? $d['respuesta_texto'] : '(sin comentarios)';
		$pdf->SetTextColor(70, 70, 70);
		$pdf->SetFont('Arial', 'I', 9);
		$pdf->MultiCell(0, 5, pdf_text($txt), 'LBR', 'L');
	}

	$pdf->Ln(2);
}

// ── Pie ───────────────────────────────────────────────────
$pdf->Ln(4);
$pdf->SetFont('Arial', 'I', 7);
$pdf->SetTextColor(180, 180, 180);
$pdf->Cell(0, 4, 'Generado el ' . date('d/m/Y H:i'), 0, 0, 'R');

$pdf->Output('I', 'encuesta_' . $id_respuesta . '_' . preg_replace('/[^a-z0-9]/i', '_', $cab['cliente']) . '.pdf');
?>
