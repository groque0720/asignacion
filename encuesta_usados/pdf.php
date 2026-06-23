<?php
/* Resultados · PDF de una respuesta de encuesta de usados (FPDF). */
error_reporting(E_ERROR | E_PARSE);
@ini_set('display_errors', '0');
require __DIR__ . '/config/config_app.php';      // auth + $con
require __DIR__ . '/funciones/consulta.php';      // eu_utf8, eu_nivel
require __DIR__ . '/../asignacion/fpdf/fpdf.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: dashboard.php'); exit(); }

$resp = mysqli_fetch_assoc(mysqli_query($con,
    "SELECT r.*, t.fecha_respuesta, e.nombre AS encuesta_nombre
     FROM encu_respuestas r
     JOIN encu_tokens t ON t.id_token = r.id_token
     JOIN encu_encuestas e ON e.id_encuesta = r.id_encuesta
     WHERE r.id_respuesta = $id LIMIT 1"));
if (!$resp) { header('Location: dashboard.php'); exit(); }

$idu = (int)$resp['id_asignacion'];
$u = mysqli_fetch_assoc(mysqli_query($con,
    "SELECT cliente, vehiculo, `año` AS anio, km, dominio, fec_entrega, asesor_venta, id_sucursal
     FROM view_asignaciones_usados_entregadas WHERE id_unidad = $idu LIMIT 1")) ?: [];

$prom  = $resp['resultado_promedio'] !== null ? (float)$resp['resultado_promedio'] : null;
$nivel = $prom !== null ? eu_nivel($prom) : null;

$preguntas = [];
$qp = mysqli_query($con,
    "SELECT p.id_pregunta, p.texto_pregunta, p.tipo_pregunta, p.pondera,
            a.nombre AS area_nombre, d.respuesta_valor, d.respuesta_texto, d.mostrada
     FROM encu_preguntas p
     LEFT JOIN encu_areas a ON a.id_area = p.id_area
     LEFT JOIN encu_respuestas_detalle d ON d.id_pregunta = p.id_pregunta AND d.id_respuesta = $id
     WHERE p.id_encuesta = {$resp['id_encuesta']} AND p.baja = 0
     ORDER BY p.nro_orden ASC");
while ($p = mysqli_fetch_assoc($qp)) $preguntas[] = $p;

$opcs = [];
$qo = mysqli_query($con,
    "SELECT d.id_pregunta, o.texto_opcion, ro.valor_elegido
     FROM encu_respuestas_detalle d
     JOIN encu_respuestas_opciones ro ON ro.id_detalle = d.id_detalle
     JOIN encu_opciones o ON o.id_opcion = ro.id_opcion
     WHERE d.id_respuesta = $id ORDER BY o.nro_orden ASC");
while ($o = mysqli_fetch_assoc($qo)) $opcs[(int)$o['id_pregunta']][] = $o;

function T($s) { return utf8_decode(eu_utf8((string)$s)); }
function fFecha($f) { if (!$f || $f === '0000-00-00') return '-'; $p = explode('-', substr($f,0,10)); return count($p)===3 ? "$p[2]/$p[1]/$p[0]" : $f; }
$sucs = [1=>'Resistencia',2=>'Sáenz Peña',3=>'Villa Ángela',4=>'Charata'];
$tipoLabel = [1=>'Escala 1-10',2=>'Sí / No',3=>'Selección múltiple',4=>'Lista Sí/No',5=>'Texto libre'];

class EUPDF extends FPDF {
    function Header() {
        $this->SetFillColor(15, 23, 42);
        $this->Rect(0, 0, 210, 22, 'F');
        $this->SetTextColor(255); $this->SetFont('Arial', 'B', 14);
        $this->SetXY(12, 6); $this->Cell(0, 6, utf8_decode('Encuesta de Satisfacción · Usados'), 0, 1);
        $this->SetFont('Arial', '', 9); $this->SetX(12); $this->Cell(0, 5, utf8_decode('Derka y Vargas S.A.'), 0, 1);
        $this->SetTextColor(40); $this->Ln(8);
    }
    function Footer() {
        $this->SetY(-12); $this->SetFont('Arial', '', 8); $this->SetTextColor(150);
        $this->Cell(0, 8, utf8_decode('Derka y Vargas S.A. · Encuesta de Usados'), 0, 0, 'L');
        $this->Cell(0, 8, utf8_decode('Página ').$this->PageNo(), 0, 0, 'R');
    }
}

$pdf = new EUPDF('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->SetMargins(12, 22, 12);
$pdf->AddPage();

// Datos cliente
$pdf->SetFont('Arial', 'B', 13); $pdf->SetTextColor(20);
$pdf->Cell(0, 7, T($u['cliente'] ?? '-'), 0, 1);
$pdf->SetFont('Arial', '', 10); $pdf->SetTextColor(80);
$veh = ($u['vehiculo'] ?? '') . ($u && $u['anio'] ? ' - '.(int)$u['anio'] : '') . ($u && $u['dominio'] ? ' - '.$u['dominio'] : '');
$pdf->Cell(0, 6, T($veh), 0, 1);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(95, 5, T('Asesor: '.($u['asesor_venta'] ?? '-')), 0, 0);
$pdf->Cell(0, 5, T('Sucursal: '.($sucs[$u['id_sucursal'] ?? 0] ?? '-')), 0, 1);
$pdf->Cell(95, 5, T('Entrega: '.fFecha($u['fec_entrega'] ?? '')), 0, 0);
$pdf->Cell(0, 5, T('Respondida: '.fFecha($resp['fecha_respuesta'])), 0, 1);
$pdf->Ln(3);

// Promedio
if ($nivel) {
    list($r, $g, $b) = sscanf($nivel['color'], "#%02x%02x%02x");
    $pdf->SetFillColor($r, $g, $b); $pdf->SetTextColor(255); $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 9, T('  Promedio general: '.number_format($prom,1).'/10   -   '.$nivel['nombre']), 0, 1, 'L', true);
} else {
    $pdf->SetFillColor(230); $pdf->SetTextColor(80); $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 9, T('  Sin promedio (sin preguntas ponderadas)'), 0, 1, 'L', true);
}
$pdf->SetTextColor(40); $pdf->Ln(4);

// Preguntas
foreach ($preguntas as $i => $p) {
    $tipo = (int)$p['tipo_pregunta']; $mostrada = (int)$p['mostrada'];
    $val  = $p['respuesta_valor'] !== null ? (float)$p['respuesta_valor'] : null;

    if ($pdf->GetY() > 262) $pdf->AddPage();
    $pdf->SetFont('Arial', '', 7); $pdf->SetTextColor(120);
    $meta = ($tipoLabel[$tipo] ?? '') . ($p['area_nombre'] ? '  |  '.$p['area_nombre'] : '');
    $pdf->Cell(0, 4, T($meta), 0, 1);
    $pdf->SetFont('Arial', 'B', 10); $pdf->SetTextColor(20);
    $pdf->MultiCell(0, 5, T(($i+1).'. '.$p['texto_pregunta']));
    $pdf->SetFont('Arial', '', 10); $pdf->SetTextColor(60);

    if ($mostrada === 0) {
        $pdf->SetTextColor(150); $pdf->Cell(0, 5, T('No correspondía (omitida)'), 0, 1);
    } elseif ($tipo === 1) {
        $pdf->Cell(0, 5, T('Respuesta: '.($val !== null ? number_format($val,0).'/10' : '-')), 0, 1);
    } elseif ($tipo === 2) {
        $pdf->Cell(0, 5, T('Respuesta: '.($val !== null && $val >= 10 ? 'Sí' : 'No')), 0, 1);
    } elseif ($tipo === 3) {
        $sel = array_map(function($o){ return $o['texto_opcion']; }, $opcs[(int)$p['id_pregunta']] ?? []);
        $pdf->MultiCell(0, 5, T('Seleccionó: '.($sel ? implode(', ', $sel) : 'nada')));
    } elseif ($tipo === 4) {
        foreach ($opcs[(int)$p['id_pregunta']] ?? [] as $o) {
            $pdf->Cell(0, 5, T('   - '.$o['texto_opcion'].': '.((int)$o['valor_elegido']===1?'Sí':'No')), 0, 1);
        }
    } elseif ($tipo === 5) {
        $txt = trim((string)$p['respuesta_texto']);
        $pdf->MultiCell(0, 5, T($txt !== '' ? $txt : '(sin comentarios)'));
    }
    $pdf->Ln(2);
    $pdf->SetDrawColor(230); $pdf->Line($pdf->GetX(), $pdf->GetY(), 198, $pdf->GetY()); $pdf->Ln(2);
}

$pdf->Output('I', 'encuesta_usado_'.$id.'.pdf');
