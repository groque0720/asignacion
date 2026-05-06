<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../funciones/func_mysql.php');
require('../../asignacion/fpdf/fpdf.php');

conectar();
@session_start();
if (!isset($_SESSION['autentificado']) || $_SESSION['autentificado'] !== 'SI') {
    header('Location: ../../login');
    exit();
}
mysqli_query($con, "SET NAMES 'utf8'");

$sucursalId = isset($_GET['sucursalId']) ? intval($_GET['sucursalId']) : 0;
$whereSuc   = $sucursalId > 0 ? ' WHERE IdSucursal = ' . $sucursalId : '';
$andSuc     = $sucursalId > 0 ? ' AND v.idsucursal = ' . $sucursalId : '';

function f_ars($n) {
    return '$ ' . number_format((float)$n, 0, ',', '.');
}
function f_txt_cut($s, $max) {
    $s = (string)$s;
    if (strlen($s) <= $max) return $s;
    return substr($s, 0, $max - 2) . '..';
}
function f_date($d) {
    if (!$d || $d === '0000-00-00') return '-';
    $p = explode('-', $d);
    if (count($p) === 3) return $p[2] . '/' . $p[1] . '/' . $p[0];
    return $d;
}
function f_pct($part, $total) {
    if ($total <= 0) return '0,0%';
    return number_format(($part / $total) * 100, 1, ',', '.') . '%';
}

class CostoArriboPDF extends FPDF {
    public $titleText = 'COSTO DE UNIDADES CON ARRIBO';

    function Header() {
        $this->SetFillColor(30, 41, 59);
        $this->Rect(0, 0, $this->GetPageWidth(), 14, 'F');

        $this->SetTextColor(255, 255, 255);
        $this->SetY(4);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 4, 'DERKA Y VARGAS S.A. - ' . $this->titleText, 0, 1, 'C');

        $this->SetFont('Arial', '', 7);
        $this->Cell(0, 4, 'Emitido: ' . date('d/m/Y H:i:s'), 0, 1, 'C');

