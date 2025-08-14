<?php
// Turn off notice errors to prevent issues with headers
error_reporting(E_ALL & ~E_NOTICE);

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

// Si no es admin, redirigir
if (!$isAdmin) {
    header("Location: /planes_avanzados/");
    exit();
}

$situacionId = $_GET['situacionId'] ?? 1; // Avanzados o Adjudicados
$formato = $_GET['formato'] ?? 'lista'; // Formato lista o cards

// Incluir acción para obtener todos los planes
include __DIR__ . "/actions/obtener_planes_todos_modelos.php";

// Verificar si se obtuvieron registros
if ($num_rows == 0) {
    // Si no hay registros, mostrar mensaje y terminar
    die("No se encontraron planes para exportar con los criterios seleccionados.");
}

$objPHPExcel = new PHPExcel();
$sheet = $objPHPExcel->getActiveSheet();
$sheet->setTitle($situacionId == 1 ? "Avanzados" : "Adjudicados");

// Generar el archivo según el formato solicitado
if ($formato === 'cards') {
    // Formato de cards (similar a exportar.php)
    exportarEnFormatoCards($sheet, $planes_todos_modelos);
} else {
    // Formato de lista (similar a exportar_lista.php)
    exportarEnFormatoLista($sheet, $planes_todos_modelos);
}

// Generar nombre del archivo
$fecha = date('d-m-Y');
$tipoSituacion = $situacionId == 1 ? "avanzados" : "adjudicados";
$tipoFormato = $formato === 'cards' ? "cards" : "lista";
$nombreArchivo = "planes_todos_modelos_{$tipoSituacion}_{$tipoFormato}_{$fecha}.xlsx";

// Generar el archivo y enviarlo al navegador
ob_clean(); // Limpiar el buffer de salida para evitar problemas con headers

// Enviar headers antes de enviar el Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $nombreArchivo . '"');
header('Cache-Control: max-age=0');

$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;

// --- Funciones para generar los formatos ---

