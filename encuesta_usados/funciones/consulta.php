<?php
/*
 * Lógica de datos del módulo Encuesta · Usados (la usan las actions).
 * Requiere $con (config_app.php). Prefijo eu_ = "encuesta usados".
 *
 * Fuente: vista view_asignaciones_usados_entregadas + tablas encu_*.
 * El estado de la encuesta de cada unidad se DERIVA por LEFT JOIN a encu_tokens
 * / encu_respuestas (no se toca asignaciones_usados):
 *   0 = sin generar | 1 = pendiente (token sin responder) | 2 = completada
 */

// Normaliza a UTF-8 válido (hay datos legacy de usados en latin1/cp1252 que
// romperían json_encode). Convierte sólo si la cadena no es ya UTF-8 válida.
function eu_utf8($s) {
    if ($s === null || $s === '') return $s;
    if (function_exists('mb_check_encoding') && !mb_check_encoding($s, 'UTF-8')) {
        return mb_convert_encoding($s, 'UTF-8', 'Windows-1252');
    }
    return $s;
}

// Nivel (nombre + color) para un score, desde encu_niveles. Fallback hardcodeado.
function eu_nivel($score) {
    global $con;
    $s = (float)$score;
    $res = mysqli_query($con,
        "SELECT nombre, color FROM encu_niveles
         WHERE $s >= valor_desde AND $s <= valor_hasta
         ORDER BY valor_desde DESC LIMIT 1");
    if ($res && mysqli_num_rows($res) > 0) return mysqli_fetch_assoc($res);
    if ($s >= 9) return ['nombre' => 'Alta satisfacción', 'color' => '#1e8449'];
    if ($s >= 7) return ['nombre' => 'Satisfactorio',     'color' => '#1a7abf'];
    if ($s >= 5) return ['nombre' => 'Regular',           'color' => '#d68910'];
    return             ['nombre' => 'A mejorar',          'color' => '#c0392b'];
}

// Token único de 64 chars (SHA-256)
function eu_generar_token($id_asignacion) {
    return hash('sha256', $id_asignacion . microtime(true) . random_bytes(16));
}

// Evalúa la condición de una pregunta contra una respuesta ya dada
function eu_evaluar_condicion($valor_respuesta, $operador, $cond_valor) {
    $v = (float)$valor_respuesta;
    $c = (float)$cond_valor;
    switch ($operador) {
        case '<':  return $v <  $c;
        case '<=': return $v <= $c;
        case '=':  return $v == $c;
        case '>=': return $v >= $c;
        case '>':  return $v >  $c;
        case '!=': return $v != $c;
        default:   return true;
    }
}

// FROM + JOINs comunes (vista de usados entregados + estado de encuesta derivado)
function eu_from() {
    return "FROM view_asignaciones_usados_entregadas v
            LEFT JOIN encu_tokens     t ON t.id_asignacion = v.id_unidad
            LEFT JOIN encu_respuestas r ON r.id_token      = t.id_token";
}

// WHERE base (sucursal + búsqueda + fecha/borrado), SIN el filtro de estado.
function eu_where_base($con) {
    $suc = isset($_GET['suc']) ? (int)$_GET['suc'] : 0;
    $q   = isset($_GET['q'])   ? trim($_GET['q'])  : '';

    $W = "v.borrar = 0 AND v.guardado = 1 AND v.fec_entrega >= '" . EU_FECHA_DESDE . "'";
    if ($suc > 0) $W .= " AND v.id_sucursal = $suc";
    if ($q !== '') {
        $qe = mysqli_real_escape_string($con, $q);
        $W .= " AND (v.cliente LIKE '%$qe%' OR v.vehiculo LIKE '%$qe%'
                     OR v.dominio LIKE '%$qe%' OR v.asesor_venta LIKE '%$qe%'
                     OR v.nro_unidad LIKE '%$qe%' OR v.interno LIKE '%$qe%')";
    }
    return $W;
}

// Añade el filtro de estado de encuesta al WHERE base.
function eu_where_estado($Wbase) {
    $est = isset($_GET['est']) ? trim($_GET['est']) : '';
    if ($est === '0') return $Wbase . " AND t.id_token IS NULL";
    if ($est === '1') return $Wbase . " AND t.id_token IS NOT NULL AND COALESCE(t.completada,0) = 0";
    if ($est === '2') return $Wbase . " AND COALESCE(t.completada,0) = 1";
    return $Wbase;
}

// ORDER BY validado.
function eu_order() {
    $map = [
        'fec_entrega' => 'v.fec_entrega',
        'cliente'     => 'v.cliente',
        'vehiculo'    => 'v.vehiculo',
        'dominio'     => 'v.dominio',
        'asesor'      => 'v.asesor_venta',
        'estado'      => 'estado',
    ];
    $sort = isset($_GET['sort']) ? $_GET['sort'] : '';
    $dir  = (isset($_GET['dir']) && strtolower($_GET['dir']) === 'asc') ? 'ASC' : 'DESC';
    if (isset($map[$sort])) return $map[$sort] . " $dir";
    return "v.fec_entrega DESC";
}
