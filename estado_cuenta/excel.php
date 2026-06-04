<?php
/*
 * Estado de Cuenta en Excel (.xlsx). Reutiliza _consulta.php.
 */
error_reporting(E_ERROR | E_PARSE);
@ini_set('display_errors', '0');
ob_start();
@session_start();
include("funciones/func_mysql.php");
include("_consulta.php");
conectar();
mysqli_query($con, "SET NAMES 'utf8'");
require_once '../asignacion/Classes/PHPExcel.php';

if (!isset($_SESSION["autentificado"]) || $_SESSION["autentificado"] !== "SI") {
    header("Location: ../login"); exit();
}
$idcliente = (int)($_GET['IDrecord'] ?? $_GET['idcliente'] ?? 0);
$d = ec_datos($con, $idcliente);
if ($d === null) { die("No se encontró estado de cuenta para el cliente."); }

$MONEY = '"$ "#,##0.00';
$xls = new PHPExcel();
$xls->getProperties()->setCreator('Sistema Asignación')->setTitle('Estado de Cuenta');
$s = $xls->getActiveSheet();
$s->setTitle('Estado de Cuenta');
$s->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE)
   ->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4)->setFitToWidth(1)->setFitToHeight(0);

$widths = [10, 14, 18, 18, 18, 16, 18, 40];
foreach ($widths as $i => $w) $s->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($i))->setWidth($w);

// Título
$s->mergeCells('A1:H1');
$s->setCellValue('A1', 'DERKA Y VARGAS S.A.  —  ESTADO DE CUENTA  —  '.date('d/m/Y H:i'));
$s->getStyle('A1')->applyFromArray([
    'font'=>['bold'=>true,'size'=>12,'color'=>['rgb'=>'1F3A6B']],
    'alignment'=>['horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER],
]);
$s->getRowDimension(1)->setRowHeight(22);

// Datos del cliente
$kv = function($row, $k, $v) use ($s) {
    $s->setCellValue('A'.$row, $k); $s->getStyle('A'.$row)->getFont()->setBold(true);
    $s->mergeCells('B'.$row.':D'.$row); $s->setCellValue('B'.$row, $v);
};
$kv(3, 'Cliente:', $d['cliente']);
$kv(4, 'Asesor:', $d['asesor']);
$kv(5, 'Tipo de Crédito:', $d['credito']);
$kv(6, 'Financiera:', $d['financiera_cred']);
$s->setCellValue('A7', 'Monto financiación:'); $s->getStyle('A7')->getFont()->setBold(true);
$s->setCellValue('B7', (float)$d['monto_cred']); $s->getStyle('B7')->getNumberFormat()->setFormatCode($MONEY);

// Resumen de montos a la derecha
$res = [['Monto Operación', $d['monto_operacion']], ['Pagado', $d['pagado']], ['A cancelar', $d['a_cancelar']]];
$rr = 3;
foreach ($res as $r) {
    $s->setCellValue('F'.$rr, $r[0]); $s->getStyle('F'.$rr)->getFont()->setBold(true);
    $s->setCellValue('G'.$rr, (float)$r[1]); $s->getStyle('G'.$rr)->getNumberFormat()->setFormatCode($MONEY);
    $rr++;
}

// Cabecera tabla
$head = ['Nro', 'Fecha', 'Tipo', 'Modo', 'Financiera', 'Nro Recibo', 'Monto', 'Observación'];
$hr = 9;
foreach ($head as $i => $t) $s->setCellValueByColumnAndRow($i, $hr, $t);
$s->getStyle('A'.$hr.':H'.$hr)->applyFromArray([
    'font'=>['bold'=>true,'color'=>['rgb'=>'FFFFFF']],
    'fill'=>['type'=>PHPExcel_Style_Fill::FILL_SOLID,'startcolor'=>['rgb'=>'1D4ED8']],
    'alignment'=>['horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER],
    'borders'=>['allborders'=>['style'=>PHPExcel_Style_Border::BORDER_THIN]],
]);

// Filas
$row = $hr + 1; $total = 0;
foreach ($d['pagos'] as $p) {
    $total += $p['monto'];
    $vals = [$p['idpago'], ec_fecha($p['fecha']), $p['tipo'], $p['modo'], $p['financiera'], $p['nrorecibo'], (float)$p['monto'], $p['obs']];
    foreach ($vals as $i => $v) {
        $s->setCellValueByColumnAndRow($i, $row, $v);
        if ($i === 6) $s->getStyleByColumnAndRow($i, $row)->getNumberFormat()->setFormatCode($MONEY);
    }
    $s->getStyleByColumnAndRow(5, $row)->getNumberFormat()->setFormatCode('@'); // recibo como texto
    $row++;
}
// Total
$s->mergeCells('A'.$row.':F'.$row);
$s->setCellValue('A'.$row, 'TOTAL PAGADO');
$s->setCellValueByColumnAndRow(6, $row, $total);
$s->getStyleByColumnAndRow(6, $row)->getNumberFormat()->setFormatCode($MONEY);
$s->getStyle('A'.$row.':H'.$row)->applyFromArray([
    'font'=>['bold'=>true],
    'fill'=>['type'=>PHPExcel_Style_Fill::FILL_SOLID,'startcolor'=>['rgb'=>'DBEAFE']],
    'borders'=>['top'=>['style'=>PHPExcel_Style_Border::BORDER_MEDIUM,'color'=>['rgb'=>'1D4ED8']]],
]);
$s->getStyle('A'.$hr.':H'.$row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);

$nombre = 'estado_cuenta_'.$idcliente.'_'.date('Y-m-d').'.xlsx';
if (ob_get_length()) ob_end_clean();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="'.$nombre.'"');
header('Cache-Control: max-age=0');
PHPExcel_IOFactory::createWriter($xls, 'Excel2007')->save('php://output');
exit;
