<?php
/*
 * Lógica de datos del Dashboard · Descuentos (0km entregados). Requiere $con.
 * Prefijo dd_ = "dashboard descuentos".
 *
 * MODELO DE DATOS (confirmado analizando la base):
 *  - Unidad entregada = `asignaciones` con entregada=1, borrar=0, guardado=1
 *    (asignaciones es stock 0km; fec_entrega = fecha real de entrega).
 *  - Se vincula a la venta por  reservas.nrounidad = asignaciones.nro_unidad
 *    (relación ~1:1; reservas.anulada=0).
 *  - El descuento vive en `lineas_detalle` (movimiento=1 = desglose de la
 *    operación: Precio Lista + Flete − Descuento). Definición AMPLIA de descuento
 *    (elegida por el usuario): TODA línea de operación con monto < 0.
 *      operacion (neto) = SUM(monto)            [movimiento=1]
 *      descuento        = SUM(-monto) si monto<0 [movimiento=1]
 *      bruto (lista+flete) = operacion + descuento
 */

/**
 * Agrupa filas por una dimensión y arma {clave, entregadas, conDesc, monto,
 * penetracion, promedio}, ordenado por monto desc. La usan las actions.
 *   $keyFn(row)  -> clave del grupo
 *   $metaFn(row) -> campos extra del grupo (sólo la 1ra vez), ej. la sucursal del vendedor.
 */
function dd_agrupar($rows, $keyFn, $metaFn = null) {
    $g = [];
    foreach ($rows as $x) {
        $k = $keyFn($x);
        if ($k === '' || $k === null) continue;
        if (!isset($g[$k])) {
            $g[$k] = ['clave'=>$k, 'entregadas'=>0, 'conDesc'=>0, 'monto'=>0.0];
            if ($metaFn) $g[$k] += $metaFn($x);
        }
        $g[$k]['entregadas']++;
        $g[$k]['conDesc'] += $x['con_desc'];
        $g[$k]['monto']   += $x['descuento'];
    }
    $out = array_values($g);
    foreach ($out as &$o) {
        $o['monto']       = round($o['monto']);
        $o['penetracion'] = $o['entregadas'] ? round(100 * $o['conDesc'] / $o['entregadas'], 1) : 0;
        $o['promedio']    = $o['conDesc'] ? round($o['monto'] / $o['conDesc']) : 0;
    }
    unset($o);
    usort($out, function ($a, $b) { return $b['monto'] <=> $a['monto']; });
    return $out;
}

/** Normaliza texto legacy latin1 → UTF-8 antes del json_encode (datos Windows-1252). */
function dd_utf8($s) {
    if ($s === null || $s === '') return $s;
    if (function_exists('mb_check_encoding') && !mb_check_encoding($s, 'UTF-8')) {
        return mb_convert_encoding($s, 'UTF-8', 'Windows-1252');
    }
    return $s;
}

function dd_mes_nombre($mes) {
    $m = [1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',
          7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'];
    return $m[(int)$mes] ?? '';
}

/** ¿La cadena es una fecha válida YYYY-MM-DD? */
function dd_es_fecha($s) {
    return is_string($s) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $s) === 1;
}

/**
 * Condición SQL del período sobre a.fec_entrega: el rango Desde/Hasta tiene
 * prioridad; si no, el año. La comparten dd_filas y dd_opciones para que los
 * selects muestren sólo lo que tuvo entregas en el período elegido.
 */
function dd_periodo_where($f) {
    if (dd_es_fecha($f['desde']) && dd_es_fecha($f['hasta'])) {
        return "a.fec_entrega BETWEEN '{$f['desde']}' AND '{$f['hasta']}'";
    }
    return "YEAR(a.fec_entrega) = " . (int)$f['anio'];
}

/**
 * Opciones para los filtros, derivadas del universo de entregadas (todos los años).
 * Devuelve: anios[], sucursales[{id,nombre}], grupos[{id,nombre}], vendedores[{id,nombre}].
 */
