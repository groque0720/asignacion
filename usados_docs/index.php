<?php
require_once '_init.php';

// ── Ítems activos ───────────────────────────────────────────────────────────
$items = [];
$r = mysqli_query($con, "SELECT * FROM usados_docs_items WHERE activo = 1 ORDER BY posicion, id_item");
while ($row = mysqli_fetch_assoc($r)) $items[] = $row;

// ── Asesores para filtro ────────────────────────────────────────────────────
$asesores = [];
$r = mysqli_query($con, "SELECT DISTINCT u.idusuario, u.nombre
    FROM usuarios u
    INNER JOIN asignaciones_usados au ON au.asesortoma = u.idusuario
    WHERE au.entregado = 0 AND au.interno <> 0 AND au.interno <> ''
    ORDER BY u.nombre");
while ($row = mysqli_fetch_assoc($r)) $asesores[] = $row;

// ── Filtros ─────────────────────────────────────────────────────────────────
$filtro_asesor = isset($_GET['asesor']) ? (int)$_GET['asesor'] : 0;
$filtro_estado = (isset($_GET['estado']) && $_GET['estado'] !== '') ? (int)$_GET['estado'] : -1;

$where_extra = '';
if ($filtro_asesor > 0) {
    $where_extra = " AND asignaciones_usados.asesortoma = $filtro_asesor";
}

// ── Query principal de usados ───────────────────────────────────────────────
$SQL = "SELECT
    asignaciones_usados.id_unidad,
    asignaciones_usados.interno,
    asignaciones_usados.vehiculo,
    asignaciones_usados.dominio,
    usuario_1.nombre  AS nombre_asesor_toma,
    usuarios.nombre   AS nombre_asesor_venta
FROM
    usuarios usuario_1
    JOIN asignaciones_usados ON asignaciones_usados.asesortoma = usuario_1.idusuario
    JOIN usuarios             ON asignaciones_usados.id_asesor  = usuarios.idusuario
WHERE
    asignaciones_usados.entregado = 0
    AND asignaciones_usados.interno <> 0
    AND asignaciones_usados.interno <> ''
    AND asignaciones_usados.borrar = 0
    AND asignaciones_usados.guardado = 1
    AND asignaciones_usados.fec_entrega IS NULL
    $where_extra
ORDER BY asignaciones_usados.interno";

$result = mysqli_query($con, $SQL);
$usados     = [];
$ids_unidad = [];
while ($row = mysqli_fetch_assoc($result)) {
    $usados[]     = $row;
    $ids_unidad[] = (int)$row['id_unidad'];
}

// ── Cargar seguimiento de todos los usados ──────────────────────────────────
$seguimiento = [];  // [id_unidad][id_item] => row
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

// ── Aplicar filtro de estado general ────────────────────────────────────────
if ($filtro_estado >= 0) {
    $usados = array_values(array_filter($usados, function ($u) use ($filtro_estado, $items, $seguimiento) {
        return calcular_estado_general((int)$u['id_unidad'], $items, $seguimiento) === $filtro_estado;
    }));
}

$total = count($usados);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Seguimiento Documentación — Usados</title>
    <link rel="shortcut icon" type="image/x-icon" href="../imagenes/favicon.ico">
    <link rel="stylesheet" href="css/usados_docs.css">
    <script src="../asignacion/js/jquery-2.1.3.min.js"></script>
</head>
<body>

<!-- ── HEADER ──────────────────────────────────────────────────────────────── -->
<header class="ud-header">
    <div class="ud-header-left">
        <img src="../imagenes/logodyv_c.png" alt="Logo" class="ud-logo">
        <h1 class="ud-title">Seguimiento Documentación — Usados</h1>
    </div>
    <div class="ud-header-right">
        <span class="ud-user"><?= htmlspecialchars($nombre_usuario) ?></span>
        <?php if ($es_admin): ?>
            <button class="btn-admin" id="btn-abrir-admin">&#9881; Gestionar Ítems</button>
        <?php endif; ?>
    </div>
</header>

<!-- ── BARRA DE FILTROS ─────────────────────────────────────────────────────── -->
<div class="ud-filters">
    <form method="GET" action="">
        <div class="filter-group">
            <label>Asesor:</label>
            <select name="asesor" onchange="this.form.submit()">
                <option value="0">Todos</option>
                <?php foreach ($asesores as $a): ?>
                    <option value="<?= $a['idusuario'] ?>"
                        <?= $filtro_asesor == $a['idusuario'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($a['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="filter-group">
            <label>Estado:</label>
            <select name="estado" onchange="this.form.submit()">
                <option value=""  <?= $filtro_estado < 0  ? 'selected' : '' ?>>Todos</option>
                <option value="0" <?= $filtro_estado === 0 ? 'selected' : '' ?>>&#9675; Pendiente</option>
                <option value="3" <?= $filtro_estado === 3 ? 'selected' : '' ?>>&#9681; En proceso</option>
                <option value="1" <?= $filtro_estado === 1 ? 'selected' : '' ?>>&#10003; Completo</option>
            </select>
        </div>
        <div class="filter-info">
            <span><?= $total ?> usado<?= $total !== 1 ? 's' : '' ?></span>
            <?php if ($filtro_asesor > 0 || $filtro_estado >= 0): ?>
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
                <th class="col-sticky col-interno">Interno</th>
                <th class="col-sticky col-vehiculo">Vehículo</th>
                <th class="col-sticky col-dominio">Dominio</th>
                <th class="col-sticky col-asesor">Asesor toma</th>
                <?php foreach ($items as $item): ?>
                    <th class="col-item"
                        title="<?= htmlspecialchars($item['descripcion'] ?? $item['nombre']) ?>">
                        <?= htmlspecialchars($item['nombre']) ?>
                    </th>
                <?php endforeach; ?>
                <th class="col-estado-gral col-sticky-right">Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usados as $usado):
                $id_unidad   = (int)$usado['id_unidad'];
                $estado_gral = calcular_estado_general($id_unidad, $items, $seguimiento);
                $eg          = $ESTADOS[$estado_gral];
            ?>
            <tr class="ud-row" data-id-unidad="<?= $id_unidad ?>">
                <td class="col-sticky col-interno"><?= htmlspecialchars($usado['interno']) ?></td>
                <td class="col-sticky col-vehiculo"><?= htmlspecialchars($usado['vehiculo']) ?></td>
                <td class="col-sticky col-dominio"><?= htmlspecialchars($usado['dominio'] ?? '—') ?></td>
                <td class="col-sticky col-asesor"><?= htmlspecialchars($usado['nombre_asesor_toma']) ?></td>

                <?php foreach ($items as $item):
                    $id_item = (int)$item['id_item'];
                    $seg     = $seguimiento[$id_unidad][$id_item] ?? null;
                    $estado  = $seg ? (int)$seg['estado'] : 0;
                    $edata   = $ESTADOS[$estado];

                    // Tooltip
                    $titulo = $edata['label'];
                    if ($seg && $seg['observacion']) {
                        $titulo .= ': ' . $seg['observacion'];
                    }
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
                <div class="ud-modal-titulo"  id="modal-titulo"></div>
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
