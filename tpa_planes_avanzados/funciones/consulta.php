<?php
/*
 * Lógica de datos del módulo TPA Planes Avanzados.
 * La usan las actions (listar / catálogos / exportaciones) para que el FILTRO y
 * las consultas estén definidos en un solo lugar. Requiere $con (config_app.php).
 *
 * Prefijo tpa_ = "TPA Planes Avanzados".
 */

// ─── Filtros desde $_GET (situación + modelo + estado) ──────────────────────
function tpa_filtros() {
    $sit    = isset($_GET['situacionId']) ? (int)$_GET['situacionId'] : 1;
    $modelo = isset($_GET['modelo'])      ? (int)$_GET['modelo']      : 0;   // 0 = primer modelo activo
    $estado = (isset($_GET['estado']) && $_GET['estado'] !== '') ? (int)$_GET['estado'] : 0; // 0 = todos
    if ($sit < 1) $sit = 1;
    return [$sit, $modelo, $estado];
}

// ─── Helpers de saneo / formato ─────────────────────────────────────────────
// Parsea un monto en formato AR ("$ 1.234.567,89") a float. (= convertirNumero viejo)
function tpa_num($monto) {
    $monto = trim((string)$monto);
    $monto = str_replace(['$', ' ', '.'], '', $monto);
    $monto = str_replace(',', '.', $monto);
    return (float)preg_replace('/[^0-9.\-]/', '', $monto);
}
// String escapado para SQL.
function tpa_txt($con, $v) {
    return mysqli_real_escape_string($con, (string)$v);
}
// Fecha: vacía / "null" / 0 => NULL real (evita error 1292 con sql_mode estricto en dev).
function tpa_fechaSQL($con, $v) {
    $v = trim((string)$v);
    if ($v === '' || strtolower($v) === 'null' || $v === '0' || $v === '0000-00-00') return 'NULL';
    return "'" . mysqli_real_escape_string($con, $v) . "'";
}

// ─── Modelos que tienen planes (botonera) ───────────────────────────────────
function tpa_modelos_activos($con) {
    $sql = "SELECT m.id AS modelo_id, m.modelo
            FROM tpa_planes_avanzados pa
            INNER JOIN tpa_planes_versiones v ON pa.version_id = v.id
            INNER JOIN tpa_planes_modelos   m ON v.modelo_id   = m.id
            WHERE pa.situacion_id <> 4
            GROUP BY m.id
            ORDER BY m.posicion ASC";
    $res = mysqli_query($con, $sql);
    $out = [];
    while ($r = mysqli_fetch_assoc($res)) {
        $out[] = ['id' => (int)$r['modelo_id'], 'modelo' => $r['modelo']];
    }
    return $out;
}

// ─── Planes de una situación + modelo (+ estado opcional) ───────────────────
// Devuelve filas con todos los campos que usan tabla / cards / modales.
function tpa_query_planes($con, $sit, $modelo, $estado = 0) {
    $cond = "";
    if ($modelo > 0) $cond .= " AND m.id = " . (int)$modelo;
    if ($estado > 0) $cond .= " AND pa.estado_id = " . (int)$estado;

    $sql = "SELECT
                pa.id, pa.uuid, pa.situacion_id, pa.version_id, pa.modalidad_id,
                pa.grupo_orden, pa.cuotas_pagadas_cantidad, pa.cuotas_pagadas_monto,
                pa.costo, pa.cesion, pa.plus, pa.venta, pa.cuota_promedio, pa.valor_unidad,
                pa.integracion, pa.derecho_adjudicacion, pa.precio_final,
                pa.estado_id, pa.observaciones, pa.usuario_venta_id, pa.monto_reserva,
                pa.modelo_version_retirar, pa.fecha_reserva, pa.hora_reserva,
                pa.cliente, pa.fecha_nacimiento, pa.sexo, pa.edad, pa.dni, pa.cuil,
                pa.direccion, pa.localidad, pa.provincia, pa.email, pa.celular,
                s.situacion, mo.modalidad, u.nombre AS usuario_venta,
                v.version, m.modelo, m.id AS modelo_id
            FROM tpa_planes_avanzados pa
            INNER JOIN tpa_plan_situaciones s ON pa.situacion_id = s.id
            INNER JOIN tpa_modalidades     mo ON pa.modalidad_id = mo.id
            LEFT  JOIN usuarios            u  ON pa.usuario_venta_id = u.idusuario
            INNER JOIN tpa_planes_versiones v ON pa.version_id = v.id
            INNER JOIN tpa_planes_modelos   m ON v.modelo_id   = m.id
            WHERE pa.situacion_id = " . (int)$sit . $cond . "
            ORDER BY m.posicion ASC, v.posicion ASC";
    return mysqli_query($con, $sql);
}

