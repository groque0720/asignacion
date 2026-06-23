<?php
/*
 * Lógica de datos del módulo Encuesta · 0km (la usan las actions).
 * Requiere $con (config_app.php). Prefijo enc_ = "encuesta 0km".
 *
 * Fuente: tabla `asignaciones` (entregadas) + tablas `enc_*`.
 * A DIFERENCIA de usados, el estado de la encuesta se CONSERVA en
 * `asignaciones.con_encuesta`:  0 = sin generar | 1 = pendiente | 2 = completada.
 * El token lo pone en 1; el guardado de respuestas en 2.
 */

// Normaliza a UTF-8 válido (datos legacy en latin1/cp1252 romperían json_encode).
function enc_utf8($s) {
    if ($s === null || $s === '') return $s;
    if (function_exists('mb_check_encoding') && !mb_check_encoding($s, 'UTF-8')) {
        return mb_convert_encoding($s, 'UTF-8', 'Windows-1252');
    }
    return $s;
}

// Nivel (nombre + color) para un score, desde enc_niveles. Fallback hardcodeado.
function enc_nivel($score) {
    global $con;
    $s = (float)$score;
    $res = mysqli_query($con,
        "SELECT nombre, color FROM enc_niveles
         WHERE $s >= valor_desde AND $s <= valor_hasta
         ORDER BY valor_desde DESC LIMIT 1");
    if ($res && mysqli_num_rows($res) > 0) return mysqli_fetch_assoc($res);
    if ($s >= 9) return ['nombre' => 'Alta satisfacción', 'color' => '#1e8449'];
    if ($s >= 7) return ['nombre' => 'Satisfactorio',     'color' => '#1a7abf'];
    if ($s >= 5) return ['nombre' => 'Regular',           'color' => '#d68910'];
    return             ['nombre' => 'A mejorar',          'color' => '#c0392b'];
}

// Token único de 64 chars (SHA-256)
function enc_generar_token($id_asignacion) {
    return hash('sha256', $id_asignacion . microtime(true) . random_bytes(16));
}

// Evalúa la condición de una pregunta contra una respuesta ya dada
function enc_evaluar_condicion($valor_respuesta, $operador, $cond_valor) {
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

/*
 * Setea las variables de sesión MySQL que lee el trigger trg_asignaciones_audit_update
 * (tabla auditoria_unidades). El comun/func_mysql.php moderno NO las setea, así que hay
 * que hacerlo a mano antes de cada UPDATE a `asignaciones` (acá: con_encuesta).
 * Sin sesión PHP (lado público) cae a 0 / 'sistema', igual que el módulo viejo.
 */
function enc_set_audit($con) {
    $uid    = isset($_SESSION['id'])      ? (int)$_SESSION['id']    : 0;
    $uname  = isset($_SESSION['usuario']) ? $_SESSION['usuario']    : 'sistema';
    $origen = isset($_SERVER['SCRIPT_NAME']) ? basename($_SERVER['SCRIPT_NAME']) : 'cli';
    $uname  = mysqli_real_escape_string($con, $uname);
    $origen = mysqli_real_escape_string($con, $origen);
    mysqli_query($con, "SET @id_usuario = $uid, @usuario_nombre = '$uname', @origen = '$origen'");
}

// FROM + JOINs comunes (entregas de asignaciones + token + respuesta).
function enc_from() {
    return "FROM asignaciones a
            JOIN  usuarios   u ON a.id_asesor    = u.idusuario
            LEFT JOIN grupos g ON a.id_grupo     = g.idgrupo
            LEFT JOIN modelos m ON a.id_modelo   = m.idmodelo
            LEFT JOIN sucursales s ON a.id_sucursal = s.idsucursal
            LEFT JOIN enc_tokens    t  ON t.id_asignacion = a.id_unidad
            LEFT JOIN enc_respuestas er ON er.id_asignacion = a.id_unidad";
}

// WHERE base (entregadas + sucursal + búsqueda), SIN el filtro de estado.
function enc_where_base($con) {
    $suc = isset($_GET['suc']) ? (int)$_GET['suc'] : 0;
    $q   = isset($_GET['q'])   ? trim($_GET['q'])  : '';

    $W = "a.entregada = 1 AND a.borrar = 0 AND a.guardado = 1
          AND a.fec_entrega >= '" . ENCUESTA_FECHA_DESDE . "'";
    if ($suc > 0) $W .= " AND a.id_sucursal = $suc";
    if ($q !== '') {
        $qe = mysqli_real_escape_string($con, $q);
        $W .= " AND (a.cliente LIKE '%$qe%' OR a.chasis LIKE '%$qe%'
                     OR a.nro_orden LIKE '%$qe%' OR a.nro_unidad LIKE '%$qe%'
                     OR g.grupo LIKE '%$qe%' OR m.modelo LIKE '%$qe%'
                     OR u.nombre LIKE '%$qe%')";
    }
    return $W;
}

// Añade el filtro de estado de encuesta (con_encuesta) al WHERE base.
function enc_where_estado($Wbase) {
    $est = isset($_GET['est']) ? trim($_GET['est']) : '';
    if ($est === '0') return $Wbase . " AND a.con_encuesta = 0";
    if ($est === '1') return $Wbase . " AND a.con_encuesta = 1";
    if ($est === '2') return $Wbase . " AND a.con_encuesta = 2";
    return $Wbase;
}

// ORDER BY validado.
function enc_order() {
    $map = [
        'fec_entrega' => 'a.fec_entrega',
        'cliente'     => 'a.cliente',
        'grupo'       => 'g.grupo',
        'modelo'      => 'm.modelo',
        'asesor'      => 'u.nombre',
        'sucursal'    => 's.sucursal',
        'estado'      => 'a.con_encuesta',
    ];
    $sort = isset($_GET['sort']) ? $_GET['sort'] : '';
    $dir  = (isset($_GET['dir']) && strtolower($_GET['dir']) === 'asc') ? 'ASC' : 'DESC';
    if (isset($map[$sort])) return $map[$sort] . " $dir";
    return "a.fec_entrega DESC";
}
