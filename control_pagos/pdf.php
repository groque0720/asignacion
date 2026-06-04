<?php
/*
 * Exporta a PDF (apaisado, para imprimir) el resultado del filtro actual.
 * Reutiliza la misma lógica de filtro/cálculo que data.php (vía _consulta.php).
 */
// FPDF es viejo: sus deprecations/notices romperían el binario. Silenciar + bufferizar.
error_reporting(E_ERROR | E_PARSE);
@ini_set('display_errors', '0');
ob_start();
@session_start();
include("funciones/func_mysql.php");
include("_consulta.php");
conectar();
mysqli_query($con, "SET NAMES 'utf8'");
require('../asignacion/fpdf/fpdf.php');

if (!isset($_SESSION["autentificado"]) || $_SESSION["autentificado"] !== "SI") {
    header("Location: ../login");
    exit();
}

list($W, $orderBy) = cp_where($con);
list($rows, $err)  = cp_fetch_todo($con, $W, $orderBy);
if ($err) { die("Error al generar PDF: ".$err); }

function u($s)   { return utf8_decode((string)$s); }                 // UTF-8 -> latin1 (FPDF)
function ars($n) { return '$ '.number_format((float)$n, 0, ',', '.'); }
function demorado($llego) {                                          // arribó hace +10 días
    if (!$llego || $llego === '0000-00-00') return false;
    return floor((time() - strtotime($llego)) / 86400) > 10;
}

// Columnas: [clave, título, ancho mm, alineación]
$cols = [
    ['nr',     'N.R.',        13, 'L'],
    ['nu',     'N.U.',        13, 'L'],
    ['int',    'Interno',     13, 'L'],
    ['orden',  'Nro Orden',   19, 'L'],
    ['asesor', 'Asesor',      24, 'L'],
    ['cli',    'Cliente',     40, 'L'],
    ['mod',    'Modelo',      38, 'L'],
    ['saldo',  'Saldo',       25, 'R'],
    ['fres',   'Fec.Res.',    16, 'C'],
    ['llego',  'Llegó',       16, 'C'],
    ['canc',   'Cancela',     16, 'C'],
    ['obs',    'Observación', 48, 'L'],
];

class CPPDF extends FPDF {
    public $cols = [];
    public $subSuc = '', $subEst = '', $subOps = '';

    function Header() {
        $w = $this->GetPageWidth() - 16;  // ancho útil (márgenes 8)

        // ── Banda de título (slate-900)
        $this->SetFillColor(15, 23, 42);
        $this->Rect(8, 8, $w, 15, 'F');
        $this->SetTextColor(255, 255, 255);
        $this->SetXY(11, 10);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(160, 7, u('CONTROL DE PAGOS'), 0, 2, 'L');
        $this->SetFont('Arial', '', 8.5);
        $this->SetTextColor(148, 163, 184);
        $this->Cell(160, 5, u('Derka y Vargas S.A.'), 0, 0, 'L');
        // fecha de generación (derecha)
        $this->SetXY(8, 12.5);
        $this->SetTextColor(203, 213, 225);
        $this->SetFont('Arial', '', 8.5);
        $this->Cell($w - 3, 5, u('Generado: '.date('d/m/Y  H:i')), 0, 0, 'R');

        // ── Sub-banda de contexto (slate-100)
        $this->SetFillColor(241, 245, 249);
        $this->Rect(8, 24.5, $w, 7, 'F');
        $this->SetXY(11, 26);
        $this->_kv('Sucursal: ', $this->subSuc, 4);
        $this->_kv('   |   Estado: ', $this->subEst, 4);
        $this->_kv('   |   Operaciones: ', $this->subOps, 4);

        // ── Cabecera de columnas (azul)
        $this->SetXY(8, 33.5);
        $this->SetFillColor(29, 78, 216);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 6.5);
        $this->SetDrawColor(29, 78, 216);
        foreach ($this->cols as $c) {
            $this->Cell($c[2], 7.5, u($c[1]), 1, 0, 'C', true);
        }
        $this->Ln();
        $this->SetTextColor(30, 41, 59);
        $this->SetDrawColor(226, 232, 240);  // líneas suaves para el cuerpo
    }

    // Imprime "label" normal + "valor" en negrita, en línea
    function _kv($label, $valor, $h) {
        $this->SetFont('Arial', '', 8.5);
        $this->SetTextColor(100, 116, 139);
        $this->Cell($this->GetStringWidth(u($label)), $h, u($label), 0, 0, 'L');
        $this->SetFont('Arial', 'B', 8.5);
        $this->SetTextColor(30, 41, 59);
        $this->Cell($this->GetStringWidth(u($valor)) + 1, $h, u($valor), 0, 0, 'L');
    }

    function Footer() {
        $this->SetY(-11);
        $this->SetDrawColor(226, 232, 240);
        $this->Line(8, $this->GetY(), $this->GetPageWidth() - 8, $this->GetY());
        $this->SetY(-10);
        $this->SetFont('Arial', 'I', 7.5);
        $this->SetTextColor(148, 163, 184);
        $this->Cell(100, 6, u('Derka y Vargas S.A.  ·  Control de Pagos'), 0, 0, 'L');
        $this->Cell($this->GetPageWidth() - 16 - 100, 6, u('Página '.$this->PageNo().' de {nb}'), 0, 0, 'R');
    }

    function fit($txt, $w) {
        $txt = u($txt);
        if ($this->GetStringWidth($txt) <= $w - 2) return $txt;
        while (strlen($txt) > 1 && $this->GetStringWidth($txt.'..') > $w - 2) {
            $txt = substr($txt, 0, -1);
        }
        return $txt.'..';
    }
}