        $this->SetTextColor(0, 0, 0);
        $this->SetY(18);
    }

    function Footer() {
        $this->SetY(-10);
        $this->SetTextColor(120, 120, 120);
        $this->SetFont('Arial', 'I', 7);
        $this->Cell(0, 4, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

function detail_header($pdf) {
    $pdf->SetFont('Arial', 'B', 7.5);
    $pdf->SetFillColor(30, 41, 59);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(11, 5.5, 'NroUn', 0, 0, 'C', true);
    $pdf->Cell(48, 5.5, utf8_decode('Modelo / Version'), 0, 0, 'L', true);
    $pdf->Cell(17, 5.5, 'Orden', 0, 0, 'C', true);
    $pdf->Cell(33, 5.5, 'Chasis', 0, 0, 'L', true);
    $pdf->Cell(38, 5.5, 'Cliente', 0, 0, 'L', true);
    $pdf->Cell(28, 5.5, 'Asesor', 0, 0, 'L', true);
    $pdf->Cell(16, 5.5, 'Reserva', 0, 0, 'C', true);
    $pdf->Cell(16, 5.5, 'Arribo', 0, 0, 'C', true);
    $pdf->Cell(18, 5.5, 'Estado Res.', 0, 0, 'C', true);
    $pdf->Cell(26, 5.5, 'Costo', 0, 0, 'R', true);
    $pdf->Cell(26, 5.5, 'Saldo', 0, 1, 'R', true);
    $pdf->SetTextColor(15, 23, 42);
}

function sucursal_header($pdf, $nombre) {
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetFillColor(220, 226, 240);
    $pdf->SetTextColor(30, 41, 59);
    $pdf->Cell(277, 5.5, utf8_decode('Sucursal: ' . $nombre), 0, 1, 'L', true);
    $pdf->SetTextColor(15, 23, 42);
}

function sucursal_subtotal($pdf, $nombre, $unidRes, $unid, $costo, $saldo) {
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->SetFillColor(241, 245, 249);
    $pdf->SetTextColor(15, 23, 42);
    $label = '   Subtotal ' . $nombre . '  (' . $unidRes . ' c/reserva / ' . $unid . ' total)';
    $pdf->Cell(225, 5.5, utf8_decode($label), 0, 0, 'L', true);
    $pdf->Cell(26, 5.5, f_ars($costo), 0, 0, 'R', true);
    $pdf->Cell(26, 5.5, f_ars($saldo), 0, 1, 'R', true);
}

// Nombre de sucursal cuando filtra
$nombreSuc = '';
if ($sucursalId > 0) {
    $resN = mysqli_query($con, 'SELECT sucursal FROM sucursales WHERE idsucursal = ' . $sucursalId);
    if ($resN && ($r = mysqli_fetch_assoc($resN))) {
        $nombreSuc = $r['sucursal'];
    }
}

// ─── Resumen por sucursal (vista hija ya agrega Costo / CostoConReserva / etc.) ──
$resumen = array();
$totCosto       = 0;
$totCostoRes    = 0;
$totCostoSinRes = 0;
$totUnid        = 0;
$totUnidRes     = 0;
$totSaldo       = 0;

$qSum = 'SELECT * FROM view_asignaciones_saldo_pendiente_corregida_llegadas' . $whereSuc;
$res  = mysqli_query($con, $qSum);
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) {
        $resumen[]       = $r;
        $totCosto       += (float)$r['Costo'];
        $totCostoRes    += (float)$r['CostoConReserva'];
        $totCostoSinRes += (float)$r['CostoSinReserva'];
        $totUnid        += (int)$r['Unidades'];
        $totUnidRes     += (int)$r['UnidadesConReserva'];
        $totSaldo       += (float)$r['Saldo'];
    }
}
usort($resumen, function($a, $b) { return strcmp($a['Sucursal'], $b['Sucursal']); });

// ─── Detalle unitario (vista maestra + JOIN para estado_reserva) ─────────────
$qDet = "SELECT
  v.`NroUn.`        AS NroUn,
  v.Mes,
  v.`Año`           AS Anio,
  v.Modelo,
  v.`Versión`       AS Version,
  v.NroOrden,
  v.Interno,
  v.Chasis,
  v.Cliente,
  v.Asesor,
  v.Sucursal,
  v.idsucursal,
  v.Reserva,
  v.Arribo,
  v.`Costo TASA`    AS Costo,
  v.Saldo,
  COALESCE(a.estado_reserva, 0) AS EstadoReserva
FROM view_asignaciones_saldo_pendiente_corregida v
LEFT JOIN asignaciones a
  ON a.nro_orden  = v.NroOrden
 AND a.nro_unidad = v.`NroUn.`
 AND a.guardado   = 1
 AND a.borrar     = 0
 AND a.entregada  = 0
WHERE v.Arribo IS NOT NULL AND v.Arribo <> ''" . $andSuc . "
ORDER BY v.Sucursal, v.Modelo, v.`Versión`, v.Arribo";
$resDet = mysqli_query($con, $qDet);

// ═══ PDF ═════════════════════════════════════════════════════════════════════
$pdf = new CostoArriboPDF('L', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 12);

// Encabezado contextual
if ($sucursalId > 0 && $nombreSuc !== '') {
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetTextColor(51, 65, 85);
    $pdf->Cell(0, 5, utf8_decode('Sucursal: ' . $nombreSuc), 0, 1, 'L');
    $pdf->Ln(1);
}

// ─── KPIs ─────────────────────────────────────────────────────────────────────
$pctRes     = $totCosto > 0 ? round($totCostoRes / $totCosto * 100, 1) : 0;
$pctSinRes  = $totCosto > 0 ? round($totCostoSinRes / $totCosto * 100, 1) : 0;
$unidSinRes = max(0, $totUnid - $totUnidRes);

