<?php
/**
 * celda_modal.php — Devuelve HTML con el formulario de detalle de una celda.
 * Llamado vía AJAX GET.
 */
require_once '_init.php';

$id_unidad = isset($_GET['id_unidad']) ? (int)$_GET['id_unidad'] : 0;
$id_item   = isset($_GET['id_item'])   ? (int)$_GET['id_item']   : 0;

if (!$id_unidad || !$id_item) {
    echo '<p class="modal-error">Parámetros inválidos.</p>';
    exit;
}

// Verificar que el ítem existe y está activo
$r    = mysqli_query($con, "SELECT * FROM usados_docs_items WHERE id_item = $id_item AND activo = 1");
$item = mysqli_fetch_assoc($r);
if (!$item) {
    echo '<p class="modal-error">Ítem no encontrado.</p>';
    exit;
}

// Seguimiento actual
$r   = mysqli_query($con, "SELECT s.*, u.nombre AS nombre_usuario
    FROM usados_docs_seguimiento s
    LEFT JOIN usuarios u ON s.id_usuario = u.idusuario
    WHERE s.id_unidad = $id_unidad AND s.id_item = $id_item");
$seg = mysqli_fetch_assoc($r);

$estado_actual  = $seg ? (int)$seg['estado'] : 0;
$archivo_actual = $seg ? $seg['archivo'] : null;

// ── Lista unificada de adjuntos de la celda ─────────────────────────────────
// Fuentes: tabla nueva usados_docs_archivos + archivo legacy en seguimiento.
//   tipo 'adjunto'  → fila de usados_docs_archivos (id real)
//   tipo 'actual'   → archivo legacy guardado en usados_docs_seguimiento.archivo
$adjuntos = [];

$ra = mysqli_query($con, "SELECT a.id, a.archivo, a.fecha, u.nombre
    FROM usados_docs_archivos a
    LEFT JOIN usuarios u ON a.id_usuario = u.idusuario
    WHERE a.id_unidad = $id_unidad AND a.id_item = $id_item
    ORDER BY a.fecha DESC");
while ($row = mysqli_fetch_assoc($ra)) {
    $adjuntos[] = ['tipo' => 'adjunto', 'id' => (int)$row['id'],
                   'archivo' => $row['archivo'], 'nombre' => $row['nombre'], 'fecha' => $row['fecha']];
}

if ($archivo_actual) {
    $adjuntos[] = ['tipo' => 'actual', 'id' => 0,
                   'archivo' => $archivo_actual,
                   'nombre' => $seg['nombre_usuario'] ?? null, 'fecha' => $seg['updated_at'] ?? null];
}
?>

<form id="form-celda" enctype="multipart/form-data">
    <input type="hidden" name="id_unidad" value="<?= $id_unidad ?>">
    <input type="hidden" name="id_item"   value="<?= $id_item ?>">

    <?php if ($seg): ?>
    <div class="form-meta-bar">
        <span class="form-meta-estado badge-mini <?= $ESTADOS[$estado_actual]['class'] ?>">
            <?= $ESTADOS[$estado_actual]['icon'] ?> <?= $ESTADOS[$estado_actual]['label'] ?>
        </span>
        <span class="form-meta-info">
            <?= htmlspecialchars($seg['nombre_usuario'] ?? 'Desconocido') ?>
            &mdash; <?= date('d/m/Y H:i', strtotime($seg['updated_at'])) ?>
        </span>
    </div>
    <?php endif; ?>

    <!-- Estado -->
    <div class="form-section">
        <div class="form-section-label">Estado</div>
        <div class="estado-opciones">
            <?php foreach ($ESTADOS as $val => $e): ?>
            <label class="estado-opcion <?= $e['class'] ?> <?= $estado_actual === $val ? 'activo' : '' ?>">
                <input type="radio" name="estado" value="<?= $val ?>"
                       <?= $estado_actual === $val ? 'checked' : '' ?>>
                <span class="estado-icon"><?= $e['icon'] ?></span>
                <span class="estado-texto"><?= $e['label'] ?></span>
            </label>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Observación -->
    <div class="form-section">
        <div class="form-section-label">Observación</div>
        <textarea name="observacion" rows="2"
                  placeholder="Opcional..."><?= htmlspecialchars($seg['observacion'] ?? '') ?></textarea>
    </div>

    <!-- Archivos -->
    <div class="form-section">
        <div class="form-section-label">Archivos adjuntos</div>

        <?php if (!empty($adjuntos)): ?>
        <div class="archivos-previos" id="archivos-lista">
            <?php foreach ($adjuntos as $a): ?>
            <div class="archivo-prev-item archivo-row" id="archivo-row-<?= $a['tipo'] ?>-<?= $a['id'] ?>">
                <a href="uploads/<?= htmlspecialchars($a['archivo']) ?>" target="_blank" class="hist-link">
                    &#128206; <?= htmlspecialchars($a['archivo']) ?>
                </a>
                <span class="archivo-prev-meta">
                    <?= htmlspecialchars($a['nombre'] ?? 'Desconocido') ?><?php if (!empty($a['fecha'])): ?>,
                    <?= date('d/m/y H:i', strtotime($a['fecha'])) ?><?php endif; ?>
                </span>
                <button type="button" class="btn-del-archivo"
                    onclick="eliminarArchivo('<?= $a['tipo'] ?>', <?= $id_unidad ?>, <?= $id_item ?>, <?= $a['id'] ?>, this)"
                    title="Eliminar archivo">&#10005;</button>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="archivo-upload-area">
            <label class="archivo-upload-label">
                <span>&#8679; Subir archivo(s)</span>
                <small>PDF, JPG, PNG &mdash; podés seleccionar varios &mdash; máx. 5 MB c/u</small>
                <input type="file" name="archivo[]" multiple accept=".pdf,.jpg,.jpeg,.png,.gif,.webp">
            </label>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn-guardar">Guardar</button>
        <button type="button" class="btn-historial"
                onclick="verHistorial(<?= $id_unidad ?>, <?= $id_item ?>)">
            &#128203; Historial
        </button>
    </div>
</form>

<!-- Historial (oculto por defecto) -->
<div id="historial-container" style="display:none">
    <div class="historial-titulo">Historial de cambios</div>
    <div id="historial-lista"><div class="ud-loading">Cargando...</div></div>
    <button type="button" class="btn-link" onclick="ocultarHistorial()">&#8592; Volver al formulario</button>
</div>
