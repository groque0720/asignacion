<?php
/**
 * export_modelo_mes_excel.php — Excel (.xlsx) del "Comparativo Mes a Mes — Por Modelo/Versión".
 * Mismos filtros y misma consulta que la tabla en pantalla (funciones/modelo_mes.php).
 * ?detalle=1 agrega las versiones debajo de cada modelo, agrupadas (se pliegan con el +/- de Excel).
 */

// PHPExcel es viejo: sus deprecations/notices romperían el binario. Silenciar + bufferizar.
error_reporting(E_ERROR | E_PARSE);
@ini_set('display_errors', '0');
ob_start();

include("funciones/func_mysql.php");
include("funciones/where_builder.php");
include("funciones/modelo_mes.php");
conectar();
@session_start();
if (!isset($_SESSION["autentificado"]) || $_SESSION["autentificado"] !== "SI") {
    header("Location: ../login");
    exit();
}

require_once __DIR__ . '/../asignacion/Classes/PHPExcel.php';

// Datos legacy en latin1 romperían el XML del .xlsx
function mm_utf8($s): string
{
    $s = (string)$s;
    return mb_check_encoding($s, 'UTF-8') ? $s : utf8_encode($s);
}

$detalle = intval($_REQUEST['detalle'] ?? 0) === 1;
$data    = get_comp_modelo_mes();
$anio    = $data['anio'];
$p       = modelo_mes_pivot($data['rows']);
$meses   = $p['meses'];

$nCols   = count($meses) + 2;   // nombre + meses + total
$lastCol = PHPExcel_Cell::stringFromColumnIndex($nCols - 1);
$NUM     = '#,##0;-#,##0;"—"';  // el 0 se ve como "—", igual que en pantalla

$xls = new PHPExcel();
$xls->getProperties()->setCreator('Derka y Vargas S.A.')->setTitle('Comparativo mes a mes ' . $anio);
$sheet = $xls->getActiveSheet();
$sheet->setTitle('Mes a mes ' . $anio);
$sheet->setShowGridlines(false);
$sheet->setShowSummaryBelow(false);  // el +/- del agrupado queda en la fila del modelo
$sheet->getPageSetup()
    ->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE)
    ->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4)
    ->setFitToWidth(1)->setFitToHeight(0);

$sheet->getColumnDimension('A')->setWidth($detalle ? 44 : 26);
for ($i = 1; $i < $nCols; $i++) {
    $sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($i))->setWidth($i === $nCols - 1 ? 10 : 8.5);
}

// Filas 1-3: título, alcance + filtros, generado
$filtros = modelo_mes_filtros();
$lineas = [
    ['Comparativo mes a mes por modelo y versión — Año ' . $anio, ['bold' => true, 'size' => 13, 'color' => ['rgb' => '1E2235']]],
    ['Reservas por mes  ·  ' . ($detalle ? 'Modelos y versiones' : 'Solo modelos')
        . ($filtros ? '  ·  Filtros: ' . implode('  ·  ', $filtros) : ''), ['size' => 9, 'color' => ['rgb' => '555E70']]],
    ['Generado el ' . date('d/m/Y H:i') . ' por ' . ($_SESSION['usuario'] ?? ''), ['italic' => true, 'size' => 8, 'color' => ['rgb' => '8C93A2']]],
];
foreach ($lineas as $i => $l) {
    $r = $i + 1;
    $sheet->mergeCells('A' . $r . ':' . $lastCol . $r);
    $sheet->setCellValueExplicit('A' . $r, mm_utf8($l[0]), PHPExcel_Cell_DataType::TYPE_STRING);
    $sheet->getStyle('A' . $r)->applyFromArray(['font' => $l[1]]);
}
$sheet->getRowDimension(1)->setRowHeight(22);

