<?php
/**
 * export_modelo_mes_pdf.php — PDF (A4 horizontal) del "Comparativo Mes a Mes — Por Modelo/Versión".
 * Mismos filtros y misma consulta que la tabla en pantalla (funciones/modelo_mes.php).
 * ?detalle=1 agrega las versiones debajo de cada modelo; si no, sólo modelos (grupos).
 */

require __DIR__ . '/../asignacion/fpdf/fpdf.php';
include("funciones/func_mysql.php");
include("funciones/where_builder.php");
include("funciones/modelo_mes.php");
conectar();
@session_start();
if (!isset($_SESSION["autentificado"]) || $_SESSION["autentificado"] !== "SI") {
    header("Location: ../login");
    exit();
}

// FPDF usa fuentes core en cp1252; los datos vienen en UTF-8 (salvo legacy latin1)
function mm_txt($s): string
{
    $s = (string)$s;
    if (!mb_check_encoding($s, 'UTF-8')) return $s;
    $r = @iconv('UTF-8', 'windows-1252//TRANSLIT', $s);
    return $r === false ? utf8_decode($s) : $r;
}

function mm_num(int $n): string
{
    return number_format($n, 0, ',', '.');
}

function mm_var(float $v): string
{
    return ($v >= 0 ? '+' : '') . number_format($v, 1, ',', '.') . '%';
}

class ModeloMesPDF extends FPDF
{
    const MARGEN = 10;
    const Y_TABLA = 29;      // donde arranca el encabezado de la tabla
    const ALTO_HEAD = 7;
    const LIMITE_Y = 196;    // por debajo va el pie

    public $titulo    = '';
    public $subtitulo = '';
    public $filtros   = '';
    public $pie       = '';
    public $cols      = [];  // [['w' => mm, 'label' => texto], ...] — nombre, meses…, total
    public $x0        = self::MARGEN;

    function anchoTabla(): float
    {
        return array_sum(array_column($this->cols, 'w'));
    }

    // Recorta con "…" para que entre en $w mm con la fuente actual
    function ajustar(string $s, float $w): string
    {
        if ($this->GetStringWidth($s) <= $w) return $s;
        while ($s !== '' && $this->GetStringWidth($s . chr(133)) > $w) {
            $s = substr($s, 0, -1);
        }
        return rtrim($s) . chr(133);
    }

    // Separadores verticales suaves: después del nombre y antes del total
    function separadores(float $y, float $h)
    {
        $this->SetDrawColor(226, 230, 237);
        $xn = $this->x0 + $this->cols[0]['w'];
        $xt = $this->x0 + $this->anchoTabla() - end($this->cols)['w'];
        $this->Line($xn, $y, $xn, $y + $h);
        $this->Line($xt, $y, $xt, $y + $h);
    }

    function Header()
    {
        $ancho = $this->GetPageWidth() - 2 * self::MARGEN;

        $this->SetXY(self::MARGEN, 9);
        $this->SetFont('Helvetica', 'B', 13);
        $this->SetTextColor(30, 34, 53);
        $this->Cell($ancho, 7, $this->titulo, 0, 0, 'L');
        $this->SetXY(self::MARGEN, 9);
        $this->SetFont('Helvetica', '', 8);
        $this->SetTextColor(120, 128, 145);
        $this->Cell($ancho, 7, 'Derka y Vargas S.A.', 0, 0, 'R');

        $this->SetXY(self::MARGEN, 16);
        $this->SetFont('Helvetica', '', 8.5);
        $this->SetTextColor(85, 94, 112);
        $this->Cell($ancho, 5, $this->subtitulo, 0, 0, 'L');

        if ($this->filtros !== '') {
            $this->SetXY(self::MARGEN, 21);
            $this->SetFont('Helvetica', 'I', 7.5);
            $this->SetTextColor(120, 128, 145);
            $this->Cell($ancho, 4, $this->ajustar($this->filtros, $ancho - 2), 0, 0, 'L');
        }

        $this->SetDrawColor(220, 224, 232);
        $this->SetLineWidth(0.2);
        $this->Line(self::MARGEN, 26.5, self::MARGEN + $ancho, 26.5);

        // Encabezado de la tabla (se repite en cada página)
        $y  = self::Y_TABLA;
        $h  = self::ALTO_HEAD;
        $tw = $this->anchoTabla();
        $this->SetFillColor(241, 244, 249);
        $this->Rect($this->x0, $y, $tw, $h, 'F');
        $this->SetFont('Helvetica', 'B', 7);
        $x    = $this->x0;
        $last = count($this->cols) - 1;
        foreach ($this->cols as $i => $c) {
            if ($i === $last) $this->SetTextColor(30, 64, 160);
            else              $this->SetTextColor(85, 95, 115);
            if ($i === 0) {
                $this->SetXY($x + 1.5, $y);
                $this->Cell($c['w'] - 1.5, $h, $c['label'], 0, 0, 'L');
            } else {
                $this->SetXY($x, $y);
                $this->Cell($c['w'], $h, $c['label'], 0, 0, 'C');
            }
            $x += $c['w'];
        }
        $this->separadores($y, $h);
        $this->SetDrawColor(196, 202, 214);
        $this->Line($this->x0, $y + $h, $this->x0 + $tw, $y + $h);
        $this->SetXY($this->x0, $y + $h);
    }

