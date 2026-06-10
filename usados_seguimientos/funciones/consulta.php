<?php
/*
 * Lógica de datos del módulo Seguimiento Documentación Usados (moderno).
 * Queries + estados + cálculo de estado general + helpers de presentación.
 * La usan los actions/ (listar, celda, historial, etc.). Requiere: $con.
 *
 * Estados de una celda: 0=Pendiente | 1=Hecho | 2=No corresponde | 3=En proceso
 */

// Orden visual (Pendiente, No corresponde, En proceso, Hecho) — igual al módulo viejo.
$US_ESTADOS = [
    0 => ['label' => 'Pendiente',      'icon' => '○', 'class' => 'est-pendiente'],
    2 => ['label' => 'No corresponde', 'icon' => '−', 'class' => 'est-no-corresponde'],
    3 => ['label' => 'En proceso',     'icon' => '◑', 'class' => 'est-en-proceso'],
    1 => ['label' => 'Hecho',          'icon' => '✓', 'class' => 'est-hecho'],
];

// Lista para el front (orden de los radios del modal).
function us_estados_lista() {
    global $US_ESTADOS;
    $out = [];
    foreach ($US_ESTADOS as $val => $e) {
        $out[] = ['estado' => $val, 'label' => $e['label'], 'icon' => $e['icon'], 'class' => $e['class']];
    }
    return $out;
}

// Estado general de un usado a partir de los estados de sus celdas (ints).
// No corresponde (2) no afecta. 0=Pendiente | 1=Completo | 3=En proceso.
function us_estado_general(array $estados): array {
    $hay_pendiente = false;
    $hay_proceso   = false;
    foreach ($estados as $e) {
        if ((int)$e === 0) $hay_pendiente = true;
        if ((int)$e === 3) $hay_proceso   = true;
    }
    if ($hay_pendiente) return ['estado' => 0, 'label' => 'Pendiente',  'icon' => '○', 'class' => 'est-pendiente'];
    if ($hay_proceso)   return ['estado' => 3, 'label' => 'En proceso', 'icon' => '◑', 'class' => 'est-en-proceso'];
    return ['estado' => 1, 'label' => 'Completo', 'icon' => '✓', 'class' => 'est-hecho'];
}

// Ítems activos (columnas).
function us_items($con): array {
    $items = [];
    $r = mysqli_query($con, "SELECT * FROM usados_docs_items WHERE activo = 1 ORDER BY posicion, id_item");
    while ($row = mysqli_fetch_assoc($r)) {
        $items[] = ['id_item' => (int)$row['id_item'], 'nombre' => $row['nombre'], 'descripcion' => $row['descripcion']];
    }
    return $items;
}

// Sucursales y estados de usado para el toolbar.
function us_sucursales($con): array {
    $out = [['id' => 0, 'nombre' => 'Todas']];
    $r = mysqli_query($con, "SELECT idsucursal, sucres FROM sucursales WHERE activo = 1 ORDER BY posicion");
    while ($row = mysqli_fetch_assoc($r)) $out[] = ['id' => (int)$row['idsucursal'], 'nombre' => $row['sucres']];
    return $out;
}
function us_estados_usado($con): array {
    $out = [['id' => 0, 'nombre' => 'Todos']];
    $r = mysqli_query($con, "SELECT id_estado_usado, estado_usado FROM asignaciones_usados_estados ORDER BY posicion");
    while ($row = mysqli_fetch_assoc($r)) $out[] = ['id' => (int)$row['id_estado_usado'], 'nombre' => $row['estado_usado']];
    return $out;
}

// ── Helpers de presentación (devuelven datos listos para el front) ──────────
function us_fecha($f): string {
    return $f ? date('d/m/y', strtotime($f)) : '';
}

// Badge de reserva → ['label','class'].
function us_badge_reserva($reservada, $estado_reserva, $fec_reserva, $fecha_cancelacion): array {
    if (!$reservada)            return ['label' => 'Libre',     'class' => 'res-libre'];
    if ($estado_reserva == 0)   return ['label' => 'Res. NC',   'class' => 'res-nc'];
    if ($fecha_cancelacion)     return ['label' => 'Cancelada', 'class' => 'res-cancelada'];
    $dias  = $fec_reserva ? abs(floor((strtotime($fec_reserva) - time()) / 86400)) : 0;
    return ['label' => 'Reservada', 'class' => $dias >= 10 ? 'res-vencida' : 'res-ok'];
}

// Badge UCT → ['label','class'] | null.
function us_badge_uct($id_cert): ?array {
    if ($id_cert == 2) return ['label' => 'UCT-ORO',   'class' => 'uct-oro'];
    if ($id_cert == 4) return ['label' => 'UCT-PLATA', 'class' => 'uct-plata'];
    return null;
}

// Resaltado de fila → 'gris' | 'lila' | ''.
function us_row_hl($ant, $reservada, $estado_reserva, $fec_reserva, $fecha_cancelacion): string {
    if ($reservada && $estado_reserva == 1 && !$fecha_cancelacion && $fec_reserva) {
        $dias = abs(floor((strtotime($fec_reserva) - time()) / 86400));
        if ($dias >= 10) return 'lila';
    }
    if ($ant >= 50) return 'gris';
    return '';
}