$kpiY = $pdf->GetY();
$pdf->SetDrawColor(226, 232, 240);
$pdf->SetFillColor(248, 250, 252);
$pdf->Rect(10, $kpiY, 92, 18, 'DF');
$pdf->SetFillColor(236, 253, 245);
$pdf->Rect(102, $kpiY, 92, 18, 'DF');
$pdf->SetFillColor(255, 251, 235);
$pdf->Rect(194, $kpiY, 93, 18, 'DF');

$pdf->SetFont('Arial', 'B', 7);
$pdf->SetTextColor(100, 116, 139);
$pdf->SetXY(12, $kpiY + 2);
$pdf->Cell(88, 4, 'COSTO TOTAL', 0, 0, 'L');
$pdf->SetTextColor(5, 122, 85);
$pdf->SetXY(104, $kpiY + 2);
$pdf->Cell(88, 4, 'CON RESERVA CONFIRMADA', 0, 0, 'L');
$pdf->SetTextColor(180, 83, 9);
$pdf->SetXY(196, $kpiY + 2);
$pdf->Cell(88, 4, 'SIN RESERVA', 0, 1, 'L');

$pdf->SetFont('Arial', 'B', 13);
$pdf->SetTextColor(15, 23, 42);
$pdf->SetXY(12, $kpiY + 7);
$pdf->Cell(88, 7, f_ars($totCosto), 0, 0, 'L');
$pdf->SetTextColor(5, 122, 85);
$pdf->SetXY(104, $kpiY + 7);
$pdf->Cell(88, 7, f_ars($totCostoRes), 0, 0, 'L');
$pdf->SetTextColor(180, 83, 9);
$pdf->SetXY(196, $kpiY + 7);
$pdf->Cell(88, 7, f_ars($totCostoSinRes), 0, 1, 'L');

$pdf->SetFont('Arial', '', 7);
$pdf->SetTextColor(100, 116, 139);
$pdf->SetXY(12, $kpiY + 14);
$pdf->Cell(88, 4, $totUnid . ' unidades', 0, 0, 'L');
$pdf->SetTextColor(5, 122, 85);
$pdf->SetXY(104, $kpiY + 14);
$pdf->Cell(88, 4, $totUnidRes . ' unid.  ' . number_format($pctRes, 1, ',', '.') . '%', 0, 0, 'L');
$pdf->SetTextColor(180, 83, 9);
$pdf->SetXY(196, $kpiY + 14);
$pdf->Cell(88, 4, $unidSinRes . ' unid.  ' . number_format($pctSinRes, 1, ',', '.') . '%', 0, 1, 'L');

$pdf->SetTextColor(15, 23, 42);
$pdf->SetY($kpiY + 22);

// ─── Tabla resumen por sucursal ──────────────────────────────────────────────
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetFillColor(30, 41, 59);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(70, 6, 'Sucursal', 0, 0, 'L', true);
$pdf->Cell(40, 6, 'Costo Total', 0, 0, 'R', true);
$pdf->Cell(40, 6, 'Con Reserva', 0, 0, 'R', true);
$pdf->Cell(40, 6, 'Sin Reserva', 0, 0, 'R', true);
$pdf->Cell(34, 6, 'Unid. (Res/Tot)', 0, 0, 'C', true);
$pdf->Cell(20, 6, '% Res.', 0, 0, 'R', true);
$pdf->Cell(33, 6, 'Saldo TASA', 0, 1, 'R', true);