    function Footer()
    {
        $ancho = $this->GetPageWidth() - 2 * self::MARGEN;
        $y     = $this->GetPageHeight() - 10;
        $this->SetDrawColor(226, 230, 237);
        $this->Line(self::MARGEN, $y - 1.5, self::MARGEN + $ancho, $y - 1.5);
        $this->SetFont('Helvetica', '', 7);
        $this->SetTextColor(140, 147, 162);
        $this->SetXY(self::MARGEN, $y);
        $this->Cell($ancho, 4, $this->pie, 0, 0, 'L');
        // FPDF alinea midiendo el alias "{nb}" y no el número que lo reemplaza: compensar esa diferencia
        $extra = $this->GetStringWidth('{nb}') - $this->GetStringWidth('0');
        $this->SetXY(self::MARGEN, $y);
        $this->Cell($ancho + $extra, 4, mm_txt('Página ') . $this->PageNo() . ' de {nb}', 0, 0, 'R');
    }

    // Salta de página si no entran $alto mm más
    function asegurar(float $alto)
    {
        if ($this->GetY() + $alto > self::LIMITE_Y) $this->AddPage();
    }

    // $tipo: 'grupo' | 'version' | 'total'. $celdas: [[cantidad, variación|null], ...] por mes activo
    function Fila(string $tipo, string $nombre, array $celdas, int $total, ?array $fondo)
    {
        $h  = self::alto($tipo);
        $y  = $this->GetY();
        $tw = $this->anchoTabla();

        if ($fondo) {
            $this->SetFillColor($fondo[0], $fondo[1], $fondo[2]);
            $this->Rect($this->x0, $y, $tw, $h, 'F');
        }
        if ($tipo === 'total') {
            $this->SetDrawColor(160, 170, 190);
            $this->SetLineWidth(0.35);
            $this->Line($this->x0, $y, $this->x0 + $tw, $y);
            $this->SetLineWidth(0.2);
        }

        // Nombre
        $wN     = $this->cols[0]['w'];
        $indent = $tipo === 'version' ? 6 : 1.5;
        if ($tipo === 'version') {
            $this->SetFont('Helvetica', '', 7);
            $this->SetTextColor(75, 85, 105);
        } else {
            $this->SetFont('Helvetica', 'B', $tipo === 'total' ? 8.5 : 8);
            $this->SetTextColor(30, 34, 53);
        }
        $this->SetXY($this->x0 + $indent, $y);
        $this->Cell($wN - $indent, $h, $this->ajustar($nombre, $wN - $indent - 2.5), 0, 0, 'L');

        // Meses
        $x = $this->x0 + $wN;
        foreach ($celdas as $i => $c) {
            $w = $this->cols[$i + 1]['w'];
            $this->valor($x, $y, $w, $h, $c[0], $c[1], $tipo, false);
            $x += $w;
        }
        // Total de la fila
        $this->valor($x, $y, end($this->cols)['w'], $h, $total, null, $tipo, true);

        $this->separadores($y, $h);
        $this->SetDrawColor(228, 231, 238);
        $this->Line($this->x0, $y + $h, $this->x0 + $tw, $y + $h);
        $this->SetXY($this->x0, $y + $h);
    }

    static function alto(string $tipo): float
    {
        return $tipo === 'version' ? 7 : ($tipo === 'total' ? 9 : 8.4);
    }

    // Cantidad arriba y, si corresponde, la variación % chiquita abajo (como en pantalla)
    function valor(float $x, float $y, float $w, float $h, int $val, ?float $var, string $tipo, bool $esTotal)
    {
        $tam = $tipo === 'version' ? 7 : ($tipo === 'total' ? 8.5 : 8);
        $this->SetFont('Helvetica', ($tipo !== 'version' || $esTotal) ? 'B' : '', $tam);
        if ($val > 0) {
            if ($tipo === 'total')       $this->SetTextColor(30, 58, 138);
            elseif ($tipo === 'version') $this->SetTextColor(55, 65, 85);
            else                         $this->SetTextColor(30, 34, 53);
            $txt = mm_num($val);
        } else {
            $this->SetTextColor(190, 196, 208);
            $txt = chr(151); // —
        }

        if ($var === null) {
            $this->SetXY($x, $y);
            $this->Cell($w, $h, $txt, 0, 0, 'C');
            return;
        }
        $top = $y + ($h - 6.2) / 2;
        $this->SetXY($x, $top);
        $this->Cell($w, 3.6, $txt, 0, 0, 'C');
        $this->SetFont('Helvetica', '', 6);
        if ($var >= 0) $this->SetTextColor(26, 140, 86);
        else           $this->SetTextColor(205, 60, 60);
        $this->SetXY($x, $top + 3.6);
        $this->Cell($w, 2.6, mm_var($var), 0, 0, 'C');
    }
}

