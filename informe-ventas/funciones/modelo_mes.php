<?php
/**
 * modelo_mes.php — Comparativo mes a mes por modelo/versión.
 * Lo usan api.php (action=comp_modelo_mes, la tabla en pantalla) y los exports
 * export_modelo_mes_pdf.php / export_modelo_mes_excel.php, así lo impreso
 * sale de la misma consulta que lo que se ve.
 * Requiere funciones/where_builder.php.
 */

const MM_MESES_CORTO = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
const MM_MESES_LARGO = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto',
                        'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

// =============================================================
// ACTION: comp_modelo_mes — Comparativo mes a mes por modelo/versión
// Devuelve: { anio, rows: [{grupo, modelo, mes, cantidad}] }
// =============================================================
function get_comp_modelo_mes(): array
{
    $params   = [];
    $types    = '';
    $anio_req = intval($_REQUEST['anio'] ?? 0);

    if ($anio_req < 2015 || $anio_req > 2035) {
        // Sin año seleccionado → usar año actual
        $anio_req = intval(date('Y'));
        $params[] = $anio_req;
        $types   .= 'i';
        $where    = " AND YEAR(r.fecres) = ?";
        $params2  = [];
        $types2   = '';
        $where2   = build_where_clause($params2, $types2, true);
        $params   = array_merge($params, $params2);
        $types   .= $types2;
        $where   .= $where2;
    } else {
        $where = build_where_clause($params, $types);
    }

    $sql = "SELECT
        COALESCE(g.grupo, 'Usados') AS grupo,
        COALESCE(m.modelo, '—')     AS modelo,
        MONTH(r.fecres)             AS mes,
        COUNT(*)                    AS cantidad
    " . base_from_sql() . $where . "
    GROUP BY r.idgrupo, r.idmodelo, MONTH(r.fecres)
    ORDER BY
        (g.posicion IS NULL OR g.posicion = 0) ASC, COALESCE(g.posicion, 9999),
        COALESCE(g.grupo, 'Usados'),
        (m.posicion IS NULL OR m.posicion = 0) ASC, COALESCE(m.posicion, 9999),
        COALESCE(m.modelo, '—'),
        mes";

    $rows = exec_prepared($sql, $params, $types);

    return ['anio' => $anio_req, 'rows' => $rows ?? []];
}

// Pivot grupo > versión > mes, igual que renderCompModeloMes() en dashboard.js
// (respeta el orden que trae la query y sólo deja los meses con datos).
// Devuelve: { meses: [int], grupos: [{nombre, meses{mes:cant}, total, versiones: [{nombre, meses, total}]}],
//             tot_mes: {mes:cant}, total }
function modelo_mes_pivot(array $rows): array
{
    $grupos = [];
    $meses  = [];
    foreach ($rows as $r) {
        $g    = (string)$r['grupo'];
        $v    = (string)$r['modelo'];
        $mes  = intval($r['mes']);
        $cant = intval($r['cantidad']);

        if (!isset($grupos[$g])) {
            $grupos[$g] = ['nombre' => $g, 'meses' => [], 'total' => 0, 'versiones' => []];
        }
        if (!isset($grupos[$g]['versiones'][$v])) {
            $grupos[$g]['versiones'][$v] = ['nombre' => $v, 'meses' => [], 'total' => 0];
        }
        $grupos[$g]['versiones'][$v]['meses'][$mes] = ($grupos[$g]['versiones'][$v]['meses'][$mes] ?? 0) + $cant;
        $grupos[$g]['versiones'][$v]['total']      += $cant;
        $grupos[$g]['meses'][$mes]                  = ($grupos[$g]['meses'][$mes] ?? 0) + $cant;
        $grupos[$g]['total']                       += $cant;
        $meses[$mes] = true;
    }

    $meses = array_keys($meses);
    sort($meses);

    $tot_mes = array_fill_keys($meses, 0);
    $total   = 0;
    foreach ($grupos as &$g) {
        $g['versiones'] = array_values($g['versiones']);
        foreach ($g['meses'] as $mes => $cant) { $tot_mes[$mes] += $cant; }
        $total += $g['total'];
    }
    unset($g);

    return ['meses' => $meses, 'grupos' => array_values($grupos), 'tot_mes' => $tot_mes, 'total' => $total];
}

// Variación % contra el mes activo anterior (null = no se muestra), como pctBadge() del JS
function modelo_mes_var(int $actual, int $anterior): ?float
{
    if ($anterior <= 0) return null;
    return round(($actual - $anterior) / $anterior * 100, 1);
}

// Filtros aplicados en texto legible ("Sucursal: X", …) para el encabezado de los exports
function modelo_mes_filtros(): array
{
    $out = [];
    $mes = intval($_REQUEST['mes'] ?? 0);
    if ($mes >= 1 && $mes <= 12) $out[] = 'Mes: ' . MM_MESES_LARGO[$mes];

    foreach (['fecha_desde' => 'Desde', 'fecha_hasta' => 'Hasta'] as $k => $label) {
        $f  = trim($_REQUEST[$k] ?? '');
        $dt = $f !== '' ? DateTime::createFromFormat('Y-m-d', $f) : false;
        if ($dt && $dt->format('Y-m-d') === $f) $out[] = $label . ': ' . $dt->format('d/m/Y');
    }

    $lookups = [
        'idsucursal' => ['Sucursal', 'SELECT sucursal AS n FROM sucursales WHERE idsucursal = ?'],
        'idusuario'  => ['Vendedor', 'SELECT nombre AS n FROM usuarios WHERE idusuario = ?'],
        'idgrupo'    => ['Modelo',   'SELECT grupo AS n FROM grupos WHERE idgrupo = ?'],
        'idmodelo'   => ['Versión',  'SELECT modelo AS n FROM modelos WHERE idmodelo = ?'],
    ];
    foreach ($lookups as $k => $l) {
        $id = intval($_REQUEST[$k] ?? 0);
        if ($id <= 0) continue;
        $row   = exec_prepared_one($l[1], [$id], 'i');
        $out[] = $l[0] . ': ' . ($row['n'] ?? ('#' . $id));
    }

    $marca = trim($_REQUEST['marca'] ?? '');
    if ($marca !== '') $out[] = 'Marca: ' . $marca;

    $flags = [
        'anulada'    => [0 => 'Sin anuladas', 1 => 'Sólo anuladas'],
        'credito'    => [0 => 'Contado',      1 => 'Con crédito'],
        'toma_usado' => [0 => 'Sin toma',     1 => 'Con toma de usado'],
    ];
    foreach ($flags as $k => $txt) {
        $raw = $_REQUEST[$k] ?? '';
        if ($raw === '' || $raw === '-1') continue;
        $v = intval($raw);
        if (isset($txt[$v])) $out[] = $txt[$v];
    }

    $compra = strtolower(trim($_REQUEST['compra'] ?? ''));
    if ($compra === 'nuevo' || $compra === 'usado') $out[] = 'Compra: ' . ucfirst($compra);

    return $out;
}
