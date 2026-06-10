<?php
/*
 * Estado de Cuenta en PDF (para imprimir). Streamea el archivo y termina.
 * Requiere: $con y ec_datos/ec_fecha (funciones/consulta.php).
 */
require __DIR__ . '/../../asignacion/fpdf/fpdf.php';

$idcliente = (int)($_GET['IDrecord'] ?? $_GET['idcliente'] ?? 0);
$d = ec_datos($con, $idcliente);
if ($d === null) { die("No se encontró estado de cuenta para el cliente."); }

function u($s)   { return utf8_decode((string)$s); }
function ars($n) { return '$ '.number_format((float)$n, 2, ',', '.'); }

class ECPDF extends FPDF {
    function Header() {
        $w = $this->GetPageWidth() - 20;
        $this->SetFillColor(15, 23, 42);
        $this->Rect(10, 10, $w, 15, 'F');
        $this->SetTextColor(255, 255, 255);
        $this->SetXY(13, 12);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(120, 7, u('ESTADO DE CUENTA'), 0, 2, 'L');
        $this->SetFont('Arial', '', 8.5);
        $this->SetTextColor(148, 163, 184);
        $this->Cell(120, 5, u('Derka y Vargas S.A.'), 0, 0, 'L');
        $this->SetXY(10, 14.5);
        $this->SetTextColor(203, 213, 225);
        $this->SetFont('Arial', '', 8.5);
        $this->Cell($w - 3, 5, u('Generado: '.date('d/m/Y  H:i')), 0, 0, 'R');
        $this->SetY(30);
    }
    function Footer() {
        $this->SetY(-12);
        $this->SetDrawColor(226, 232, 240);
        $this->Line(10, $this->GetY(), $this->GetPageWidth() - 10, $this->GetY());
        $this->SetY(-10);
        $this->SetFont('Arial', 'I', 7.5);
        $this->SetTextColor(148, 163, 184);
        $this->Cell(100, 6, u('Derka y Vargas S.A.  ·  Estado de Cuenta'), 0, 0, 'L');
        $this->Cell($this->GetPageWidth() - 20 - 100, 6, u('Página '.$this->PageNo().' de {nb}'), 0, 0, 'R');
    }
    function fit($txt, $w) {
        $txt = u($txt);
        if ($this->GetStringWidth($txt) <= $w - 2) return $txt;
        while (strlen($txt) > 1 && $this->GetStringWidth($txt.'..') > $w - 2) $txt = substr($txt, 0, -1);
        return $txt.'..';
    }
    // fila clave/valor del bloque de datos
    function kv($k, $v, $wk, $wv, $ln) {
        $this->SetFont('Arial', '', 9);  $this->SetTextColor(100,116,139);
        $this->Cell($wk, 6.5, u($k), 0, 0, 'L');
        $this->SetFont('Arial', 'B', 9); $this->SetTextColor(30,41,59);
        $this->Cell($wv, 6.5, u($v), 0, $ln, 'L');
    }
}

$pdf = new ECPDF('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(true, 16);
$pdf->AddPage();

// ── Bloque de datos del cliente ──────────────────────────────────────────────
$pdf->SetFillColor(248, 250, 252);
$pdf->Rect(10, $pdf->GetY(), $pdf->GetPageWidth() - 20, 26, 'F');
$pdf->SetXY(13, $pdf->GetY() + 2.5);
$pdf->kv('Cliente:', $d['cliente'], 22, 75, 0);
$pdf->kv('Asesor:', $d['asesor'], 20, 60, 1);
$pdf->SetX(13);
$pdf->kv('Tipo de Crédito:', $d['credito'] ?: '-', 32, 65, 0);
$pdf->kv('Financiera:', $d['financiera_cred'] ?: '-', 24, 55, 1);
$pdf->SetX(13);
$pdf->kv('Monto financiación:', ars($d['monto_cred']), 38, 60, 1);
$pdf->Ln(4);

// ── Resumen de montos (3 cajas) ──────────────────────────────────────────────
$y = $pdf->GetY();
$wbox = ($pdf->GetPageWidth() - 20 - 8) / 3;
$cajas = [
    ['Monto Operación', ars($d['monto_operacion']), [241,245,249], [30,41,59]],
    ['Pagado',          ars($d['pagado']),          [209,250,229], [4,120,87]],
    ['A cancelar',      ars($d['a_cancelar']),      $d['a_cancelar']>0?[254,243,199]:[209,250,229], $d['a_cancelar']>0?[180,83,9]:[4,120,87]],
];
$x = 10;
foreach ($cajas as $c) {
    $pdf->SetFillColor($c[2][0],$c[2][1],$c[2][2]);
    $pdf->Rect($x, $y, $wbox, 16, 'F');
    $pdf->SetXY($x+3, $y+2.5);
    $pdf->SetFont('Arial','',8); $pdf->SetTextColor(71,85,105);
    $pdf->Cell($wbox-6, 4, u($c[0]), 0, 2, 'L');
    $pdf->SetFont('Arial','B',12); $pdf->SetTextColor($c[3][0],$c[3][1],$c[3][2]);
    $pdf->Cell($wbox-6, 7, u($c[1]), 0, 0, 'L');
    $x += $wbox + 4;
}
$pdf->SetY($y + 16 + 5);

// ── Tabla de pagos ───────────────────────────────────────────────────────────
$cols = [['Nro',12,'L'],['Fecha',20,'C'],['Tipo',22,'L'],['Modo',24,'L'],['Financiera',24,'L'],['Recibo',22,'L'],['Monto',28,'R'],['Observación',38,'L']];
$pdf->SetFont('Arial','B',8); $pdf->SetFillColor(29,78,216); $pdf->SetTextColor(255,255,255); $pdf->SetDrawColor(29,78,216);
foreach ($cols as $c) $pdf->Cell($c[1], 7.5, u($c[0]), 1, 0, 'C', true);
$pdf->Ln();

$pdf->SetFont('Arial','',8); $pdf->SetDrawColor(226,232,240);
$fill = false; $total = 0;
foreach ($d['pagos'] as $p) {
    $total += $p['monto'];
    $pdf->SetFillColor(248,250,252); $pdf->SetTextColor(30,41,59);
    $cells = [$p['idpago'], ec_fecha($p['fecha']), $p['tipo'], $p['modo'], $p['financiera'], $p['nrorecibo'], ars($p['monto']), $p['obs']];
    foreach ($cols as $i => $c) {
        if ($i === 6) $pdf->SetFont('Arial','B',8); else $pdf->SetFont('Arial','',8);
        $pdf->Cell($c[1], 6, $pdf->fit($cells[$i], $c[1]), 'B', 0, $c[2], $fill);
    }
    $pdf->Ln(); $fill = !$fill;
}
if (count($d['pagos']) === 0) {
    $pdf->SetTextColor(148,163,184);
    $pdf->Cell(array_sum(array_column($cols,1)), 8, u('Sin pagos registrados.'), 'B', 1, 'C');
}

// Total
$pdf->SetFont('Arial','B',9); $pdf->SetFillColor(219,234,254); $pdf->SetTextColor(30,58,95); $pdf->SetDrawColor(29,78,216);
$pdf->Cell(12+20+22+24+24+22, 8, u('TOTAL PAGADO'), 'T', 0, 'R', true);
$pdf->Cell(28, 8, ars($total), 'T', 0, 'R', true);
$pdf->Cell(38, 8, '', 'T', 1, 'L', true);

if (ob_get_length()) ob_end_clean();
$pdf->Output('I', 'estado_cuenta_'.$idcliente.'_'.date('Y-m-d').'.pdf');
