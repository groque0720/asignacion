<?php
require_once __DIR__ . '/Classes/PHPExcel.php';
include __DIR__ . "/funciones/func_mysql.php";
conectar();
mysqli_query($con, "SET NAMES 'utf8'");
@session_start();

// Seguridad
if ($_SESSION["autentificado"] != "SI") {
    header("Location: ../login");
    exit();
}

$userId = $_SESSION["id"];
$usersAdmin = ['56', '81', '11'];
$isAdmin = in_array($userId, $usersAdmin);

// Datos
include __DIR__ . "/actions/obtener_modelos_activos_en_planes.php";
$modelo_activo_id = $_GET['modelo_activo'] ?? 1;
$situacionId = $_GET['situacionId'] ?? 1;
$estadoId = $_GET['estadoId'] ?? null;
include __DIR__ . "/actions/obtener_modelo_activo.php";
include __DIR__ . "/actions/obtener_planes_avanzados_x_sit_mod.php";

// Crear Excel
$objPHPExcel = new PHPExcel();
$sheet = $objPHPExcel->getActiveSheet();

// Estilos
$estiloHeader = [
    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
    'alignment' => ['horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER],
    'borders' => ['allborders' => ['style' => \PHPExcel_Style_Border::BORDER_THIN]],
    'fill' => ['type' => \PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => '4A90E2']]
];