// Mapea una fila cruda a la estructura que consume el front (tipos numéricos limpios).
function tpa_map_row($r) {
    return [
        'id'                      => (int)$r['id'],
        'uuid'                    => $r['uuid'],
        'situacion_id'            => (int)$r['situacion_id'],
        'version_id'              => (int)$r['version_id'],
        'modalidad_id'            => (int)$r['modalidad_id'],
        'modelo_id'               => (int)$r['modelo_id'],
        'grupo_orden'             => $r['grupo_orden'],
        'modelo'                  => $r['modelo'],
        'version'                 => $r['version'],
        'modalidad'               => $r['modalidad'],
        'cuotas_pagadas_cantidad' => (int)$r['cuotas_pagadas_cantidad'],
        'cuotas_pagadas_monto'    => (float)$r['cuotas_pagadas_monto'],
        'costo'                   => (float)$r['costo'],
        'cesion'                  => (float)$r['cesion'],
        'plus'                    => (float)$r['plus'],
        'venta'                   => (float)$r['venta'],
        'cuota_promedio'          => (float)$r['cuota_promedio'],
        'valor_unidad'            => (float)$r['valor_unidad'],
        'integracion'             => (float)$r['integracion'],
        'derecho_adjudicacion'    => (float)$r['derecho_adjudicacion'],
        'precio_final'            => (float)$r['precio_final'],
        'monto_reserva'           => $r['monto_reserva'] === null ? null : (float)$r['monto_reserva'],
        'estado_id'               => (int)$r['estado_id'],
        'observaciones'           => $r['observaciones'],
        'cliente'                 => $r['cliente'],
        'usuario_venta'           => $r['usuario_venta'],
        'usuario_venta_id'        => $r['usuario_venta_id'] === null ? null : (int)$r['usuario_venta_id'],
        // datos de cliente (para el modal de reserva / edición)
        'sexo'                    => $r['sexo'],
        'fecha_nacimiento'        => ($r['fecha_nacimiento'] && $r['fecha_nacimiento'] !== '0000-00-00') ? $r['fecha_nacimiento'] : '',
        'edad'                    => $r['edad'],
        'dni'                     => $r['dni'],
        'cuil'                    => $r['cuil'],
        'direccion'               => $r['direccion'],
        'localidad'               => $r['localidad'],
        'provincia'               => $r['provincia'],
        'email'                   => $r['email'],
        'celular'                 => $r['celular'],
        'fecha_reserva'           => ($r['fecha_reserva'] && $r['fecha_reserva'] !== '0000-00-00') ? $r['fecha_reserva'] : '',
        'hora_reserva'            => $r['hora_reserva'],
        'modelo_version_retirar'  => $r['modelo_version_retirar'],
    ];
}

// ─── Catálogos para los modales (selects) ───────────────────────────────────
function tpa_catalogo($con, $sql, $map) {
    $res = mysqli_query($con, $sql);
    $out = [];
    while ($r = mysqli_fetch_assoc($res)) $out[] = $map($r);
    return $out;
}

function tpa_versiones($con) {
    return tpa_catalogo($con,
        "SELECT v.id, v.version, m.modelo, m.posicion AS mpos, v.posicion AS vpos
         FROM tpa_planes_versiones v
         INNER JOIN tpa_planes_modelos m ON v.modelo_id = m.id
         WHERE v.activo = 1
         ORDER BY m.posicion ASC, v.posicion ASC",
        function ($r) { return ['id' => (int)$r['id'], 'label' => trim($r['modelo'] . ' ' . $r['version'])]; });
}
function tpa_modalidades($con) {
    return tpa_catalogo($con,
        "SELECT id, modalidad FROM tpa_modalidades WHERE activo = 1 ORDER BY id ASC",
        function ($r) { return ['id' => (int)$r['id'], 'label' => $r['modalidad']]; });
}
function tpa_estados($con) {
    return tpa_catalogo($con,
        "SELECT id, estado, color FROM tpa_planes_avanzados_estados WHERE activo = 1 ORDER BY id ASC",
        function ($r) { return ['id' => (int)$r['id'], 'label' => $r['estado'], 'color' => $r['color']]; });
}
function tpa_situaciones($con) {
    return tpa_catalogo($con,
        "SELECT id, situacion FROM tpa_plan_situaciones WHERE activo = 1 AND id <> 4 ORDER BY orden DESC",
        function ($r) { return ['id' => (int)$r['id'], 'label' => $r['situacion']]; });
}
function tpa_asesores($con) {
    return tpa_catalogo($con,
        "SELECT idusuario, nombre FROM usuarios WHERE idperfil = 3 AND activo = 1 ORDER BY nombre ASC",
        function ($r) { return ['id' => (int)$r['idusuario'], 'label' => $r['nombre']]; });
}

// Texto del estado / situación (para títulos de exportación).
function tpa_situacion_nombre($sit) {
    return [1 => 'Avanzados', 2 => 'Adjudicados'][$sit] ?? 'Planes';
}
function tpa_estado_nombre($estado) {
    return [1 => 'Libres', 2 => 'Reservados', 3 => 'Vendidos'][$estado] ?? 'Todos';
}