$pdf = new CPPDF('L', 'mm', 'A4');
$pdf->cols   = $cols;
$pdf->subSuc = cp_sucursal_nombre();
$pdf->subEst = cp_estado_nombre();
$pdf->subOps = (string)count($rows);
$pdf->AliasNbPages();
$pdf->SetMargins(8, 8, 8);
$pdf->SetAutoPageBreak(true, 14);
$pdf->AddPage();

$pdf->SetFont('Arial', '', 6.5);
$fill   = false;
$tSaldo = 0.0;

foreach ($rows as $r) {
    $tSaldo += $r['saldo'];
    $pdf->SetFillColor(248, 250, 252);   // zebra slate-50

    foreach ($cols as $c) {
        switch ($c[0]) {
            case 'nr':     $val = $r['idreserva']; break;
            case 'nu':     $val = $r['nrounidad']; break;
            case 'int':    $val = $r['interno']; break;
            case 'orden':  $val = $r['nroorden']; break;
            case 'asesor': $val = $r['asesor']; break;
            case 'cli':    $val = $r['cliente']; break;
            case 'mod':    $val = $r['modelo_txt']; break;
            case 'saldo':  $val = ars($r['saldo']); break;
            case 'fres':   $val = cp_fecha($r['fecres']); break;
            case 'llego':  $val = cp_fecha($r['llego']); break;
            case 'canc':   $val = cp_fecha($r['fechacanc']); break;
            case 'obs':    $val = $r['obs']; break;
            default:       $val = '';
        }

        // estilos por columna
        if ($c[0] === 'saldo') {
            $pdf->SetFont('Arial', 'B', 6.5);
            $pdf->SetTextColor($r['saldo'] == 0 ? 4 : 30, $r['saldo'] == 0 ? 120 : 41, $r['saldo'] == 0 ? 87 : 59);
        } elseif ($c[0] === 'llego' && demorado($r['llego'])) {
            $pdf->SetFont('Arial', 'B', 6.5);
            $pdf->SetTextColor(185, 28, 28);   // rojo demora
        } else {
            $pdf->SetFont('Arial', '', 6.5);
            $pdf->SetTextColor(30, 41, 59);
        }

        $pdf->Cell($c[2], 6, $pdf->fit($val, $c[2]), 'B', 0, $c[3], $fill);
    }
    $pdf->Ln();
    $fill = !$fill;
}

// ── Fila de totales
$pdf->SetFont('Arial', 'B', 8.5);
$pdf->SetFillColor(219, 234, 254);
$pdf->SetTextColor(30, 58, 95);
$pdf->SetDrawColor(29, 78, 216);
$wAntesSaldo = 13 + 13 + 13 + 19 + 24 + 40 + 38;
$pdf->Cell($wAntesSaldo, 8, u('TOTAL SALDO  ('.count($rows).' operaciones)'), 'T', 0, 'R', true);
$pdf->Cell(25, 8, ars($tSaldo), 'T', 0, 'R', true);
$pdf->Cell(16 + 16 + 16 + 48, 8, '', 'T', 1, 'L', true);

if (ob_get_length()) ob_end_clean();   // descarta cualquier aviso suelto antes del binario
$pdf->Output('I', 'control_pagos_'.date('Y-m-d_His').'.pdf');