function exportarEnFormatoLista($sheet, $planes_todos_modelos) {
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

    // Configurar anchos de columna
    $sheet->getColumnDimension('A')->setWidth(15); // Modelo
    $sheet->getColumnDimension('B')->setWidth(15); // Plan
    $sheet->getColumnDimension('C')->setWidth(15); // Modalidad
    $sheet->getColumnDimension('D')->setWidth(20); // Grupo-Orden
    $sheet->getColumnDimension('E')->setWidth(10); // Cantidad Cuotas
    $sheet->getColumnDimension('F')->setWidth(15); // Monto Cuotas
    $sheet->getColumnDimension('G')->setWidth(15); // Costo DYV
    $sheet->getColumnDimension('H')->setWidth(15); // Plus
    $sheet->getColumnDimension('I')->setWidth(15); // Cuota Promedio
    $sheet->getColumnDimension('J')->setWidth(15); // Valor Unidad
    $sheet->getColumnDimension('K')->setWidth(15); // Venta
    $sheet->getColumnDimension('L')->setWidth(15); // Integración
    $sheet->getColumnDimension('M')->setWidth(20); // Derecho Adjudicación
    $sheet->getColumnDimension('N')->setWidth(15); // Total
    $sheet->getColumnDimension('O')->setWidth(15); // Reserva
    $sheet->getColumnDimension('P')->setWidth(15); // Estado
    $sheet->getColumnDimension('Q')->setWidth(50); // Situación

    // Encabezados
    $fila = 1;
    $sheet->mergeCells("A{$fila}:A" . ($fila + 1));
    $sheet->setCellValue("A{$fila}", "Modelo");
    $sheet->mergeCells("B{$fila}:B" . ($fila + 1));
    $sheet->setCellValue("B{$fila}", "Plan");
    $sheet->mergeCells("C{$fila}:C" . ($fila + 1));
    $sheet->setCellValue("C{$fila}", "Modalidad");
    $sheet->mergeCells("D{$fila}:D" . ($fila + 1));
    $sheet->setCellValue("D{$fila}", "Grupo-Orden");

    $sheet->mergeCells("E{$fila}:F{$fila}");
    $sheet->setCellValue("E{$fila}", "Cuotas Pagadas");
    $sheet->setCellValue("E" . ($fila + 1), "Cantidad");
    $sheet->setCellValue("F" . ($fila + 1), "Monto (*)");

    $sheet->mergeCells("G{$fila}:G" . ($fila + 1));
    $sheet->setCellValue("G{$fila}", "Costo (*)");
    $sheet->mergeCells("H{$fila}:H" . ($fila + 1));
    $sheet->setCellValue("H{$fila}", "Plus (*)");
    $sheet->mergeCells("I{$fila}:I" . ($fila + 1));
    $sheet->setCellValue("I{$fila}", "Cuota Promedio");
    $sheet->mergeCells("J{$fila}:J" . ($fila + 1));
    $sheet->setCellValue("J{$fila}", "Valor Unidad");
    $sheet->mergeCells("K{$fila}:K" . ($fila + 1));
    $sheet->setCellValue("K{$fila}", "Venta");
    $sheet->mergeCells("L{$fila}:L" . ($fila + 1));
    $sheet->setCellValue("L{$fila}", "Integración");
    $sheet->mergeCells("M{$fila}:M" . ($fila + 1));
    $sheet->setCellValue("M{$fila}", "Derecho de \nAdjudicación");
    $sheet->mergeCells("N{$fila}:N" . ($fila + 1));
    $sheet->setCellValue("N{$fila}", "Total");
    $sheet->mergeCells("O{$fila}:O" . ($fila + 1));
    $sheet->setCellValue("O{$fila}", "Reserva");
    $sheet->mergeCells("P{$fila}:P" . ($fila + 1));
    $sheet->setCellValue("P{$fila}", "Estado");
    $sheet->mergeCells("Q{$fila}:Q" . ($fila + 1));
    $sheet->setCellValue("Q{$fila}", "Situación \nCliente/asesor");

    // Aplicar estilos especiales
    $sheet->getStyle("H{$fila}")->getFont()->getColor()->setRGB('FF0000'); // Plus en rojo
    $sheet->getStyle("N{$fila}")->getFont()->getColor()->setRGB('FF0000'); // Total en rojo

    // Aplicar estilos a encabezados
    $sheet->getStyle("A{$fila}:Q" . ($fila + 1))->applyFromArray($estiloHeader);

    // Datos
    $fila = 3;
    // Resetear el puntero de resultados para asegurar que empezamos desde el principio
    mysqli_data_seek($planes_todos_modelos, 0);

    while ($plan = mysqli_fetch_array($planes_todos_modelos)) {
        // Determinar el estado para mostrar
        $estado_texto = "";
        switch ($plan['estado_id']) {
            case 1:
                $estado_texto = "Libre";
                break;
            case 2:
                $estado_texto = "Reservado";
                break;
            case 3:
                $estado_texto = "Vendido";
                break;
            default:
                $estado_texto = "Desconocido";
                break;
        }
        
        // Construir el nombre de usuario completo
        $usuario_completo = $plan['usuario_venta'];
        
        // Determinar cliente/asesor
        $cliente_texto = "";
        if (isset($plan['cliente']) && !empty($plan['cliente'])) {
            $cliente_texto = $plan['cliente'];
        } elseif ($plan['estado_id'] == 1) {
            $cliente_texto = "Libre";
        } else {
            $cliente_texto = "Cliente";
        }
        
        $cliente_asesor = $cliente_texto . " / " . $usuario_completo;
        
        $sheet->setCellValue("A{$fila}", $plan['modelo']); // Solo el modelo
        $sheet->setCellValue("B{$fila}", $plan['modelo'] . ' ' . $plan['version']); // Plan completo
        $sheet->setCellValue("C{$fila}", $plan['modalidad']);
        $sheet->setCellValue("D{$fila}", $plan['grupo_orden']);
        $sheet->setCellValue("E{$fila}", $plan['cuotas_pagadas_cantidad']);
        $sheet->setCellValue("F{$fila}", $plan['cuotas_pagadas_monto']);
        $sheet->setCellValue("G{$fila}", $plan['costo']);
        $sheet->setCellValue("H{$fila}", $plan['plus']);
        $sheet->setCellValue("I{$fila}", $plan['cuota_promedio']);
        $sheet->setCellValue("J{$fila}", $plan['valor_unidad']);
        $sheet->setCellValue("K{$fila}", $plan['venta']);
        $sheet->setCellValue("L{$fila}", $plan['integracion']);
        $sheet->setCellValue("M{$fila}", $plan['derecho_adjudicacion']);
        $sheet->setCellValue("N{$fila}", $plan['precio_final']);
        $sheet->setCellValue("O{$fila}", $plan['monto_reserva']);
        $sheet->setCellValue("P{$fila}", $estado_texto);
        $sheet->setCellValue("Q{$fila}", $cliente_asesor);
        
        // Aplicar centrado a todas las celdas
        $sheet->getStyle("A{$fila}:Q{$fila}")->applyFromArray($estiloCelda);
        
        // Formato numérico
        $sheet->getStyle("F{$fila}:O{$fila}")->getNumberFormat()->setFormatCode('#,##0.00');
        
        // Colorear fila según estado
        switch ($plan['estado_id']) {
            case 1: // Libre
                $sheet->getStyle("A{$fila}:Q{$fila}")->getFill()
                      ->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)
                      ->getStartColor()->setRGB('ABEBC6');
                break;
            case 2: // Reservado
                $sheet->getStyle("A{$fila}:Q{$fila}")->getFill()
                      ->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)
                      ->getStartColor()->setRGB('FAD7A0');
                break;
            case 3: // Vendido
                $sheet->getStyle("A{$fila}:Q{$fila}")->getFill()
                      ->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)
                      ->getStartColor()->setRGB('F1948A');
                break;
        }
        
        // Plus y Total en rojo
        $sheet->getStyle("H{$fila}")->getFont()->getColor()->setRGB('FF0000');
        $sheet->getStyle("N{$fila}")->getFont()->getColor()->setRGB('FF0000');
        
        $fila++;
    }

    // Aplicar bordes a toda la tabla
    $ultimaFila = $fila - 1;
    if ($ultimaFila >= 3) { // Solo si hay datos
        $sheet->getStyle("A1:Q{$ultimaFila}")->applyFromArray([
            'borders' => [
                'allborders' => [
                    'style' => \PHPExcel_Style_Border::BORDER_THIN
                ]
            ]
        ]);
    }
}

