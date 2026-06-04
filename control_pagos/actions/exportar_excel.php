<?php
/*
 * Exporta a Excel (.xlsx) el resultado del filtro actual de Control de Pagos.
 * Reutiliza la misma lógica de filtro/cálculo que listar.php (vía funciones/consulta.php).
 * Streamea el binario y termina la ejecución.
 *
 * Requiere: $con (config_app.php) y las funciones cp_* (funciones/consulta.php).
 * El endpoint excel.php ya silenció errores y abrió el output buffer.
 */
require_once __DIR__ . '/../../asignacion/Classes/PHPExcel.php';

list($W, $orderBy) = cp_where($con);
list($rows, $err)  = cp_fetch_todo($con, $W, $orderBy);
if ($err) { die("Error al generar Excel: ".$err); }

// ─── Columnas (título + ancho + si es moneda) ────────────────────────────────
$cols = [
    ['t' => 'N.R.',        'w' => 9],
    ['t' => 'N.U.',        'w' => 9],
    ['t' => 'Interno',     'w' => 9],
    ['t' => 'Nro Orden',   'w' => 14],
    ['t' => 'Asesor',      'w' => 16],
    ['t' => 'Cliente',     'w' => 28],
    ['t' => 'Tipo Venta',  'w' => 14],
    ['t' => 'Modelo',      'w' => 30],
    ['t' => 'Usado',       'w' => 13, 'money' => true],
    ['t' => 'Efectivo',    'w' => 14, 'money' => true],
    ['t' => 'Crédito',     'w' => 14, 'money' => true],
    ['t' => 'Leasing',     'w' => 13, 'money' => true],
    ['t' => 'Saldo',       'w' => 15, 'money' => true],
    ['t' => 'Fec.Res.',    'w' => 11],
    ['t' => 'Llegó',       'w' => 11],
    ['t' => 'Cancela',     'w' => 11],
    ['t' => 'Observación', 'w' => 34],
];
$nCols = count($cols);
$lastCol = PHPExcel_Cell::stringFromColumnIndex($nCols - 1);
$MONEY = '"$ "#,##0';

$xls = new PHPExcel();
$xls->getProperties()->setCreator('Sistema Asignación')->setTitle('Control de Pagos');
$sheet = $xls->getActiveSheet();
$sheet->setTitle('Control de Pagos');

$sheet->getPageSetup()
    ->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE)
    ->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4)
    ->setFitToWidth(1)->setFitToHeight(0);

foreach ($cols as $i => $c) {
    $sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($i))->setWidth($c['w']);
}

// Fila 1: título
$titulo = 'DERKA Y VARGAS S.A.  —  CONTROL DE PAGOS  —  Sucursal: '.cp_sucursal_nombre().
          '  —  Estado: '.cp_estado_nombre().'  —  '.date('d/m/Y H:i');
$sheet->mergeCells('A1:'.$lastCol.'1');
$sheet->setCellValue('A1', $titulo);
$sheet->getStyle('A1')->applyFromArray([
    'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '1F3A6B']],
    'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER],
]);
$sheet->getRowDimension(1)->setRowHeight(22);

// Fila 2: cabecera
foreach ($cols as $i => $c) {
    $sheet->setCellValueByColumnAndRow($i, 2, $c['t']);
}
$sheet->getStyle('A2:'.$lastCol.'2')->applyFromArray([
    'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']],
    'fill'      => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => ['rgb' => '1D4ED8']],
    'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER, 'wrap' => true],
    'borders'   => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN]],
]);
$sheet->getRowDimension(2)->setRowHeight(24);
$sheet->freezePane('A3');
$sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 2);

// Filas de datos
$row = 3;
$tUsado = $tEfec = $tCred = $tLeas = $tSaldo = 0.0;
foreach ($rows as $r) {
    $tUsado += $r['usado']; $tEfec += $r['efectivo']; $tCred += $r['credito'];
    $tLeas += $r['leasing']; $tSaldo += $r['saldo'];

    $vals = [
        $r['idreserva'], $r['nrounidad'], $r['interno'], $r['nroorden'], $r['asesor'],
        $r['cliente'], $r['tipo_venta'], $r['modelo_txt'],
        (float)$r['usado'], (float)$r['efectivo'], (float)$r['credito'], (float)$r['leasing'], (float)$r['saldo'],
        cp_fecha($r['fecres']), cp_fecha($r['llego']), cp_fecha($r['fechacanc']), $r['obs'],
    ];
    foreach ($vals as $i => $v) {
        $sheet->setCellValueByColumnAndRow($i, $row, $v);
        if (!empty($cols[$i]['money'])) {
            $sheet->getStyleByColumnAndRow($i, $row)->getNumberFormat()->setFormatCode($MONEY);
        }
    }
    // texto explícito en columnas que pueden parecer números (interno/orden) para no perder ceros
    $sheet->getStyleByColumnAndRow(2, $row)->getNumberFormat()->setFormatCode('@');
    $sheet->getStyleByColumnAndRow(3, $row)->getNumberFormat()->setFormatCode('@');
    $row++;
}

// Fila de totales
$sheet->mergeCells('A'.$row.':H'.$row);
$sheet->setCellValue('A'.$row, 'TOTALES  ('.count($rows).' operaciones)');
$tot = [8 => $tUsado, 9 => $tEfec, 10 => $tCred, 11 => $tLeas, 12 => $tSaldo];
foreach ($tot as $i => $v) {
    $sheet->setCellValueByColumnAndRow($i, $row, $v);
    $sheet->getStyleByColumnAndRow($i, $row)->getNumberFormat()->setFormatCode($MONEY);
}
$sheet->getStyle('A'.$row.':'.$lastCol.$row)->applyFromArray([
    'font'    => ['bold' => true, 'size' => 10],
    'fill'    => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => ['rgb' => 'DBEAFE']],
    'borders' => ['top' => ['style' => PHPExcel_Style_Border::BORDER_MEDIUM, 'color' => ['rgb' => '1D4ED8']]],
]);

// Bordes finos + alineación del cuerpo
$lastRow = $row;
$sheet->getStyle('A2:'.$lastCol.$lastRow)->getBorders()->getAllBorders()
      ->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
$sheet->getStyle('A3:'.$lastCol.$lastRow)->getAlignment()
      ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

// Salida
$nombre = 'control_pagos_'.date('Y-m-d_His').'.xlsx';
if (ob_get_length()) ob_end_clean();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="'.$nombre.'"');
header('Cache-Control: max-age=0');
$writer = PHPExcel_IOFactory::createWriter($xls, 'Excel2007');
$writer->save('php://output');
exit;
