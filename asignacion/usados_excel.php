<?php

include_once("funciones/func_mysql.php");
$con = conectar();
mysqli_query($con, "SET NAMES 'utf8'");
require_once 'Classes/PHPExcel.php';

@session_start();
if ($_SESSION["autentificado"] != "SI") {
    header("Location: ../login");
    exit();
}

$p          = $_SESSION["idperfil"];
$es_gerente = $_SESSION["es_gerente"];
$id_usuario = $_SESSION["id"];

// !se habilita a Angel Peulich (17) a ver los importes de gerente, igual que en pantalla
$es_gerente = in_array($id_usuario, [17]) ? 1 : $es_gerente;
$ver_0km    = ($id_usuario == 47 || $id_usuario == 89);

// === lookups (mismo orden y misma indexación que carga_unidades_usados.php) ===
$SQL = "SELECT * FROM asignaciones_usados_colores ORDER BY color";
$colores = mysqli_query($con, $SQL);
$color_a[0]['color'] = '-';
while ($color = mysqli_fetch_array($colores)) {
    $color_a[$color['id_color']]['color'] = $color['color'];
}

$SQL = "SELECT * FROM sucursales";
$sucursales = mysqli_query($con, $SQL);
$sucursal_a[0]['sucres'] = '-';
$i = 1;
while ($sucursal = mysqli_fetch_array($sucursales)) {
    $sucursal_a[$i]['sucres'] = $sucursal['sucres'];
    $i++;
}

$SQL = "SELECT * FROM usuarios WHERE idperfil = 3";
$usuarios = mysqli_query($con, $SQL);
$usuario_a[1]['nombre'] = '-';
while ($usuario = mysqli_fetch_array($usuarios)) {
    $usuario_a[$usuario['idusuario']]['nombre'] = $usuario['nombre'];
}

$SQL = "SELECT * FROM grupos WHERE activo = 1";
$grupos = mysqli_query($con, $SQL);
$por_a[]['grupo_res'] = '-';
while ($grupo = mysqli_fetch_array($grupos)) {
    $por_a[$grupo['idgrupo']]['grupo_res'] = $grupo['grupo_res'];
}

// === armo lista dinámica de columnas según permisos del usuario ===
$cols = [];
$cols[] = ['title' => 'Nro',                       'width' => 5];
$cols[] = ['title' => 'Un.',                       'width' => 7];
$cols[] = ['title' => 'Interno',                   'width' => 8];
$cols[] = ['title' => 'Marca - Modelo - Versión',  'width' => 42];
$cols[] = ['title' => 'Por',                       'width' => 6];
$cols[] = ['title' => 'Año',                       'width' => 6];
$cols[] = ['title' => 'KM',                        'width' => 9];
$cols[] = ['title' => 'Dominio',                   'width' => 10];
$cols[] = ['title' => 'Color',                     'width' => 11];
$cols[] = ['title' => 'Último Dueño',              'width' => 22];
$cols[] = ['title' => 'Asesor T.',                 'width' => 16];
$cols[] = ['title' => 'Recepción',                 'width' => 11];
$cols[] = ['title' => 'Ant.',                      'width' => 6];
if ($es_gerente == 1) {
    $cols[] = ['title' => 'Toma + Imp.',           'width' => 13];
    $cols[] = ['title' => 'Costo Cont.',           'width' => 13];
    $cols[] = ['title' => 'Costo Rep.',            'width' => 13];
    $cols[] = ['title' => '$ Info',                'width' => 13];
}
$cols[] = ['title' => '$ Transf.',                 'width' => 13];
$cols[] = ['title' => '$ Venta',                   'width' => 14];
$cols[] = ['title' => '$ Contado',                 'width' => 14];
if ($ver_0km) {
    $cols[] = ['title' => '$ 0km',                 'width' => 13];
}
$cols[] = ['title' => 'Suc.',                      'width' => 7];
$cols[] = ['title' => 'Canc.',                     'width' => 7];
$cols[] = ['title' => 'Cliente',                   'width' => 20];
$cols[] = ['title' => 'Asesor',                    'width' => 16];

$totalCols = count($cols);
$lastColLetter = PHPExcel_Cell::stringFromColumnIndex($totalCols - 1);

// índice dinámico de la columna $ Venta (para el subtotal por estado)
$idxVenta = null;
foreach ($cols as $idx => $c) {
    if ($c['title'] === '$ Venta') { $idxVenta = $idx; break; }
}

$xls = new PHPExcel();
$xls->getProperties()
    ->setCreator('Sistema Asignación')
    ->setTitle('Planilla Usados')
    ->setSubject('Planilla de Unidades Usadas');

$sheet = $xls->getActiveSheet();
$sheet->setTitle('Usados');

// === Page setup A4 horizontal, ajustar al ancho ===
$sheet->getPageSetup()
    ->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE)
    ->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4)
    ->setFitToWidth(1)
    ->setFitToHeight(0)
    ->setHorizontalCentered(true);

