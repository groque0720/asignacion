<?php
require_once __DIR__ . '/Classes/PHPExcel.php';
include __DIR__ . "/funciones/func_mysql.php";
conectar();
mysqli_query($con, "SET NAMES 'utf8'");
@session_start();

//COMPRUEBA QUE EL USUARIO ESTA AUTENTIFICADO
if ($_SESSION["autentificado"] != "SI") {
    //si no existe, envio a la página de autentificacion
    header("Location: ../login");
    //ademas salgo de este script
    exit();
  }
$userId = $_SESSION["id"];
$usersAdmin = ['56', '81', '11'];
// 56 Mauro Vargas
// 81 Santiago Galiano
// 11 Admin
$isAdmin = in_array($userId, $usersAdmin);
  
include __DIR__ . "/actions/obtener_modelos_activos_en_planes.php";

$modelo_activo_id = $_GET['modelo_activo'] ?? 1;
$situacionId = $_GET['situacionId'] ?? 1;
$estadoId = $_GET['estadoId'] ?? null;

include __DIR__ . "/actions/obtener_modelo_activo.php";
include __DIR__ . "/actions/obtener_planes_avanzados_x_sit_mod.php";

$objPHPExcel = new PHPExcel();
$sheet = $objPHPExcel->getActiveSheet();

// Configurar estilos
$estiloHeader = [
    'font' => ['bold' => true, 'size' => 10],
    'alignment' => [
        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER
    ],
    'borders' => [
        'allborders' => ['style' => \PHPExcel_Style_Border::BORDER_THIN]
    ]
];

$estiloCelda = [
    'alignment' => [
        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER
    ],
    'borders' => [
        'allborders' => ['style' => \PHPExcel_Style_Border::BORDER_THIN]
    ]
];

// Modificar estilo de celdas numéricas para centrado
$estiloCeldaNumero = [
    'alignment' => [
        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER, // Cambiado a CENTER
        'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER
    ],
    'borders' => [
        'allborders' => ['style' => \PHPExcel_Style_Border::BORDER_THIN]
    ]
];

// Configurar anchos de columna
$sheet->getColumnDimension('A')->setWidth(15); // Plan
$sheet->getColumnDimension('B')->setWidth(20); // Modalidad
$sheet->getColumnDimension('C')->setWidth(25); // Grupo-Orden
$sheet->getColumnDimension('D')->setWidth(10); // Cantidad Cuotas
$sheet->getColumnDimension('E')->setWidth(15); // Monto Cuotas
$sheet->getColumnDimension('F')->setWidth(15); // Costo DYV
$sheet->getColumnDimension('G')->setWidth(15); // Plus
$sheet->getColumnDimension('H')->setWidth(15); // Cuota Promedio
$sheet->getColumnDimension('I')->setWidth(15); // Valor Unidad
$sheet->getColumnDimension('J')->setWidth(15); // Venta
$sheet->getColumnDimension('K')->setWidth(15); // Integración
$sheet->getColumnDimension('L')->setWidth(25); // Derecho Adjudicación
$sheet->getColumnDimension('M')->setWidth(15); // Total
$sheet->getColumnDimension('N')->setWidth(15); // Reserva
$sheet->getColumnDimension('O')->setWidth(80); // Situación

// Encabezados
$fila = 1;
$sheet->mergeCells("A{$fila}:A" . ($fila + 1));
$sheet->setCellValue("A{$fila}", "Plan");
$sheet->mergeCells("B{$fila}:B" . ($fila + 1));
$sheet->setCellValue("B{$fila}", "Modalidad");
$sheet->mergeCells("C{$fila}:C" . ($fila + 1));
$sheet->setCellValue("C{$fila}", "Grupo-Orden");

$sheet->mergeCells("D{$fila}:E{$fila}");
$sheet->setCellValue("D{$fila}", "Cuotas Pagadas");
$sheet->setCellValue("D" . ($fila + 1), "Cantidad");
$sheet->setCellValue("E" . ($fila + 1), "Monto (*)");

