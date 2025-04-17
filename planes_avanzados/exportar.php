<?php

// Incluir la librería PHPExcel (ajusta la ruta si es necesario)
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

// Configuración inicial
$columnaInicial = 'A';
$filaActual = 1;
$cardsPerRow = 3; // Número de cards por fila
$anchoCard = 25; // Ancho de cada card
$espacioEntreCards = 1; // Columnas vacías entre cards

// Configurar anchos de columna para el grid
for ($i = 0; $i < ($cardsPerRow * (2 + $espacioEntreCards)); $i++) {
    $columna = chr(ord('A') + $i);
    $sheet->getColumnDimension($columna)->setWidth($anchoCard);
}

$cardCount = 0;
while ($plan = mysqli_fetch_array($planes_avanzados)) {
    // Calcular posición de la card
    $columnaActual = chr(ord($columnaInicial) + ($cardCount % $cardsPerRow) * (2 + $espacioEntreCards));
    
    if ($cardCount > 0 && $cardCount % $cardsPerRow == 0) {
        $filaActual += 18; // Nueva fila después de cada conjunto de cards
    }
    
    // Determinar el tipo de card y generarla
    if ($plan['estado_id'] == 1) {
        generarCardModelo1($sheet, $filaActual, $columnaActual, $plan);
    } else {
        generarCardModelo1($sheet, $filaActual, $columnaActual, $plan);
    }
    
    $cardCount++;
}

// Generar nombre del archivo
$fecha = date('d-m-Y');
$nombreBase = 'planes_cards_' . strtolower(str_replace(' ', '_', $modelo_activo_nombre));

if ($estadoId) {
    if ($estadoId == 1) {
        $nombreArchivo = $nombreBase . '_libres';
    } elseif ($estadoId == 2) {
        $nombreArchivo = $nombreBase . '_reservados';
    } elseif ($estadoId == 3) {
        $nombreArchivo = $nombreBase . '_vendidos';
    } else {
        $nombreArchivo = $nombreBase . '_otros';
    }   
} else {
    $nombreArchivo = $nombreBase . '_libres_reservados_vendidos';
}
$nombreArchivo .= '_' . $fecha . '.xlsx';

// Enviar headers antes de enviar el Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $nombreArchivo . '"');
header('Cache-Control: max-age=0');

$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;

// --- Funciones para generar las cards ---

