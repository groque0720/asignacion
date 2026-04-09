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

// Archivos anteriores del historial (distintos al actual)
$archivos_hist = [];
$arch_excluir  = $archivo_actual ? "AND h.archivo != '" . mysqli_real_escape_string($con, $archivo_actual) . "'" : '';
$r2 = mysqli_query($con, "SELECT h.id, h.archivo, h.fecha, u.nombre
    FROM usados_docs_historial h
    LEFT JOIN usuarios u ON h.id_usuario = u.idusuario
    WHERE h.id_unidad = $id_unidad AND h.id_item = $id_item
      AND h.archivo IS NOT NULL
      $arch_excluir
    ORDER BY h.fecha DESC
    LIMIT 10");
while ($row = mysqli_fetch_assoc($r2)) $archivos_hist[] = $row;
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

    <!-- Archivo -->
    <div class="form-section">
        <div class="form-section-label">Archivo adjunto</div>

        <?php if ($archivo_actual): ?>
        <div class="archivo-actual" id="archivo-actual-row">
            <span class="archivo-icon">&#128206;</span>
            <a href="uploads/<?= htmlspecialchars($archivo_actual) ?>" target="_blank">
                Ver archivo actual
            </a>
            <button type="button" class="btn-del-archivo"
                onclick="eliminarArchivo('actual', <?= $id_unidad ?>, <?= $id_item ?>, 0, this)"
                title="Eliminar archivo">&#10005;</button>
        </div>
        <?php endif; ?>

        <?php if (!empty($archivos_hist)): ?>
        <div class="archivos-previos">
            <div class="archivos-previos-titulo">Anteriores:</div>
            <?php foreach ($archivos_hist as $ah): ?>
            <div class="archivo-prev-item" id="archivo-hist-<?= $ah['id'] ?>">
                <a href="uploads/<?= htmlspecialchars($ah['archivo']) ?>" target="_blank" class="hist-link">
                    &#128206; Ver archivo
                </a>
                <span class="archivo-prev-meta">
                    <?= htmlspecialchars($ah['nombre'] ?? 'Desconocido') ?>,
                    <?= date('d/m/y H:i', strtotime($ah['fecha'])) ?>
                </span>
                <button type="button" class="btn-del-archivo"
                    onclick="eliminarArchivo('historial', <?= $id_unidad ?>, <?= $id_item ?>, <?= $ah['id'] ?>, this)"
                    title="Eliminar archivo">&#10005;</button>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="archivo-upload-area">
            <label class="archivo-upload-label">
                <span>&#8679; Subir <?= $archivo_actual ? 'nuevo' : '' ?> archivo</span>
                <small>PDF, JPG, PNG &mdash; máx. 2 MB</small>
                <input type="file" name="archivo" accept=".pdf,.jpg,.jpeg,.png,.gif,.webp">
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