$sheet->mergeCells("F{$fila}:F" . ($fila + 1));
$sheet->setCellValue("F{$fila}", "Costo (*)");
$sheet->mergeCells("G{$fila}:G" . ($fila + 1));
$sheet->setCellValue("G{$fila}", "Plus (*)");
$sheet->mergeCells("H{$fila}:H" . ($fila + 1));
$sheet->setCellValue("H{$fila}", "Cuota Promedio");
$sheet->mergeCells("I{$fila}:I" . ($fila + 1));
$sheet->setCellValue("I{$fila}", "Valor Unidad");
$sheet->mergeCells("J{$fila}:J" . ($fila + 1));
$sheet->setCellValue("J{$fila}", "Venta");
$sheet->mergeCells("K{$fila}:K" . ($fila + 1));
$sheet->setCellValue("K{$fila}", "Integración");
$sheet->mergeCells("L{$fila}:L" . ($fila + 1));
$sheet->setCellValue("L{$fila}", "Derecho de \nAdjudicación");
$sheet->mergeCells("M{$fila}:M" . ($fila + 1));
$sheet->setCellValue("M{$fila}", "Total");
$sheet->mergeCells("N{$fila}:N" . ($fila + 1));
$sheet->setCellValue("N{$fila}", "Reserva");
$sheet->mergeCells("O{$fila}:O" . ($fila + 1));
$sheet->setCellValue("O{$fila}", "Situación \nCliente/asesor");

// Aplicar estilos especiales
$sheet->getStyle("G{$fila}")->getFont()->getColor()->setRGB('FF0000'); // Plus en rojo
$sheet->getStyle("M{$fila}")->getFont()->getColor()->setRGB('FF0000'); // Total en rojo

// Aplicar estilos a encabezados
$sheet->getStyle("A{$fila}:O" . ($fila + 1))->applyFromArray($estiloHeader);

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
    $sheet->setCellValue("K{$fila}", $plan['integracion']);
    $sheet->setCellValue("L{$fila}", $plan['derecho_adjudicacion']);
    $sheet->setCellValue("M{$fila}", $plan['precio_final']);
    $sheet->setCellValue("N{$fila}", $plan['monto_reserva']);
    $sheet->setCellValue("O{$fila}", $plan['cliente'] . " / " . $plan['usuario_venta']);
    
    // Aplicar centrado a todas las celdas
    $sheet->getStyle("A{$fila}:O{$fila}")->applyFromArray($estiloCelda);
    
    // No necesitamos alineación especial para números ahora ya que todo va centrado
    // Mantener solo el formato numérico
    $sheet->getStyle("E{$fila}:N{$fila}")->getNumberFormat()->setFormatCode('#,##0.00');
    
    if ($plan['estado_id'] == 1) {
        $sheet->getStyle("A{$fila}:O{$fila}")->getFill()
              ->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)
              ->getStartColor()->setRGB('ABEBC6');
    } elseif ($plan['estado_id'] == 2) {
        $sheet->getStyle("A{$fila}:O{$fila}")->getFill()
              ->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)
              ->getStartColor()->setRGB('FAD7A0');
    }
    
    // Plus y Total en rojo
    $sheet->getStyle("G{$fila}")->getFont()->getColor()->setRGB('FF0000');
    $sheet->getStyle("M{$fila}")->getFont()->getColor()->setRGB('FF0000');
    
    $fila++;
}

// Aplicar bordes a toda la tabla
$ultimaFila = $fila - 1;
$sheet->getStyle("A1:O{$ultimaFila}")->applyFromArray([
    'borders' => [
        'allborders' => [
            'style' => \PHPExcel_Style_Border::BORDER_THIN
        ]
    ]
]);

// Generar nombre del archivo
$fecha = date('d-m-Y');
$nombreBase = 'planes_lista_' . strtolower(str_replace(' ', '_', $modelo_activo_nombre));

if ($estadoId) {
    $nombreArchivo = $nombreBase . ($estadoId == 1 ? '_libres' : '_reservados');
} else {
    $nombreArchivo = $nombreBase . '_libres_y_reservados';
}
$nombreArchivo .= '_' . $fecha . '.xlsx';

// Enviar headers antes de enviar el Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $nombreArchivo . '"');
header('Cache-Control: max-age=0');

$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
