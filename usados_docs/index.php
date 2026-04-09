<?php
require_once '_init.php';

// ── Ítems activos ───────────────────────────────────────────────────────────
$items = [];
$r = mysqli_query($con, "SELECT * FROM usados_docs_items WHERE activo = 1 ORDER BY posicion, id_item");
while ($row = mysqli_fetch_assoc($r)) $items[] = $row;

// ── Sucursales para filtro ──────────────────────────────────────────────────
$sucursales_a = [];
$r = mysqli_query($con, "SELECT idsucursal, sucres FROM sucursales WHERE activo = 1 ORDER BY posicion");
while ($row = mysqli_fetch_assoc($r)) {
    $sucursales_a[(int)$row['idsucursal']] = $row['sucres'];
}

// ── Estados del usado para filtro ───────────────────────────────────────────
$estados_usado_a = [];
$r = mysqli_query($con, "SELECT * FROM asignaciones_usados_estados ORDER BY posicion");
while ($row = mysqli_fetch_assoc($r)) {
    $estados_usado_a[(int)$row['id_estado_usado']] = $row['estado_usado'];
}

// ── Filtros ─────────────────────────────────────────────────────────────────
$filtro_sucursal     = isset($_GET['sucursal'])     ? (int)$_GET['sucursal']     : 0;
$filtro_estado_usado = isset($_GET['estado_usado']) ? (int)$_GET['estado_usado'] : 0;
$filtro_estado       = (isset($_GET['estado']) && $_GET['estado'] !== '') ? (int)$_GET['estado'] : -1;

$where_extra = '';
if ($filtro_sucursal > 0)     $where_extra .= " AND asignaciones_usados.id_sucursal = $filtro_sucursal";
if ($filtro_estado_usado > 0) $where_extra .= " AND asignaciones_usados.id_estado = $filtro_estado_usado";

$hay_filtro = $filtro_sucursal > 0 || $filtro_estado_usado > 0 || $filtro_estado >= 0;

// ── Query principal ─────────────────────────────────────────────────────────
$SQL = "SELECT
    asignaciones_usados.id_unidad,
    asignaciones_usados.interno,
    asignaciones_usados.vehiculo,
    asignaciones_usados.dominio,
    asignaciones_usados.año,
    asignaciones_usados.km,
    asignaciones_usados.fec_recepcion,
    DATEDIFF(DATE(NOW()), asignaciones_usados.fec_recepcion) AS ant,
    asignaciones_usados.id_estado,
    asignaciones_usados.reservada,
    asignaciones_usados.estado_reserva,
    asignaciones_usados.fec_reserva,
    asignaciones_usados.fecha_cancelacion,
    asignaciones_usados.id_estado_certificado,
    asignaciones_usados.id_sucursal,
    usuario_1.nombre  AS nombre_asesor_toma,
    usuarios.nombre   AS nombre_asesor_venta
FROM
    usuarios usuario_1
    JOIN asignaciones_usados ON asignaciones_usados.asesortoma = usuario_1.idusuario
    JOIN usuarios             ON asignaciones_usados.id_asesor  = usuarios.idusuario
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

$result = mysqli_query($con, $SQL);
$usados     = [];
$ids_unidad = [];
while ($row = mysqli_fetch_assoc($result)) {
    $usados[]     = $row;
    $ids_unidad[] = (int)$row['id_unidad'];
}