$sheet->getPageMargins()->setTop(0.6);
$sheet->getPageMargins()->setBottom(0.5);
$sheet->getPageMargins()->setLeft(0.3);
$sheet->getPageMargins()->setRight(0.3);
$sheet->getPageMargins()->setHeader(0.25);
$sheet->getPageMargins()->setFooter(0.2);

// Pie de página mínimo (solo nro de página, sin códigos de formato que algunos
// Excel mal-interpretan como texto gigante)
$sheet->getHeaderFooter()->setOddFooter('&CPagina &P de &N');

// Anchos columnas
foreach ($cols as $idx => $c) {
    $letter = PHPExcel_Cell::stringFromColumnIndex($idx);
    $sheet->getColumnDimension($letter)->setWidth($c['width']);
}

// === Fila 1: Título general (merge a todo el ancho) ===
$fecha = date('d/m/Y H:i');
$sheet->mergeCells('A1:' . $lastColLetter . '1');
$sheet->setCellValue('A1', 'DERKA Y VARGAS S.A.   —   PLANILLA DE UNIDADES USADAS   —   ' . $fecha);
$sheet->getStyle('A1')->applyFromArray([
    'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '1F3A6B']],
    'alignment' => [
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
    ],
]);
$sheet->getRowDimension(1)->setRowHeight(22);

// === Fila 2: Títulos de columna ===
$row = 2;
foreach ($cols as $idx => $c) {
    $sheet->setCellValueByColumnAndRow($idx, $row, $c['title']);
}
$sheet->getStyle('A2:' . $lastColLetter . '2')->applyFromArray([
    'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']],
    'fill'      => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => ['rgb' => '4A6FA5']],
    'alignment' => [
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        'wrap'       => true,
    ],
    'borders'   => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN]],
]);
$sheet->getRowDimension(2)->setRowHeight(28);

// Freeze y repetir título + cabecera en cada página impresa
$sheet->freezePane('A3');
$sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 2);

$row = 3;

// === Recorro estados y unidades ===
$SQL = "SELECT * FROM asignaciones_usados_estados ORDER BY posicion";
$estado_usado = mysqli_query($con, $SQL);

// misma lista de usuarios habilitados a ver estados extra que carga_unidades_usados.php
$user_permitidos = [1, 2, 11, 16, 17, 20, 37, 75, 128, 27, 36, 41, 45, 46, 47, 49, 56, 71, 72, 89, 94, 96, 103, 106, 124, 135, 146];