// ── Listado principal: filas (usados) × columnas (ítems) ────────────────────
// Lee filtros de $_GET: sucursal, estado_usado, estado (doc. general).
// Devuelve ['items'=>[], 'usados'=>[], 'total'=>int].
function us_listar($con): array {
    global $US_ESTADOS;

    $filtro_sucursal     = isset($_GET['sucursal'])     ? (int)$_GET['sucursal']     : 0;
    $filtro_estado_usado = isset($_GET['estado_usado']) ? (int)$_GET['estado_usado'] : 0;
    $filtro_estado       = (isset($_GET['estado']) && $_GET['estado'] !== '') ? (int)$_GET['estado'] : -1;

    $where_extra = '';
    if ($filtro_sucursal > 0)     $where_extra .= " AND asignaciones_usados.id_sucursal = $filtro_sucursal";
    if ($filtro_estado_usado > 0) $where_extra .= " AND asignaciones_usados.id_estado = $filtro_estado_usado";

    $items = us_items($con);

    $SQL = "SELECT
        asignaciones_usados.id_unidad,
        asignaciones_usados.interno,
        asignaciones_usados.vehiculo,
        asignaciones_usados.dominio,
        asignaciones_usados.km,
        asignaciones_usados.fec_recepcion,
        DATEDIFF(DATE(NOW()), asignaciones_usados.fec_recepcion) AS ant,
        asignaciones_usados.reservada,
        asignaciones_usados.estado_reserva,
        asignaciones_usados.fec_reserva,
        asignaciones_usados.fecha_cancelacion,
        asignaciones_usados.id_estado_certificado,
        asignaciones_usados.id_sucursal,
        usuario_1.nombre AS nombre_asesor_toma
    FROM
        usuarios usuario_1
        JOIN asignaciones_usados ON asignaciones_usados.asesortoma = usuario_1.idusuario
        JOIN usuarios            ON asignaciones_usados.id_asesor  = usuarios.idusuario
    WHERE
        asignaciones_usados.entregado = 0
        AND asignaciones_usados.interno   <> 0
        AND asignaciones_usados.interno   <> ''
        AND asignaciones_usados.borrar    = 0
        AND asignaciones_usados.guardado  = 1
        AND asignaciones_usados.fec_entrega IS NULL
        AND asignaciones_usados.id_estado != 0
        $where_extra
    ORDER BY asignaciones_usados.interno";

    $res = mysqli_query($con, $SQL);
    if (!$res) return ['error' => mysqli_error($con)];

    $usados = [];
    $ids    = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $usados[(int)$row['id_unidad']] = $row;
        $ids[] = (int)$row['id_unidad'];
    }

    // Seguimiento (estado/observación + archivo legacy) por celda.
    $seg = [];
    // Celdas con adjuntos en la tabla nueva.
    $con_adj = [];
    if (!empty($ids)) {
        $in = implode(',', $ids);

        $rs = mysqli_query($con, "SELECT id_unidad, id_item, estado, observacion, archivo
            FROM usados_docs_seguimiento WHERE id_unidad IN ($in)");
        while ($r = mysqli_fetch_assoc($rs)) {
            $seg[(int)$r['id_unidad']][(int)$r['id_item']] = $r;
        }

        $ra = mysqli_query($con, "SELECT DISTINCT id_unidad, id_item
            FROM usados_docs_archivos WHERE id_unidad IN ($in)");
        while ($r = mysqli_fetch_assoc($ra)) {
            $con_adj[(int)$r['id_unidad']][(int)$r['id_item']] = true;
        }
    }

    // Sucursales (para mostrar el nombre).
    $suc_nombre = [];
    foreach (us_sucursales($con) as $s) $suc_nombre[$s['id']] = $s['nombre'];

    $out = [];
    foreach ($usados as $idu => $u) {
        $celdas  = [];
        $estados = [];
        foreach ($items as $it) {
            $idi    = $it['id_item'];
            $s      = $seg[$idu][$idi] ?? null;
            $estado = $s ? (int)$s['estado'] : 0;
            $estados[] = $estado;
            $tiene_arch = ($s && !empty($s['archivo'])) || !empty($con_adj[$idu][$idi]);
            $celdas[$idi] = [
                'estado'      => $estado,
                'icon'        => $US_ESTADOS[$estado]['icon'],
                'class'       => $US_ESTADOS[$estado]['class'],
                'observacion' => $s['observacion'] ?? '',
                'tiene_arch'  => $tiene_arch,
            ];
        }

        $eg = us_estado_general($estados);

        // Filtro de estado general (post-cálculo).
        if ($filtro_estado >= 0 && $eg['estado'] !== $filtro_estado) continue;

        $ant = (int)$u['ant'];
        $out[] = [
            'id_unidad'   => $idu,
            'interno'     => $u['interno'],
            'vehiculo'    => $u['vehiculo'],
            'dominio'     => $u['dominio'] ?: '—',
            'asesor_toma' => $u['nombre_asesor_toma'],
            'recepcion'   => us_fecha($u['fec_recepcion']),
            'ant'         => $ant,
            'sucursal'    => $suc_nombre[(int)$u['id_sucursal']] ?? '—',
            'reserva'     => us_badge_reserva($u['reservada'], $u['estado_reserva'], $u['fec_reserva'], $u['fecha_cancelacion']),
            'uct'         => us_badge_uct($u['id_estado_certificado']),
            'row_hl'      => us_row_hl($ant, $u['reservada'], $u['estado_reserva'], $u['fec_reserva'], $u['fecha_cancelacion']),
            'estado_gral' => $eg,
            'celdas'      => $celdas,
        ];
    }

    return ['items' => $items, 'usados' => $out, 'total' => count($out)];
}