// ── Cargar seguimiento ──────────────────────────────────────────────────────
$seguimiento = [];
if (!empty($ids_unidad)) {
    $ids_str = implode(',', $ids_unidad);
    $r = mysqli_query($con, "SELECT s.*, u.nombre AS nombre_usuario
        FROM usados_docs_seguimiento s
        LEFT JOIN usuarios u ON s.id_usuario = u.idusuario
        WHERE s.id_unidad IN ($ids_str)");
    while ($row = mysqli_fetch_assoc($r)) {
        $seguimiento[(int)$row['id_unidad']][(int)$row['id_item']] = $row;
    }
}

// ── Filtro de estado general (post-query) ───────────────────────────────────
if ($filtro_estado >= 0) {
    $usados = array_values(array_filter($usados, function ($u) use ($filtro_estado, $items, $seguimiento) {
        return calcular_estado_general((int)$u['id_unidad'], $items, $seguimiento) === $filtro_estado;
    }));
}

$total = count($usados);

// ── Helpers de presentación ──────────────────────────────────────────────────
function fmt_fecha($f) {
    return $f ? date('d/m/y', strtotime($f)) : '—';
}

function badge_reserva($reservada, $estado_reserva, $fec_reserva, $fecha_cancelacion) {
    if (!$reservada) return '<span class="badge-res res-libre">Libre</span>';
    if ($estado_reserva == 0) return '<span class="badge-res res-nc">Res. NC</span>';
    if ($fecha_cancelacion) return '<span class="badge-res res-cancelada">Cancelada</span>';

    $dias = $fec_reserva ? abs(floor((strtotime($fec_reserva) - time()) / 86400)) : 0;
    $clase = $dias >= 10 ? 'res-vencida' : 'res-ok';
    return '<span class="badge-res ' . $clase . '">Reservada</span>';
}

function badge_uct($id_cert) {
    if ($id_cert == 2) return ' <span class="badge-uct uct-oro">UCT-ORO</span>';
    if ($id_cert == 4) return ' <span class="badge-uct uct-plata">UCT-PLATA</span>';
    return '';
}

function row_style($ant, $reservada, $estado_reserva, $fec_reserva, $fecha_cancelacion) {
    // Reservada sin cancelación hace +10 días → lila suave
    if ($reservada && $estado_reserva == 1 && !$fecha_cancelacion && $fec_reserva) {
        $dias = abs(floor((strtotime($fec_reserva) - time()) / 86400));
        if ($dias >= 10) return 'background:#ede8f5;';
    }
    // Antigüedad >= 50 días → gris suave
    if ($ant >= 50) return 'background:#eae8e4;';
    return '';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Seguimiento Documentación — Usados</title>
    <link rel="shortcut icon" type="image/x-icon" href="../imagenes/favicon.ico">
    <link rel="stylesheet" href="../asignacion/css/roquesystem.css">
    <link rel="stylesheet" href="../asignacion/css/estilo_app.css">
    <link rel="stylesheet" href="css/usados_docs.css">
    <script src="../asignacion/js/jquery-2.1.3.min.js"></script>
</head>
<body>

<!-- ── HEADER ──────────────────────────────────────────────────────────────── -->
<div class="zona-cabecera ancho-100">
    <div class="cabecera">
        <div class="cabecera-izquierda">
            <div class="zona-logo-ppal">
                <img class="logo-ppal" src="../asignacion/imagenes/logodyv_c.png" alt="">
            </div>
            <div style="line-height:30px; font-weight:700; font-size:14px; margin-left:12px; color:#333;">
                Seguimiento Documentación — Usados
            </div>
        </div>
        <div class="cabecera-derecha">
            <div class="zona-usuario">
                <div class="nombre-usuario">
                    <span><?= htmlspecialchars($nombre_usuario) ?></span>
                </div>
            </div>
            <?php if ($es_admin): ?>
            <div style="line-height:30px; margin-right:16px;">
                <button class="btn-admin" id="btn-abrir-admin">&#9881; Gestionar Ítems</button>
            </div>
            <?php endif; ?>
            <div class="zona-img-toyota">
                <img class="img-toyota" src="../asignacion/imagenes/logo_toyota.png" alt="">
            </div>
        </div>
    </div>
</div>

<!-- ── BARRA DE FILTROS ─────────────────────────────────────────────────────── -->
<div class="ud-filters">
    <form method="GET" action="" id="form-filtros">
        <div class="filter-group">
            <label>Sucursal:</label>
            <select name="sucursal" onchange="this.form.submit()">
                <option value="0">Todas</option>
                <?php foreach ($sucursales_a as $id => $nombre): ?>
                    <option value="<?= $id ?>" <?= $filtro_sucursal == $id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($nombre) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="filter-group">
            <label>Estado del usado:</label>
            <select name="estado_usado" onchange="this.form.submit()">
                <option value="0">Todos</option>
                <?php foreach ($estados_usado_a as $id => $nombre): ?>
                    <option value="<?= $id ?>" <?= $filtro_estado_usado == $id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($nombre) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="filter-group">
            <label>Estado doc.:</label>
            <select name="estado" onchange="this.form.submit()">
                <option value=""  <?= $filtro_estado <  0 ? 'selected' : '' ?>>Todos</option>
                <option value="0" <?= $filtro_estado === 0 ? 'selected' : '' ?>>&#9675; Pendiente</option>
                <option value="3" <?= $filtro_estado === 3 ? 'selected' : '' ?>>&#9681; En proceso</option>
                <option value="1" <?= $filtro_estado === 1 ? 'selected' : '' ?>>&#10003; Completo</option>
            </select>
        </div>
        <div class="filter-info">
            <span><?= $total ?> usado<?= $total !== 1 ? 's' : '' ?></span>
            <?php if ($hay_filtro): ?>
                <a href="index.php" class="btn-limpiar">&#10005; Limpiar filtros</a>
            <?php endif; ?>
        </div>
    </form>

</div>

<!-- ── TABLA ─────────────────────────────────────────────────────────────────── -->
<div class="ud-table-wrapper">
    <?php if (empty($items)): ?>
        <div class="ud-empty">
            No hay ítems configurados.
            <?php if ($es_admin): ?>
                <button class="btn-link-inline" id="btn-abrir-admin-2">Agregar ítem</button>
            <?php endif; ?>
        </div>
    <?php elseif (empty($usados)): ?>
        <div class="ud-empty">No hay usados que coincidan con los filtros aplicados.</div>
    <?php else: ?>

    <table class="ud-table" id="tabla-seguimiento">
        <thead>
            <tr>
                <!-- Sticky izquierda -->
                <th class="col-sticky col-interno">Interno</th>
                <th class="col-sticky col-vehiculo">Vehículo</th>
                <th class="col-sticky col-dominio">Dominio</th>
                <th class="col-sticky col-asesor">Asesor toma</th>
                <!-- Info contextual -->
                <th class="col-info">Recep. / Ant.</th>
                <th class="col-info">Reserva</th>
                <th class="col-info">Suc.</th>
                <!-- Ítems de documentación -->
                <?php foreach ($items as $item): ?>
                    <th class="col-item"
                        title="<?= htmlspecialchars($item['descripcion'] ?? $item['nombre']) ?>">
                        <?= htmlspecialchars($item['nombre']) ?>
                    </th>
                <?php endforeach; ?>
                <!-- Sticky derecha -->
                <th class="col-estado-gral col-sticky-right">Estado doc.</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usados as $usado):
                $id_unidad         = (int)$usado['id_unidad'];
                $ant               = (int)$usado['ant'];
                $reservada         = (int)$usado['reservada'];
                $estado_reserva    = (int)$usado['estado_reserva'];
                $fec_reserva       = $usado['fec_reserva'];
                $fecha_cancelacion = $usado['fecha_cancelacion'];

                $estado_gral = calcular_estado_general($id_unidad, $items, $seguimiento);
                $eg          = $ESTADOS[$estado_gral];

                $estilo_fila = row_style($ant, $reservada, $estado_reserva, $fec_reserva, $fecha_cancelacion);

                $ant_html = $ant >= 50
                    ? ' <span class="badge-ant">(' . $ant . 'd)</span>'
                    : '';
            ?>
            <tr class="ud-row" data-id-unidad="<?= $id_unidad ?>"
                style="<?= $estilo_fila ?>">

                <!-- Sticky: Interno -->
                <td class="col-sticky col-interno">
                    <?= htmlspecialchars($usado['interno']) ?>
                </td>

                <!-- Sticky: Vehículo + badge UCT -->
                <td class="col-sticky col-vehiculo">
                    <?= htmlspecialchars($usado['vehiculo']) ?>
                    <?= badge_uct($usado['id_estado_certificado']) ?>
                </td>

                <!-- Sticky: Dominio -->
                <td class="col-sticky col-dominio">
                    <?= htmlspecialchars($usado['dominio'] ?? '—') ?>
                </td>

                <!-- Sticky: Asesor toma -->
                <td class="col-sticky col-asesor">
                    <?= htmlspecialchars($usado['nombre_asesor_toma']) ?>
                </td>

                <!-- Info: Recepción + antigüedad -->
                <td class="col-info text-center">
                    <?= fmt_fecha($usado['fec_recepcion']) ?>
                    <?= $ant_html ?>
                </td>

                <!-- Info: Reserva -->
                <td class="col-info text-center">
                    <?= badge_reserva($reservada, $estado_reserva, $fec_reserva, $fecha_cancelacion) ?>
                </td>

                <!-- Info: Sucursal -->
                <td class="col-info text-center">
                    <?= htmlspecialchars($sucursales_a[$usado['id_sucursal']] ?? '—') ?>
                </td>

                <!-- Ítems de documentación -->
                <?php foreach ($items as $item):
                    $id_item = (int)$item['id_item'];
                    $seg     = $seguimiento[$id_unidad][$id_item] ?? null;
                    $estado  = $seg ? (int)$seg['estado'] : 0;
                    $edata   = $ESTADOS[$estado];
                    $titulo  = $edata['label'];
                    if ($seg && $seg['observacion']) $titulo .= ': ' . $seg['observacion'];
                ?>
                <td class="col-item celda-item <?= $edata['class'] ?>"
                    data-id-unidad="<?= $id_unidad ?>"
                    data-id-item="<?= $id_item ?>"
                    data-nombre-item="<?= htmlspecialchars($item['nombre']) ?>"
                    data-nombre-vehiculo="<?= htmlspecialchars($usado['vehiculo']) ?>"
                    data-interno="<?= htmlspecialchars($usado['interno']) ?>"
                    title="<?= htmlspecialchars($titulo) ?>">
                    <span class="celda-icon"><?= $edata['icon'] ?></span>
                    <?php if ($seg && $seg['archivo']): ?>
                        <span class="celda-clip" title="Tiene archivo adjunto">&#128206;</span>
                    <?php endif; ?>
                </td>
                <?php endforeach; ?>

                <!-- Sticky derecha: Estado general de documentación -->
                <td class="col-estado-gral col-sticky-right">
                    <span class="badge-estado <?= $eg['class'] ?>">
                        <?= $eg['icon'] ?> <?= $eg['label'] ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php endif; ?>
</div>

<!-- ── MODAL CELDA ───────────────────────────────────────────────────────────── -->
<div id="ud-modal-overlay" class="ud-modal-overlay" style="display:none">
    <div class="ud-modal">
        <div class="ud-modal-header">
            <div>
                <div class="ud-modal-titulo"   id="modal-titulo"></div>
                <div class="ud-modal-subtitulo" id="modal-subtitulo"></div>
            </div>
            <button class="ud-modal-close" id="btn-cerrar-modal">&#10005;</button>
        </div>
        <div class="ud-modal-body" id="ud-modal-body">
            <div class="ud-loading">Cargando...</div>
        </div>
    </div>
</div>

<!-- ── MODAL ADMIN ───────────────────────────────────────────────────────────── -->
<?php if ($es_admin): ?>
<div id="ud-admin-overlay" class="ud-modal-overlay" style="display:none">
    <div class="ud-modal ud-modal-admin">
        <div class="ud-modal-header">
            <div class="ud-modal-titulo">&#9881; Gestión de Ítems</div>
            <button class="ud-modal-close" id="btn-cerrar-admin">&#10005;</button>
        </div>
        <div class="ud-modal-body" id="ud-admin-body">
            <div class="ud-loading">Cargando...</div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- ── TOAST ─────────────────────────────────────────────────────────────────── -->
<div id="ud-toast" class="ud-toast"></div>

<script src="js/usados_docs.js"></script>
</body>
</html>