while ($estado = mysqli_fetch_array($estado_usado)) {

    if (!($estado['id_estado_usado'] == 1 || $estado['id_estado_usado'] == 4 || in_array($id_usuario, $user_permitidos))) {
        continue;
    }

    $SQL = "SELECT *, DATEDIFF(DATE(NOW()),fec_recepcion) AS ant
              FROM asignaciones_usados
             WHERE entregado = 0 AND id_estado = " . (int)$estado['id_estado_usado'] . "
             ORDER BY vehiculo";
    $usados = mysqli_query($con, $SQL);
    $cant = mysqli_num_rows($usados);
    if ($cant == 0) continue;

    // --- Título de estado (fila merge a todo el ancho)
    $sheet->mergeCells('A' . $row . ':' . $lastColLetter . $row);
    $sheet->setCellValue('A' . $row, $estado['estado_usado'] . '  —  ' . $cant . ' un.');
    $sheet->getStyle('A' . $row)->applyFromArray([
        'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
        'fill'      => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => ['rgb' => '6B86B5']],
        'alignment' => [
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        ],
    ]);
    $sheet->getRowDimension($row)->setRowHeight(20);
    $row++;

    $sumatoria_precio_venta = 0;
    $fila = 0;

    while ($u = mysqli_fetch_array($usados)) {
        $fila++;
        $sumatoria_precio_venta += $u['precio_venta'];

        // moneda por fila: si el nombre del vehículo contiene "usd" → U$D
        $moneda_format = '"$ "#,##0';
        if (strpos(strtolower($u['vehiculo']), 'usd') !== false) {
            $moneda_format = '"U$D "#,##0';
        }

        // sufijo UCT-ORO / UCT-PLATA al vehículo
        $vehiculo_extra = '';
        if ($u['id_estado_certificado'] == 2) $vehiculo_extra = ' (UCT-ORO)';
        if ($u['id_estado_certificado'] == 4) $vehiculo_extra = ' (UCT-PLATA)';

        // estado de cancelación (igual lógica que la planilla en pantalla)
        $canc = '-';
        if ($u['reservada'] == 1) {
            $canc = ($u['fecha_cancelacion'] == null) ? 'No' : 'Si';
        }

        $col = 0;
        $sheet->setCellValueByColumnAndRow($col++, $row, $fila);
        $sheet->setCellValueByColumnAndRow($col++, $row, $u['nro_unidad']);
        $sheet->setCellValueByColumnAndRow($col++, $row, $u['interno']);
        $sheet->setCellValueByColumnAndRow($col++, $row, $u['vehiculo'] . $vehiculo_extra);
        $sheet->setCellValueByColumnAndRow($col++, $row, isset($por_a[$u['por']]) ? $por_a[$u['por']]['grupo_res'] : '');
        $sheet->setCellValueByColumnAndRow($col++, $row, $u['año']);

        // KM
        $sheet->setCellValueByColumnAndRow($col, $row, (int)$u['km']);
        $sheet->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('#,##0');
        $col++;

        $sheet->setCellValueByColumnAndRow($col++, $row, $u['dominio']);
        $sheet->setCellValueByColumnAndRow($col++, $row, isset($color_a[$u['color']]) ? $color_a[$u['color']]['color'] : '');
        $sheet->setCellValueByColumnAndRow($col++, $row, $u['ultimo_dueño']);
        $sheet->setCellValueByColumnAndRow($col++, $row, isset($usuario_a[$u['asesortoma']]) ? $usuario_a[$u['asesortoma']]['nombre'] : '');
        $sheet->setCellValueByColumnAndRow($col++, $row, cambiarFormatoFecha($u['fec_recepcion']));
        $sheet->setCellValueByColumnAndRow($col++, $row, (int)$u['ant']);

        if ($es_gerente == 1) {
            $sheet->setCellValueByColumnAndRow($col, $row, (float)$u['toma_mas_impuesto']);
            $sheet->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('"$ "#,##0');
            $col++;

            $sheet->setCellValueByColumnAndRow($col, $row, (float)$u['costo_contable']);
            $sheet->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('"$ "#,##0');
            $col++;

            $sheet->setCellValueByColumnAndRow($col, $row, (float)$u['costo_reparacion']);
            $sheet->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('"$ "#,##0');
            $col++;

            $sheet->setCellValueByColumnAndRow($col, $row, (float)$u['precio_info']);
            $sheet->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('"$ "#,##0');
            $col++;
        }

        // $ Transf
        $sheet->setCellValueByColumnAndRow($col, $row, (float)$u['transferencia']);
        $sheet->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('"$ "#,##0');
        $col++;

        // $ Venta (moneda dinámica + negrita como en pantalla)
        $sheet->setCellValueByColumnAndRow($col, $row, (float)$u['precio_venta']);
        $sheet->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode($moneda_format);
        $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
        $col++;

        // $ Contado (misma moneda)
        $sheet->setCellValueByColumnAndRow($col, $row, (float)$u['precio_contado']);
        $sheet->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode($moneda_format);
        $col++;

        if ($ver_0km) {
            $sheet->setCellValueByColumnAndRow($col, $row, (float)$u['precio_0km']);
            $sheet->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode('"$ "#,##0');
            $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
            $col++;
        }

        $sheet->setCellValueByColumnAndRow($col++, $row, isset($sucursal_a[$u['id_sucursal']]) ? $sucursal_a[$u['id_sucursal']]['sucres'] : '');
        $sheet->setCellValueByColumnAndRow($col++, $row, $canc);
        $sheet->setCellValueByColumnAndRow($col++, $row, $u['cliente']);
        $sheet->setCellValueByColumnAndRow($col++, $row, isset($usuario_a[$u['id_asesor']]) ? $usuario_a[$u['id_asesor']]['nombre'] : '');

        // sombreado por antigüedad >=50 (mismo criterio que la pantalla)
        if ($u['ant'] >= 50) {
            $sheet->getStyle('A' . $row . ':' . $lastColLetter . $row)->getFill()
                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                ->getStartColor()->setRGB('E8E8DC');
        }

        $row++;
    }

    // --- Fila de subtotal del estado
    $sheet->mergeCells('A' . $row . ':' . PHPExcel_Cell::stringFromColumnIndex(4) . $row);
    $sheet->setCellValue('A' . $row, 'Total ' . $estado['estado_usado'] . ' (' . $cant . ' un.)');

    if ($idxVenta !== null) {
        $sheet->setCellValueByColumnAndRow($idxVenta, $row, $sumatoria_precio_venta);
        $sheet->getStyleByColumnAndRow($idxVenta, $row)->getNumberFormat()->setFormatCode('"$ "#,##0');
    }

    $sheet->getStyle('A' . $row . ':' . $lastColLetter . $row)->applyFromArray([
        'font'    => ['bold' => true, 'italic' => true, 'size' => 9],
        'fill'    => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => ['rgb' => 'DCE3F0']],
        'borders' => ['top' => ['style' => PHPExcel_Style_Border::BORDER_MEDIUM, 'color' => ['rgb' => '4A6FA5']]],
    ]);
    $row++;

    // fila en blanco entre estados
    $row++;
}

// Bordes finos en toda la zona de datos
$lastRow = $row - 1;
if ($lastRow > 1) {
    $sheet->getStyle('A1:' . $lastColLetter . $lastRow)
        ->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_HAIR);
}

// Alineación global del cuerpo
$sheet->getStyle('A2:' . $lastColLetter . $lastRow)->getAlignment()
    ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

// === Salida del archivo ===
$nombre = 'planilla_usados_' . date('Y-m-d_His') . '.xlsx';

if (ob_get_length()) ob_end_clean();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $nombre . '"');
header('Cache-Control: max-age=0');
header('Pragma: public');

$writer = PHPExcel_IOFactory::createWriter($xls, 'Excel2007');
$writer->save('php://output');
exit;