function dd_opciones($con, $f) {
    $op = ['anios' => [], 'sucursales' => [], 'grupos' => [], 'vendedores' => []];

    // Años: SIEMPRE globales (es el propio selector de año).
    $rs = mysqli_query($con, "SELECT DISTINCT YEAR(a.fec_entrega) y
        FROM asignaciones a
        WHERE a.entregada=1 AND a.borrar=0 AND a.guardado=1 AND a.fec_entrega >= '2024-01-01'
        ORDER BY y DESC");
    while ($r = mysqli_fetch_assoc($rs)) if ((int)$r['y'] > 0) $op['anios'][] = (int)$r['y'];

    // Sucursal / Modelo / Vendedor: sólo lo que tuvo entregas en el PERÍODO elegido
    // (no se acotan entre sí, para poder cambiar de dimensión libremente).
    $periodo = dd_periodo_where($f);
    $base = "FROM asignaciones a
             JOIN reservas r ON r.nrounidad=a.nro_unidad AND r.anulada=0
             LEFT JOIN usuarios u ON u.idusuario=r.idusuario
             WHERE a.entregada=1 AND a.borrar=0 AND a.guardado=1 AND $periodo";

    $rs = mysqli_query($con, "SELECT s.idsucursal id, s.sucursal nombre
        FROM sucursales s WHERE s.idsucursal IN (SELECT DISTINCT u.idsucursal $base AND u.idsucursal IS NOT NULL)
        ORDER BY s.posicion ASC");
    while ($r = mysqli_fetch_assoc($rs)) $op['sucursales'][] = ['id'=>(int)$r['id'], 'nombre'=>dd_utf8($r['nombre'])];

    $rs = mysqli_query($con, "SELECT DISTINCT g.idgrupo id, g.grupo nombre
        FROM grupos g WHERE g.idgrupo IN (SELECT DISTINCT r.idgrupo $base AND r.idgrupo IS NOT NULL)
        ORDER BY g.grupo ASC");
    while ($r = mysqli_fetch_assoc($rs)) $op['grupos'][] = ['id'=>(int)$r['id'], 'nombre'=>dd_utf8($r['nombre'])];

    $rs = mysqli_query($con, "SELECT DISTINCT u.idusuario id, u.nombre nombre
        FROM usuarios u WHERE u.idusuario IN (SELECT DISTINCT r.idusuario $base AND r.idusuario IS NOT NULL)
        ORDER BY u.nombre ASC");
    while ($r = mysqli_fetch_assoc($rs)) $op['vendedores'][] = ['id'=>(int)$r['id'], 'nombre'=>dd_utf8($r['nombre'])];

    return $op;
}

/**
 * Filas (una por unidad 0km entregada) aplicando los filtros estructurales.
 * NO aplica "sólo con descuento" (eso es vista de tabla, en el cliente): las
 * filas sin descuento se necesitan para los KPIs de penetración.
 *
 * $f: ['anio'=>int, 'desde'=>'Y-m-d'|'', 'hasta'=>'Y-m-d'|'',
 *      'idsucursal'=>int, 'idgrupo'=>int, 'idvendedor'=>int]
 */
function dd_filas($con, $f) {
    $w = ["a.entregada=1", "a.borrar=0", "a.guardado=1", "r.anulada=0"];

    $w[] = dd_periodo_where($f);
    if ((int)$f['idsucursal'] > 0) $w[] = "u.idsucursal = " . (int)$f['idsucursal'];
    if ((int)$f['idgrupo']    > 0) $w[] = "r.idgrupo = "    . (int)$f['idgrupo'];
    if ((int)$f['idvendedor'] > 0) $w[] = "r.idusuario = "  . (int)$f['idvendedor'];

    $where = implode(' AND ', $w);

    $sql = "SELECT
                a.id_unidad, a.nro_unidad, a.fec_entrega, a.chasis, a.nro_orden,
                c.nombre  AS cliente,
                u.idusuario AS id_vendedor, u.nombre AS vendedor,
                u.idsucursal, s.sucursal,
                r.idgrupo, g.grupo  AS modelo,
                r.idmodelo, m.modelo AS version,
                COALESCE(ld.operacion, 0) AS operacion,
                COALESCE(ld.descuento, 0) AS descuento
            FROM asignaciones a
            JOIN reservas r ON r.nrounidad = a.nro_unidad AND r.anulada = 0
            LEFT JOIN clientes  c ON c.idcliente = r.idcliente
            LEFT JOIN usuarios  u ON u.idusuario = r.idusuario
            LEFT JOIN sucursales s ON s.idsucursal = u.idsucursal
            LEFT JOIN grupos    g ON g.idgrupo = r.idgrupo
            LEFT JOIN modelos   m ON m.idmodelo = r.idmodelo
            LEFT JOIN (
                SELECT idreserva,
                       SUM(monto) AS operacion,
                       SUM(CASE WHEN monto < 0 THEN -monto ELSE 0 END) AS descuento
                FROM lineas_detalle
                WHERE movimiento = 1 AND idcodigo > 0
                GROUP BY idreserva
            ) ld ON ld.idreserva = r.idreserva
            WHERE $where
            ORDER BY a.fec_entrega DESC, a.nro_unidad DESC";

    $res = mysqli_query($con, $sql);
    if (!$res) return ['error' => mysqli_error($con)];

    $rows = [];
    while ($r = mysqli_fetch_assoc($res)) {
        $desc = (float)$r['descuento'];
        $oper = (float)$r['operacion'];
        $rows[] = [
            'id_unidad'  => (int)$r['id_unidad'],
            'nro_unidad' => (int)$r['nro_unidad'],
            'fecha'      => $r['fec_entrega'],
            'chasis'     => $r['chasis'],
            'nro_orden'  => $r['nro_orden'],
            'cliente'    => dd_utf8($r['cliente']),
            'id_vendedor'=> (int)$r['id_vendedor'],
            'vendedor'   => dd_utf8($r['vendedor']) ?: '(sin asesor)',
            'idsucursal' => (int)$r['idsucursal'],
            'sucursal'   => dd_utf8($r['sucursal']) ?: '(sin sucursal)',
            'modelo'     => dd_utf8($r['modelo']) ?: '(sin modelo)',
            'version'    => dd_utf8($r['version']),
            'operacion'  => $oper,
            'descuento'  => $desc,
            'bruto'      => $oper + $desc,
            'con_desc'   => $desc > 0 ? 1 : 0,
        ];
    }
    return ['rows' => $rows];
}