// ── Datos ──────────────────────────────────────────────────────────────────────
$detalle = intval($_REQUEST['detalle'] ?? 0) === 1;
$data    = get_comp_modelo_mes();
$anio    = $data['anio'];
$p       = modelo_mes_pivot($data['rows']);
$meses   = $p['meses'];

// Celdas de una fila: cantidad + variación contra el mes activo anterior
function mm_celdas(array $porMes, array $meses): array
{
    $out = [];
    foreach ($meses as $i => $m) {
        $val   = intval($porMes[$m] ?? 0);
        $var   = $i > 0 ? modelo_mes_var($val, intval($porMes[$meses[$i - 1]] ?? 0)) : null;
        $out[] = [$val, $var];
    }
    return $out;
}

// ── Columnas: nombre + un ancho por mes activo + total; centrada si sobra lugar ──
$util   = 297 - 2 * ModeloMesPDF::MARGEN;
$wTotal = 18;
$wNom   = $detalle ? 70 : 54;
$n      = max(count($meses), 1);
$wMes   = min(19, ($util - $wNom - $wTotal) / $n);

$cols = [['w' => $wNom, 'label' => mm_txt($detalle ? 'MODELO / VERSIÓN' : 'MODELO')]];
foreach ($meses as $m) {
    $cols[] = ['w' => $wMes, 'label' => strtoupper(MM_MESES_CORTO[$m])];
}
$cols[] = ['w' => $wTotal, 'label' => 'TOTAL'];

$pdf = new ModeloMesPDF('L', 'mm', 'A4');
$pdf->cols      = $cols;
$pdf->x0        = ModeloMesPDF::MARGEN + max(0, ($util - $pdf->anchoTabla()) / 2);
$pdf->titulo    = mm_txt('Comparativo mes a mes por modelo y versión');
$pdf->subtitulo = mm_txt('Año ' . $anio . '  ·  Reservas por mes  ·  '
                . ($detalle ? 'Modelos y versiones' : 'Solo modelos')
                . '  ·  El % es la variación contra el mes anterior');
$filtros        = modelo_mes_filtros();
$pdf->filtros   = $filtros ? mm_txt('Filtros: ' . implode('  ·  ', $filtros)) : '';
$pdf->pie       = mm_txt('Generado el ' . date('d/m/Y H:i') . ' por ' . ($_SESSION['usuario'] ?? ''));
$pdf->SetTitle(mm_txt('Comparativo mes a mes ' . $anio));
$pdf->SetAuthor('Derka y Vargas S.A.');
$pdf->SetAutoPageBreak(false);
$pdf->AliasNbPages();
$pdf->AddPage();

if (empty($p['grupos'])) {
    $pdf->Ln(6);
    $pdf->SetX(ModeloMesPDF::MARGEN);
    $pdf->SetFont('Helvetica', 'I', 9);
    $pdf->SetTextColor(120, 128, 145);
    $pdf->Cell(0, 8, mm_txt('Sin datos para el período seleccionado.'), 0, 1, 'C');
} else {
    foreach ($p['grupos'] as $gi => $g) {
        // Una versión sola sin nombre ("—", típico de Usados) repetiría la fila del modelo
        $versiones = $g['versiones'];
        if (count($versiones) === 1 && $versiones[0]['nombre'] === '—') $versiones = [];

        if ($detalle) {
            // que el modelo no quede solo al pie de la página, separado de sus versiones
            $pdf->asegurar(ModeloMesPDF::alto('grupo') + ($versiones ? ModeloMesPDF::alto('version') : 0));
            $fondo = [243, 246, 251];
        } else {
            $pdf->asegurar(ModeloMesPDF::alto('grupo'));
            $fondo = $gi % 2 ? [250, 251, 253] : null;
        }
        $pdf->Fila('grupo', mm_txt($g['nombre']), mm_celdas($g['meses'], $meses), $g['total'], $fondo);

        if ($detalle) {
            foreach ($versiones as $v) {
                $pdf->asegurar(ModeloMesPDF::alto('version'));
                $pdf->Fila('version', mm_txt($v['nombre']), mm_celdas($v['meses'], $meses), $v['total'], null);
            }
        }
    }

    $pdf->asegurar(ModeloMesPDF::alto('total'));
    $pdf->Fila('total', 'TOTAL GENERAL', mm_celdas($p['tot_mes'], $meses), $p['total'], [231, 236, 245]);
}

$pdf->Output('I', 'Comparativo_mes_a_mes_' . $anio . ($detalle ? '_versiones' : '') . '.pdf');