function exportarEnFormatoCards($sheet, $planes_todos_modelos) {

    $styleCellDetail = [
        'font' => [
            'size' => 10,
            'color' => ['rgb' => '000000']
        ],
        'alignment' => [
            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER
        ]
    ];

    $sheet->getPageSetup()
    ->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT)
    ->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4)
    ->setFitToWidth(1)
    ->setFitToHeight(0);
    $sheet->getPageMargins()->setTop(0.5)->setBottom(0.5)->setLeft(0.5)->setRight(0.5);

    // Configuración inicial
    $columnaInicial = 'B';
    $filaActual = 2;
    $cardsPerRow = 3; // Número de cards por fila
    $anchoCard = 23; // Ancho de cada card
    $espacioEntreCards = 1; // Columnas vacías entre cards

    $sheet->getColumnDimension('A')->setWidth(2);

    $filaSaltoPagina = 54; // Fila donde se insertará el salto de página

    // Configurar anchos de columna para el grid
    for ($i = 0; $i < ($cardsPerRow * (2 + $espacioEntreCards)); $i++) {
        $columna = chr(ord('B') + $i);
        $sheet->getColumnDimension($columna)->setWidth($anchoCard);
        $sheet->getColumnDimension('D')->setWidth(3);
        $sheet->getColumnDimension('G')->setWidth(3);
        $sheet->getColumnDimension('J')->setWidth(2);
    }

    // Resetear el puntero de resultados
    mysqli_data_seek($planes_todos_modelos, 0);

    // relleno blanco para ubicar las tablas
    // $sheet->getStyle('A1:J1000')->applyFromArray([
    //     'fill' => [
    //         'type' => PHPExcel_Style_Fill::FILL_SOLID,
    //         'color' => ['rgb' => 'FFFFFF']
    //     ]
    // ]);

    // Bordes de las celdas con informacion en par
    $styleBorderCellInfo = [
        'borders' => [
            'allborders' => [
                'style' => PHPExcel_Style_Border::BORDER_DASHED,
                'color' => ['rgb' => '727272']
            ]
        ]
    ];

// <------------------------------------------------------------------------->
// Definición de estilos
$styleBorderCellInfo = [
    'borders' => [
        'allborders' => [
            'style' => PHPExcel_Style_Border::BORDER_DASHED,
            'color' => ['rgb' => 'CCCCCC']
        ]
    ]
];

$styleEmptyRow = [
    'fill' => [
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => ['rgb' => 'EEEEEE']
    ],
    'borders' => [
        'allborders' => [
            'style' => PHPExcel_Style_Border::BORDER_HAIR,
            'color' => ['rgb' => 'CCCCCC']
        ]
    ]
];

// Colores del título por estado
$coloresEstado = [
    1 => 'ABEBC6', // Libre
    2 => 'FAD7A0', // Reservado
    3 => 'F1948A'  // Vendido
];