$pdf->SetTextColor(15, 23, 42);
$pdf->SetFont('Arial', '', 7.5);
$fill = false;
foreach ($resumen as $r) {
    $fill = !$fill;
    $pdf->SetFillColor($fill ? 248 : 255, $fill ? 250 : 255, $fill ? 252 : 255);
    $cs    = (float)$r['Costo'];
    $cr    = (float)$r['CostoConReserva'];
    $csr   = (float)$r['CostoSinReserva'];
    $ut    = (int)$r['Unidades'];
    $ur    = (int)$r['UnidadesConReserva'];
    $sal   = (float)$r['Saldo'];
    $pctR  = $cs > 0 ? round($cr / $cs * 100, 1) : 0;

    $pdf->SetTextColor(15, 23, 42);
    $pdf->Cell(70, 5.5, utf8_decode(f_txt_cut($r['Sucursal'], 30)), 0, 0, 'L', true);
    $pdf->Cell(40, 5.5, f_ars($cs), 0, 0, 'R', true);
    $pdf->SetTextColor(5, 122, 85);
    $pdf->Cell(40, 5.5, f_ars($cr), 0, 0, 'R', true);
    $pdf->SetTextColor(180, 83, 9);
    $pdf->Cell(40, 5.5, f_ars($csr), 0, 0, 'R', true);
    $pdf->SetTextColor(15, 23, 42);
    $pdf->Cell(34, 5.5, $ur . ' / ' . $ut, 0, 0, 'C', true);
    $pdf->Cell(20, 5.5, number_format($pctR, 1, ',', '.') . '%', 0, 0, 'R', true);
    $pdf->Cell(33, 5.5, f_ars($sal), 0, 1, 'R', true);
}

// Total
$pdf->SetFillColor(226, 232, 240);
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetTextColor(15, 23, 42);
$pdf->Cell(70, 6, 'TOTAL DyV', 0, 0, 'L', true);
$pdf->Cell(40, 6, f_ars($totCosto), 0, 0, 'R', true);
$pdf->SetTextColor(5, 122, 85);
$pdf->Cell(40, 6, f_ars($totCostoRes), 0, 0, 'R', true);
$pdf->SetTextColor(180, 83, 9);
$pdf->Cell(40, 6, f_ars($totCostoSinRes), 0, 0, 'R', true);
$pdf->SetTextColor(15, 23, 42);
$pdf->Cell(34, 6, $totUnidRes . ' / ' . $totUnid, 0, 0, 'C', true);
$pdf->Cell(20, 6, number_format($pctRes, 1, ',', '.') . '%', 0, 0, 'R', true);
$pdf->Cell(33, 6, f_ars($totSaldo), 0, 1, 'R', true);

// ─── Detalle unitario ────────────────────────────────────────────────────────
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor(30, 41, 59);
$pdf->Cell(0, 6, utf8_decode('Detalle por unidad'), 0, 1, 'L');
$pdf->Ln(1);

detail_header($pdf);
$pdf->SetFont('Arial', '', 6.6);
$pdf->SetTextColor(15, 23, 42);

$line        = false;
$currSuc     = '';
$sucCosto    = 0;
$sucSaldo    = 0;
$sucUnid     = 0;
$sucUnidRes  = 0;
$grandCosto  = 0;
$grandSaldo  = 0;
$pageBreakLimit = 192; // y-limit dentro de A4 L (210mm de alto) con AutoPageBreak

