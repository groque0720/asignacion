<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("funciones/func_mysql.php");
require('fpdf/fpdf.php');

$con = conectar();
mysqli_query($con,"SET NAMES 'utf8'");

session_start();
if (!isset($_SESSION["autentificado"]) || $_SESSION["autentificado"] != "SI") {
    header("Location: ../login");
    exit();
}

/* ===========================
   SUCURSAL
=========================== */

$sucursalId = isset($_GET['sucursalId']) ? intval($_GET['sucursalId']) : null;
$sucursalNombre = 'DERKA Y VARGAS S. A.';

if ($sucursalId) {
    $sql = "SELECT sucursal FROM sucursales WHERE idsucursal = $sucursalId";
    $res = mysqli_query($con, $sql);
    if ($res && $row = mysqli_fetch_assoc($res)) {
        $sucursalNombre = $row['sucursal'];
    }
}

/* ===========================
   CLASE PDF
=========================== */

class PDF extends FPDF
{
    private $sucursal;

    function __construct($sucursal) {
        parent::__construct('P','mm','A4');
        $this->sucursal = $sucursal;
    }

    function Header()
    {
        $this->SetFont('Arial','B',11);
        $this->Cell(0,6,'DERKA Y VARGAS S. A.',0,1,'L');

        $this->SetFont('Arial','B',10);
        $this->Cell(0,6,'COSTOS Y RECURSOS - '.strtoupper($this->sucursal),0,1,'C');

        $this->SetFont('Arial','',8);
        $this->Cell(0,5,date('d/m/Y H:i'),0,1,'R');

        $this->Ln(3);
    }

    function Footer()
    {
        $this->SetY(-12);
        $this->SetFont('Arial','',8);
        $this->Cell(0,5,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
    }
}

/* ===========================
   CONSULTAS
=========================== */

$queries = [
    'pendiente' => "SELECT * FROM view_asignaciones_saldo_pendiente_corregida_no_llegadas",
    'viaje'     => "SELECT * FROM view_asignaciones_saldo_pendiente_corregida_en_viaje",
    'llegadas'  => "SELECT * FROM view_asignaciones_saldo_pendiente_corregida_llegadas"
];

$matriz = [];
$totales = [
    'pendiente' => 0,
    'viaje' => 0,
    'llegadas' => 0,
    'general' => 0
];

foreach ($queries as $tipo => $sql) {

    $res = mysqli_query($con, $sql);

    if (!$res) continue;

    while ($row = mysqli_fetch_assoc($res)) {

        $id = $row['IdSucursal'];

        if (!isset($matriz[$id])) {
            $matriz[$id] = [
                'Sucursal' => $row['Sucursal'],
                'pendiente' => 0,
                'viaje' => 0,
                'llegadas' => 0
            ];
        }

        $saldo = floatval($row['Saldo']);

        $matriz[$id][$tipo] = $saldo;
        $totales[$tipo] += $saldo;
        $totales['general'] += $saldo;
    }
}

/* Ordenar por nombre sucursal */
usort($matriz, function($a, $b) {
    return strcmp($a['Sucursal'], $b['Sucursal']);
});

/* ===========================
   CREAR PDF
=========================== */

$pdf = new PDF($sucursalNombre);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true,10);

/* ===========================
   TABLA
=========================== */

$pdf->SetFont('Arial','',9);
$pdf->SetFillColor(230,230,230);

$pdf->Cell(70,8,'Sucursal',1,0,'C',true);
$pdf->Cell(30,8,'Pendiente',1,0,'C',true);
$pdf->Cell(30,8,'En Viaje',1,0,'C',true);
$pdf->Cell(30,8,'Llegadas',1,0,'C',true);
$pdf->Cell(30,8,'Total',1,1,'C',true);

$pdf->SetFont('Arial','',9);

if (empty($matriz)) {

    $pdf->Cell(190,10,'No hay datos para mostrar',1,1,'C');

} else {

    foreach ($matriz as $data) {

        $pendiente = $data['pendiente'] ?? 0;
        $viaje = $data['viaje'] ?? 0;
        $llegadas = $data['llegadas'] ?? 0;

        $totalSucursal = $pendiente + $viaje + $llegadas;

        $pdf->Cell(70,7,utf8_decode($data['Sucursal']),1);
        $pdf->Cell(30,7,number_format($pendiente,0,',','.'),1,0,'R');
        $pdf->Cell(30,7,number_format($viaje,0,',','.'),1,0,'R');
        $pdf->Cell(30,7,number_format($llegadas,0,',','.'),1,0,'R');
        $pdf->Cell(30,7,number_format($totalSucursal,0,',','.'),1,1,'R');
    }
}

/* ===========================
   TOTALES
=========================== */

$pdf->SetFont('Arial','',9);
$pdf->SetFillColor(200,200,200);

$pdf->Cell(70,8,'TOTAL GENERAL',1,0,'C',true);
$pdf->Cell(30,8,number_format($totales['pendiente'],0,',','.'),1,0,'R',true);
$pdf->Cell(30,8,number_format($totales['viaje'],0,',','.'),1,0,'R',true);
$pdf->Cell(30,8,number_format($totales['llegadas'],0,',','.'),1,0,'R',true);
$pdf->Cell(30,8,number_format($totales['general'],0,',','.'),1,1,'R',true);

/* ===========================
   OUTPUT
=========================== */

ob_end_clean();
$pdf->Output('Costos_recursos_resumen.pdf','I');
exit();
?>