$estiloCelda = [
    'font' => ['size' => 10],
    'alignment' => ['horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER],
    'borders' => ['allborders' => ['style' => \PHPExcel_Style_Border::BORDER_THIN]]
];

$estiloImportante = [
    'font' => ['bold' => true, 'size' => 10],
    'alignment' => ['horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER],
    'borders' => ['allborders' => ['style' => \PHPExcel_Style_Border::BORDER_THIN]]
];

// Anchos de columna
$anchos = [
    'A'=>15, 'B'=>20, 'C'=>25, 'D'=>10, 'E'=>15, 'F'=>15, 'G'=>15,
    'H'=>15, 'I'=>15, 'J'=>15, 'K'=>15, 'L'=>15, 'M'=>25, 'N'=>15, 'O'=>15, 'P'=>80
];
foreach ($anchos as $col=>$ancho) { $sheet->getColumnDimension($col)->setWidth($ancho); }

// Encabezados
$fila = 1;
$sheet->mergeCells("A{$fila}:A".($fila+1))->setCellValue("A{$fila}", "Plan");
$sheet->mergeCells("B{$fila}:B".($fila+1))->setCellValue("B{$fila}", "Modalidad");
$sheet->mergeCells("C{$fila}:C".($fila+1))->setCellValue("C{$fila}", "Grupo-Orden");

$sheet->mergeCells("D{$fila}:E{$fila}")->setCellValue("D{$fila}", "Cuotas Pagadas");
$sheet->setCellValue("D".($fila+1), "Cantidad");
$sheet->setCellValue("E".($fila+1), "Monto (*)");

$sheet->mergeCells("F{$fila}:F".($fila+1))->setCellValue("F{$fila}", "Costo (*)");
$sheet->mergeCells("G{$fila}:G".($fila+1))->setCellValue("G{$fila}", "Plus (*)");
$sheet->mergeCells("H{$fila}:H".($fila+1))->setCellValue("H{$fila}", "Cuota Promedio");
$sheet->mergeCells("I{$fila}:I".($fila+1))->setCellValue("I{$fila}", "Valor Unidad");
$sheet->mergeCells("J{$fila}:J".($fila+1))->setCellValue("J{$fila}", "Venta");
$sheet->mergeCells("K{$fila}:K".($fila+1))->setCellValue("K{$fila}", "Bonificación");
$sheet->mergeCells("L{$fila}:L".($fila+1))->setCellValue("L{$fila}", "Integración");
$sheet->mergeCells("M{$fila}:M".($fila+1))->setCellValue("M{$fila}", "Derecho de\nAdjudicación");
$sheet->mergeCells("N{$fila}:N".($fila+1))->setCellValue("N{$fila}", "Total");
$sheet->mergeCells("O{$fila}:O".($fila+1))->setCellValue("O{$fila}", "Reserva");
$sheet->mergeCells("P{$fila}:P".($fila+1))->setCellValue("P{$fila}", "Situación\nCliente/asesor");

// Estilo encabezado
$sheet->getStyle("A{$fila}:P".($fila+1))->applyFromArray($estiloHeader);

// Datos
$fila = 3;
while ($plan = mysqli_fetch_array($planes_avanzados)) {
    $sheet->setCellValue("A{$fila}", $plan['modelo'] . ' ' . $plan['version']);
    $sheet->setCellValue("B{$fila}", $plan['modalidad']);
    $sheet->setCellValue("C{$fila}", $plan['grupo_orden']);
    $sheet->setCellValue("D{$fila}", $plan['cuotas_pagadas_cantidad']);
    $sheet->setCellValue("E{$fila}", $plan['cuotas_pagadas_monto']);
    $sheet->setCellValue("F{$fila}", $plan['costo']);
    $sheet->setCellValue("G{$fila}", $plan['plus']);
    $sheet->setCellValue("H{$fila}", $plan['cuota_promedio']);
    $sheet->setCellValue("I{$fila}", $plan['valor_unidad']);
    $sheet->setCellValue("J{$fila}", $plan['venta']);
    $sheet->setCellValue("K{$fila}", ($plan['cuota_promedio'] * $plan['cuotas_pagadas_cantidad']) - $plan['venta']);
    $sheet->setCellValue("L{$fila}", $plan['integracion']);
    $sheet->setCellValue("M{$fila}", $plan['derecho_adjudicacion']);
    $sheet->setCellValue("N{$fila}", $plan['precio_final']);
    $sheet->setCellValue("O{$fila}", $plan['monto_reserva']);
    $sheet->setCellValue("P{$fila}", $plan['cliente'] . " / " . $plan['usuario_venta']);

    // Estilo base
    $sheet->getStyle("A{$fila}:P{$fila}")->applyFromArray($estiloCelda);

    // Formato numérico
    $sheet->getStyle("E{$fila}:O{$fila}")->getNumberFormat()->setFormatCode('#,##0.00');

    // Colores por estado
    if ($plan['estado_id'] == 1) {
        $color = 'ABEBC6'; // Verde claro
    } elseif ($plan['estado_id'] == 2) {
        $color = 'FAD7A0'; // Amarillo
    } elseif ($plan['estado_id'] == 3) {
        $color = 'F1948A'; // Rojo claro
    } else {
        $color = 'FFFFFF'; // Blanco
    }
    $sheet->getStyle("A{$fila}:P{$fila}")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($color);

    // Resaltar Plus, Bonificación y Total
    $sheet->getStyle("G{$fila}")->getFont()->getColor()->setRGB('FF0000'); // Rojo
    $sheet->getStyle("K{$fila}")->getFont()->getColor()->setRGB('0066CC'); // Azul
    $sheet->getStyle("N{$fila}")->getFont()->setBold(true)->getColor()->setRGB('FF0000'); // Rojo y negrita

    $fila++;
}

// Bordes generales
$ultimaFila = $fila - 1;
$sheet->getStyle("A1:P{$ultimaFila}")->applyFromArray([
    'borders' => ['allborders' => ['style' => \PHPExcel_Style_Border::BORDER_THIN]]
]);

// Configuración de impresión
$sheet->getPageSetup()
    ->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT)
    ->setPaperSize(\PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4)
    ->setFitToWidth(1)
    ->setFitToHeight(0);
$sheet->getPageMargins()->setTop(0.5)->setBottom(0.5)->setLeft(0.5)->setRight(0.5);
$sheet->getHeaderFooter()->setOddFooter('&LGenerado el &D &RPágina &P de &N');

// Nombre archivo
$fecha = date('d-m-Y');
$nombreBase = 'planes_lista_' . strtolower(str_replace(' ', '_', $modelo_activo_nombre));
if ($estadoId) {
    $suffix = ($estadoId == 1) ? 'libres' : (($estadoId == 2) ? 'reservados' : 'vendidos');
    $nombreArchivo = $nombreBase . '_' . $suffix;
} else {
    $nombreArchivo = $nombreBase . '_libres_reservados_vendidos';
}
$nombreArchivo .= '_' . $fecha . '.xlsx';

// Salida Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $nombreArchivo . '"');
header('Cache-Control: max-age=0');

$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