// Fila 5: encabezado de la tabla
$hr = 5;
$sheet->setCellValueByColumnAndRow(0, $hr, $detalle ? 'Modelo / Versión' : 'Modelo');
foreach ($meses as $i => $m) {
    $sheet->setCellValueByColumnAndRow($i + 1, $hr, strtoupper(MM_MESES_CORTO[$m]));
}
$sheet->setCellValueByColumnAndRow($nCols - 1, $hr, 'TOTAL');
$sheet->getStyle('A' . $hr . ':' . $lastCol . $hr)->applyFromArray([
    'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '555F73']],
    'fill'      => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => ['rgb' => 'F1F4F9']],
    'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER],
    'borders'   => ['bottom' => ['style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => ['rgb' => 'C4CAD6']]],
]);
$sheet->getStyle('A' . $hr)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$sheet->getStyle($lastCol . $hr)->getFont()->getColor()->setRGB('1E40A0');
$sheet->getRowDimension($hr)->setRowHeight(20);
$sheet->freezePane('B' . ($hr + 1));
$sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($hr, $hr);

// Escribe una fila (nombre + cantidades por mes + total) y devuelve el rango para darle estilo
$fila = function (int $r, string $nombre, array $porMes, int $total) use ($sheet, $meses, $nCols, $lastCol, $NUM): string {
    $sheet->setCellValueExplicitByColumnAndRow(0, $r, mm_utf8($nombre), PHPExcel_Cell_DataType::TYPE_STRING);
    foreach ($meses as $i => $m) {
        $sheet->setCellValueByColumnAndRow($i + 1, $r, intval($porMes[$m] ?? 0));
    }
    $sheet->setCellValueByColumnAndRow($nCols - 1, $r, $total);
    $sheet->getStyle('B' . $r . ':' . $lastCol . $r)->getNumberFormat()->setFormatCode($NUM);
    return 'A' . $r . ':' . $lastCol . $r;
};

$r = $hr + 1;
if (empty($p['grupos'])) {
    $sheet->mergeCells('A' . $r . ':' . $lastCol . $r);
    $sheet->setCellValue('A' . $r, 'Sin datos para el período seleccionado.');
    $sheet->getStyle('A' . $r)->getFont()->setItalic(true)->getColor()->setRGB('8C93A2');
    $r++;
} else {
    foreach ($p['grupos'] as $gi => $g) {
        $rango = $fila($r, $g['nombre'], $g['meses'], $g['total']);
        $estilo = ['font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '1E2235']]];
        if ($detalle || $gi % 2) {
            $estilo['fill'] = ['type' => PHPExcel_Style_Fill::FILL_SOLID,
                               'startcolor' => ['rgb' => $detalle ? 'F3F6FB' : 'FAFBFD']];
        }
        $sheet->getStyle($rango)->applyFromArray($estilo);
        $sheet->getRowDimension($r)->setRowHeight(18);
        $r++;

        if (!$detalle) continue;
        // Una versión sola sin nombre ("—", típico de Usados) repetiría la fila del modelo
        $versiones = $g['versiones'];
        if (count($versiones) === 1 && $versiones[0]['nombre'] === '—') continue;
        foreach ($versiones as $v) {
            $rango = $fila($r, $v['nombre'], $v['meses'], $v['total']);
            $sheet->getStyle($rango)->getFont()->setSize(9)->getColor()->setRGB('4B5569');
            $sheet->getStyle('A' . $r)->getAlignment()->setIndent(2);
            $sheet->getRowDimension($r)->setOutlineLevel(1);
            $r++;
        }
    }

    $rango = $fila($r, 'TOTAL GENERAL', $p['tot_mes'], $p['total']);
    $sheet->getStyle($rango)->applyFromArray([
        'font'    => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '1E3A8A']],
        'fill'    => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => ['rgb' => 'E7ECF5']],
        'borders' => ['top' => ['style' => PHPExcel_Style_Border::BORDER_MEDIUM, 'color' => ['rgb' => 'A0AABE']]],
    ]);
    $sheet->getStyle('A' . $r)->getFont()->getColor()->setRGB('1E2235');
    $sheet->getRowDimension($r)->setRowHeight(20);
    $r++;
}

// Líneas suaves: horizontales entre filas + separadores tras el nombre y antes del total
$ultima = $r - 1;
$suave  = ['style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => ['rgb' => 'E4E7EE']];
$sheet->getStyle('A' . ($hr + 1) . ':' . $lastCol . $ultima)->applyFromArray([
    'borders'   => ['horizontal' => $suave, 'bottom' => $suave],
    'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER],
]);
$sheet->getStyle('A' . ($hr + 1) . ':A' . $ultima)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$sheet->getStyle('A' . $hr . ':A' . $ultima)->applyFromArray(['borders' => ['right' => $suave]]);
$sheet->getStyle($lastCol . $hr . ':' . $lastCol . $ultima)->applyFromArray([
    'borders' => ['left' => $suave],
    'font'    => ['bold' => true],
]);

// Salida
$nombre = 'Comparativo_mes_a_mes_' . $anio . ($detalle ? '_versiones' : '') . '.xlsx';
if (ob_get_level()) ob_end_clean();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $nombre . '"');
header('Cache-Control: max-age=0');
PHPExcel_IOFactory::createWriter($xls, 'Excel2007')->save('php://output');
exit;