function generarCardModelo1($sheet, $fila, $columna, $plan) {
    // Estilos base
    $colorFondo = ''; 
    if ($plan['estado_id'] == 1) {
        $colorFondo = 'ABEBC6'; // Verde para Libre
    } elseif ($plan['estado_id'] == 2) {
        $colorFondo = 'FAD7A0'; // Naranja para Reservado
    } elseif ($plan['estado_id'] == 3) {
        $colorFondo = 'F1948A'; // Rojo para Vendido
    }
    $estiloBordeCard = [
        'borders' => [
            'outline' => [
                'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
                'color' => ['rgb' => '000000']
            ]
        ]
    ];
    
    $estiloTitulo = array(
        'font' => array('bold' => true, 'size' => 11),
        'alignment' => array(
            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER
        ),
        'fill' => array(
            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array('rgb' => $colorFondo)
        )
    );
    $estiloLabel = array(
        'font' => array('size' => 10),
        'alignment' => array(
            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER
        )
    );
    $estiloValor = array(
        'font' => array('size' => 10),
        'alignment' => array(
            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER
        ),
        'numberformat' => array('code' => '#,##0.00')
    );
    
    // Calcular la última fila de la card (15 filas en total)
    $ultimaFila = $fila + 14;
    
    // Aplicar borde a toda la card
    $sheet->getStyle("{$columna}{$fila}:" . chr(ord($columna)+1) . $ultimaFila)
          ->applyFromArray($estiloBordeCard);
    
    // Título y versión
    $sheet->mergeCells("{$columna}{$fila}:" . chr(ord($columna)+1) . $fila);
    $sheet->setCellValue("{$columna}{$fila}", $plan['modelo'] . ' ' . $plan['version']);
    $sheet->getStyle("{$columna}{$fila}")->applyFromArray($estiloTitulo);
    
    $fila++;
    $sheet->mergeCells("{$columna}{$fila}:" . chr(ord($columna)+1) . $fila);
    $sheet->setCellValue("{$columna}{$fila}", $plan['modalidad']);
    $sheet->getStyle("{$columna}{$fila}")->applyFromArray($estiloTitulo);
    
    // Grupo y Orden
    $fila++;
    agregarFilaDatosConEstilo($sheet, $fila, $columna, "Grupo y Orden:", $plan['grupo_orden']);
    
    // Espacio en blanco
    $fila++;
    
    // Información financiera
    $datosPlan = [
        ["Cuotas Pagas (" . $plan['cuotas_pagadas_cantidad'] . ")", $plan['cuotas_pagadas_monto'], true],
        ["Costo DYV", $plan['costo'], true],
        ["Plus", $plan['plus'], true, false, 'FF0000'], // rojo
        ["Precio Venta", $plan['venta'], true, true], // negrita
        ["Cuota Promedio", $plan['cuota_promedio'], true, false, 'FF0000'], // rojo
        ["Valor de la unidad", $plan['valor_unidad']],
        ["Integración", $plan['integracion'], true],
        ["Derecho Adjudicación", $plan['derecho_adjudicacion']],
        ["Total", $plan['precio_final'], true, true], // negrita
        ["Reserva", $plan['monto_reserva']]
    ];
    
    foreach ($datosPlan as $dato) {
        $label = $dato[0];
        $valor = $dato[1];
        $mostrarBorde = isset($dato[2]) ? $dato[2] : false;
        $negrita = isset($dato[3]) ? $dato[3] : false;
        $colorTexto = isset($dato[4]) ? $dato[4] : '000000';
        
        agregarFilaDatosConEstilo($sheet, $fila, $columna, $label, $valor, $mostrarBorde, $negrita, $colorTexto);
        $fila++;
    }
    
    // Estado/Reserva
    $fila++;
    if ($plan['estado_id'] == 1) {
        $sheet->mergeCells("{$columna}{$fila}:" . chr(ord($columna)+1) . $fila);
        $sheet->setCellValue("{$columna}{$fila}", "RESERVAR");
        $sheet->getStyle("{$columna}{$fila}")
              ->getFont()
              ->setColor(new \PHPExcel_Style_Color(\PHPExcel_Style_Color::COLOR_GREEN))
              ->setBold(true);
    } else {
        $sheet->mergeCells("{$columna}{$fila}:" . chr(ord($columna)+1) . $fila);
        $sheet->setCellValue("{$columna}{$fila}", $plan['cliente'] . " / " . $plan['usuario_venta']);
    }
    
    return $fila;
}

function agregarFilaDatosConEstilo($sheet, $fila, $columna, $label, $valor, $mostrarBorde = false, $negrita = false, $colorTexto = '000000') {
    $estiloBase = [
        'font' => ['size' => 10, 'color' => ['rgb' => $colorTexto]],
        'alignment' => [
            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER
        ]
    ];
    
    if ($negrita) {
        $estiloBase['font']['bold'] = true;
    }
    
    if ($mostrarBorde) {
        $estiloBase['borders'] = [
            'top' => ['style' => \PHPExcel_Style_Border::BORDER_THIN],
            'bottom' => ['style' => \PHPExcel_Style_Border::BORDER_THIN]
        ];
    }
    
    $sheet->setCellValue($columna . $fila, $label);
    $sheet->setCellValue(chr(ord($columna)+1) . $fila, $valor);
    $sheet->getStyle($columna . $fila)->applyFromArray($estiloBase);
    $sheet->getStyle(chr(ord($columna)+1) . $fila)->applyFromArray($estiloBase);
    $sheet->getStyle(chr(ord($columna)+1) . $fila)->getNumberFormat()->setFormatCode('#,##0.00');
}



?>