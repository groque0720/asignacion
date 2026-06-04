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

function u($s)        { return utf8_decode((string)$s); }                    // UTF-8 -> latin1 (FPDF)
function ars($n)      { return '$ '.number_format((float)$n, 0, ',', '.'); }

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
    ['obs',    'Observación', 44, 'L'],
];

class CPPDF extends FPDF {
    public $cols = [];
    public $sub  = '';

    function Header() {
        // Barra de título
        $this->SetFillColor(15, 23, 42);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 13);
        $this->Cell(0, 9, u('Control de Pagos — Derka y Vargas S.A.'), 0, 1, 'L', true);
        // Subtítulo (filtro + fecha)
        $this->SetFont('Arial', '', 8);
        $this->SetFillColor(30, 41, 59);
        $this->Cell(0, 6, u($this->sub), 0, 1, 'L', true);
        $this->Ln(1.5);
        // Cabecera de columnas
        $this->SetFillColor(29, 78, 216);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 7.5);
        foreach ($this->cols as $c) {
            $this->Cell($c[2], 7, u($c[1]), 1, 0, 'C', true);
        }
        $this->Ln();
        $this->SetTextColor(20, 20, 20);
    }

    function Footer() {
        $this->SetY(-11);
        $this->SetFont('Arial', 'I', 7);
        $this->SetTextColor(120, 120, 120);
        $this->Cell(0, 6, u('Página '.$this->PageNo().' de {nb}'), 0, 0, 'C');
    }

    // Trunca un texto para que entre en el ancho dado
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
$pdf->cols = $cols;
$pdf->sub  = 'Sucursal: '.cp_sucursal_nombre().'   |   Estado: '.cp_estado_nombre().
             '   |   Operaciones: '.count($rows).'   |   '.date('d/m/Y H:i');
$pdf->AliasNbPages();
$pdf->SetMargins(8, 8, 8);
$pdf->SetAutoPageBreak(true, 14);
$pdf->AddPage();

$pdf->SetFont('Arial', '', 7.5);
$fill = false;
$tSaldo = 0.0;
foreach ($rows as $r) {
    $tSaldo += $r['saldo'];
    $pdf->SetFillColor(243, 246, 250);

    $cells = [
        ['nr',     $r['idreserva']],
        ['nu',     $r['nrounidad']],
        ['int',    $r['interno']],
        ['orden',  $r['nroorden']],
        ['asesor', $r['asesor']],
        ['cli',    $r['cliente']],
        ['mod',    $r['modelo_txt']],
        ['saldo',  ars($r['saldo'])],
        ['fres',   cp_fecha($r['fecres'])],
        ['llego',  cp_fecha($r['llego'])],
        ['canc',   cp_fecha($r['fechacanc'])],
        ['obs',    $r['obs']],
    ];
    foreach ($cells as $i => $cell) {
        $c = $cols[$i];
        $pdf->Cell($c[2], 6, $pdf->fit($cell[1], $c[2]), 'LR', 0, $c[3], $fill);
    }
    $pdf->Ln();
    $fill = !$fill;
}

// Fila de totales
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetFillColor(219, 234, 254);
$wAntesSaldo = 13 + 13 + 13 + 19 + 24 + 40 + 38;   // ancho hasta antes de Saldo
$pdf->Cell($wAntesSaldo, 7, u('TOTALES ('.count($rows).' op.)'), 1, 0, 'R', true);
$pdf->Cell(25, 7, ars($tSaldo), 1, 0, 'R', true);
$pdf->Cell(16 + 16 + 16 + 44, 7, '', 1, 1, 'L', true);

if (ob_get_length()) ob_end_clean();   // descarta cualquier aviso suelto antes del binario
$pdf->Output('I', 'control_pagos_'.date('Y-m-d_His').'.pdf');