$cardsPerRow = 3;
$espacioEntreCards = 1;
$filasPorCard = 13;
$espacioEntreFilas = 2;
$columnaInicial = 'B';
$filaInicial = 2;
$cardCount = 0;

while ($plan = mysqli_fetch_array($planes_todos_modelos)) {

    $texto_cliente = isset($plan['cliente']) && !empty($plan['cliente']) ? $plan['cliente'] : "";
    
    // Determinar color de título según estado
    $colorFondoTitulo = isset($coloresEstado[$plan['estado_id']]) ? $coloresEstado[$plan['estado_id']] : 'FFFFFF';
    
    $valor_actual_plan = $plan['cuotas_pagadas_cantidad'] * $plan['cuota_promedio'];
    $ahorro_cliente = $valor_actual_plan - $plan['venta'];

    $tipoSituacion = $plan['situacion_id'] == 1 ? "Avanzados" : "Adjudicados";
    // Datos de la card
    $datosPlan = [
        [$tipoSituacion, null, false, true, null, false], // <-- título
        [$plan['modelo'] . ' ' . $plan['version'], null, false, true, null, true], // <-- título
        ["Modalidad: ".$plan['modalidad'], null, false, true, null, true], // <-- título
        ["Grupo y Orden: ".$plan['grupo_orden'], null, false, true, null, true], // <-- título
        ["Cuotas Pagas (" . $plan['cuotas_pagadas_cantidad'] . ")", $plan['cuotas_pagadas_monto'], true],
        ["Costo DYV", $plan['costo'], true],
        ["Plus", $plan['plus'], true, false, 'FF0000'],
        ["Integración", $plan['integracion'], true],
        ["Derecho Adjudicación", $plan['derecho_adjudicacion'], true],
        ["Precio Venta", $plan['venta'], true, true],
        ["", null, true, true], // fila vacía
        ["Cuota Promedio", $plan['cuota_promedio'], true, false, 'FF0000'],
        ["Valor de la unidad", $plan['valor_unidad'], true, false],
        
        // ["", null, true, true], // fila vacía
        // ["Cesión", $plan['cesion'], true, false], 

        // ["Total", $plan['precio_final'], true, true],
        // ["", null, true, true], // fila vacía
        // ["Valor Actual del Plan", $valor_actual_plan, true],
        // ["Ahorro Cliente", $ahorro_cliente, true, true, '0080FE'],
        // ["", null, true, true], // fila vacía
        // ["Reserva", $plan['monto_reserva'], true, false],
        // ["Cliente", $texto_cliente, true],
        // ["Asesor", $plan['usuario_venta'], true]
    ];

    // Calcular posición
    $colIndex = $cardCount % $cardsPerRow;
    $colLetraB = chr(ord($columnaInicial) + $colIndex * (2 + $espacioEntreCards));
    $colLetraC = chr(ord($colLetraB) + 1);
    $rowIndex = floor($cardCount / $cardsPerRow);
    $filaInicio = $filaInicial + $rowIndex * ($filasPorCard + $espacioEntreFilas);
    $fila = $filaInicio;
    $ultimaFilaCard = $filaInicio + count($datosPlan) - 1;

    foreach ($datosPlan as $dato) {
        $texto = $dato[0];
        $valor = $dato[1];
        $aplicarBorde = isset($dato[2]) ? $dato[2] : false;
        $negrita = isset($dato[3]) ? $dato[3] : false;
        $colorTexto = isset($dato[4]) ? $dato[4] : null;
        $esTitulo = isset($dato[5]) && $dato[5] === true;

        // Texto
        $sheet->setCellValue("{$colLetraB}{$fila}", " " . $texto);

        // Valor
        if ($valor !== null) {
            $sheet->setCellValue("{$colLetraC}{$fila}", $valor);
            if (is_numeric($valor)) {
                $sheet->getStyle("{$colLetraC}{$fila}")
                    ->getNumberFormat()
                    ->setFormatCode('"$" #,##0.00_-');
            }
        }

        // Estilo especial para filas vacías
        if (trim($texto) === "" && $valor === null) {
            $sheet->getStyle("{$colLetraB}{$fila}:{$colLetraC}{$fila}")
                ->applyFromArray($styleEmptyRow);
        }
        // Título con fondo de color
        elseif ($esTitulo) {
            $sheet->mergeCells("{$colLetraB}{$fila}:{$colLetraC}{$fila}");
            $sheet->getStyle("{$colLetraB}{$fila}")
                ->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                ->getStartColor()->setRGB($colorFondoTitulo);
            $sheet->getStyle("{$colLetraB}{$fila}")->getFont()->setBold(true);
            $sheet->getStyle("{$colLetraB}{$fila}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }
        // Borde normal
        elseif ($aplicarBorde) {
            $sheet->getStyle("{$colLetraB}{$fila}:{$colLetraC}{$fila}")
                ->applyFromArray($styleBorderCellInfo);
        }

        // Negrita
        if ($negrita && !$esTitulo) {
            $sheet->getStyle("{$colLetraB}{$fila}:{$colLetraC}{$fila}")
                ->getFont()->setBold(true);
        }

        // Color de texto
        if ($colorTexto) {
            $sheet->getStyle("{$colLetraB}{$fila}:{$colLetraC}{$fila}")
                ->getFont()->getColor()->setRGB($colorTexto);
        }

        $fila++;
    }

    // Borde grueso exterior de la card
    $sheet->getStyle("{$colLetraB}{$filaInicio}:{$colLetraC}{$ultimaFilaCard}")
        ->applyFromArray([
            'borders' => [
                'outline' => [
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

    $cardCount++;
}





    // while ($plan = mysqli_fetch_array($planes_todos_modelos)) {
    //     // Calcular posición de la card
    //     $columnaActual = chr(ord($columnaInicial) + ($cardCount % $cardsPerRow) * (2 + $espacioEntreCards));
        
    //     if ($cardCount > 0 && $cardCount % $cardsPerRow == 0) {
    //         $filaActual += 18; // Nueva fila después de cada conjunto de cards
    //     }

    //     // ACA GENERAR LA CARD
    //     // $sheet->setCellValue("M{$fila}", $plan['derecho_adjudicacion']);
    //     $sheet->setCellValue("B2", "Cuotas Pagas (" . $plan['cuotas_pagadas_cantidad'] . ")");
    //     $sheet->setCellValue("C2", $plan['cuotas_pagadas_monto']);
    //     $sheet->getStyle("C2")->getNumberFormat()->setFormatCode('"$" #,##0.00_-');
    //     $sheet->getStyle("B2:C2")->applyFromArray($styleBorderCellInfo);



    // $datosPlan = [
    //     ["Cuotas Pagas (" . $plan['cuotas_pagadas_cantidad'] . ")", $plan['cuotas_pagadas_monto'], true],
    //     ["Costo DYV", $plan['costo'], true],
    //     ["Cesión", $plan['cesion'], true, false, 'FF0000'], 
    //     ["Precio Venta", $plan['venta'], true, true], // negrita
    //     ["", , true, true], // Celda Vacia
    //     ["Plus", $plan['plus'], true, false, 'FF0000'], // rojo
    //     ["", , true, true], // Celda Vacia
    //     ["Integración", $plan['integracion'], true],
    //     ["Derecho Adjudicación", $plan['derecho_adjudicacion']],
    //     ["Total", $plan['precio_final'], true, true], // negrita
    //     ["", , true, true], // Celda Vacia
    //     ["Cuota Promedio", $plan['cuota_promedio'], true, false, 'FF0000'], // rojo
    //     ["Valor Actual del Plan", $plan['cuota_promedio'], true, false, 'FF0000'], // rojo
    //     ["Ahorro Cliente", $plan['cuota_promedio'], true, false, 'FF0000'], // rojo
    //     ["Valor de la unidad", $plan['valor_unidad']],
    //     ["", , true, true], // Celda Vacia
    //     ["Reserva", $plan['monto_reserva']]
    // ];



        
    //     // Generar la card
    //     // generarCardModelo1($sheet, $filaActual, $columnaActual, $plan);
    //     $cardCount++;
    // }
}

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
                'style' => \PHPExcel_Style_Border::BORDER_SLANTDASHDOT,
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
        $texto_cliente = isset($plan['cliente']) && !empty($plan['cliente']) ? $plan['cliente'] : "Cliente";
        $sheet->setCellValue("{$columna}{$fila}", $texto_cliente . " / " . $plan['usuario_venta']);
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
            'bottom' => ['style' => \PHPExcel_Style_Border::BORDER_MEDIUM]
        ];
    }
    
    $sheet->setCellValue($columna . $fila, $label);
    $sheet->setCellValue(chr(ord($columna)+1) . $fila, $valor);
    $sheet->getStyle($columna . $fila)->applyFromArray($estiloBase);
    $sheet->getStyle(chr(ord($columna)+1) . $fila)->applyFromArray($estiloBase);
    $sheet->getStyle(chr(ord($columna)+1) . $fila)->getNumberFormat()->setFormatCode('#,##0.00');
}