if ($resDet) {
    while ($u = mysqli_fetch_assoc($resDet)) {
        // Cambio de sucursal: cierro la anterior y abro la nueva
        if ($u['Sucursal'] !== $currSuc) {
            if ($currSuc !== '') {
                if ($pdf->GetY() > $pageBreakLimit - 6) {
                    $pdf->AddPage();
                    detail_header($pdf);
                }
                sucursal_subtotal($pdf, $currSuc, $sucUnidRes, $sucUnid, $sucCosto, $sucSaldo);
                $pdf->Ln(1);
            }
            $currSuc    = $u['Sucursal'];
            $sucCosto   = 0;
            $sucSaldo   = 0;
            $sucUnid    = 0;
            $sucUnidRes = 0;

            if ($pdf->GetY() > $pageBreakLimit - 12) {
                $pdf->AddPage();
                detail_header($pdf);
            }
            sucursal_header($pdf, $currSuc);
            $pdf->SetFont('Arial', '', 6.6);
            $line = false;
        }

        // Salto de página si la fila no entra
        if ($pdf->GetY() > $pageBreakLimit) {
            $pdf->AddPage();
            detail_header($pdf);
            sucursal_header($pdf, $currSuc . ' (cont.)');
            $pdf->SetFont('Arial', '', 6.6);
        }

        $line = !$line;
        $pdf->SetFillColor($line ? 248 : 255, $line ? 250 : 255, $line ? 252 : 255);
        $pdf->SetTextColor(15, 23, 42);

        $modelo  = trim($u['Modelo'] . ' ' . $u['Version']);
        $modelo  = f_txt_cut($modelo, 35);
        $cliente = f_txt_cut($u['Cliente'], 26);
        $asesor  = f_txt_cut($u['Asesor'], 19);
        $chasis  = f_txt_cut($u['Chasis'], 22);
        $estRes  = ((int)$u['EstadoReserva'] === 1) ? 'CONFIRMADA' : 'SIN RESERVA';
        $costo   = (float)$u['Costo'];
        $saldo   = (float)$u['Saldo'];

        $sucCosto   += $costo;
        $sucSaldo   += $saldo;
        $sucUnid    += 1;
        if ((int)$u['EstadoReserva'] === 1) $sucUnidRes += 1;
        $grandCosto += $costo;
        $grandSaldo += $saldo;

        $pdf->Cell(11, 5, $u['NroUn'], 0, 0, 'C', true);
        $pdf->Cell(48, 5, utf8_decode($modelo), 0, 0, 'L', true);
        $pdf->Cell(17, 5, $u['NroOrden'], 0, 0, 'C', true);
        $pdf->Cell(33, 5, utf8_decode($chasis), 0, 0, 'L', true);
        $pdf->Cell(38, 5, utf8_decode($cliente), 0, 0, 'L', true);
        $pdf->Cell(28, 5, utf8_decode($asesor), 0, 0, 'L', true);
        $pdf->Cell(16, 5, f_date($u['Reserva']), 0, 0, 'C', true);
        $pdf->Cell(16, 5, f_date($u['Arribo']), 0, 0, 'C', true);

        if ((int)$u['EstadoReserva'] === 1) {
            $pdf->SetTextColor(5, 122, 85);
        } else {
            $pdf->SetTextColor(180, 83, 9);
        }
        $pdf->SetFont('Arial', 'B', 6.4);
        $pdf->Cell(18, 5, $estRes, 0, 0, 'C', true);
        $pdf->SetFont('Arial', '', 6.6);
        $pdf->SetTextColor(15, 23, 42);

        $pdf->Cell(26, 5, f_ars($costo), 0, 0, 'R', true);
        $pdf->Cell(26, 5, f_ars($saldo), 0, 1, 'R', true);
    }
    // Cierre de la última sucursal
    if ($currSuc !== '') {
        if ($pdf->GetY() > $pageBreakLimit - 6) {
            $pdf->AddPage();
            detail_header($pdf);
        }
        sucursal_subtotal($pdf, $currSuc, $sucUnidRes, $sucUnid, $sucCosto, $sucSaldo);
    }
}

// Total general detalle
$pdf->Ln(2);
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetFillColor(30, 41, 59);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(225, 6, 'TOTAL GENERAL', 0, 0, 'R', true);
$pdf->Cell(26, 6, f_ars($grandCosto), 0, 0, 'R', true);
$pdf->Cell(26, 6, f_ars($grandSaldo), 0, 1, 'R', true);

ob_end_clean();
$nombreArchivo = $sucursalId > 0
    ? 'Costo_Arribo_Suc' . $sucursalId . '.pdf'
    : 'Costo_Arribo.pdf';
$pdf->Output('I', $nombreArchivo);
exit();
?>
