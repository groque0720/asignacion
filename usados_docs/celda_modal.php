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

$estado_actual = $seg ? (int)$seg['estado'] : 0;
?>

<form id="form-celda" enctype="multipart/form-data">
    <input type="hidden" name="id_unidad" value="<?= $id_unidad ?>">
    <input type="hidden" name="id_item"   value="<?= $id_item ?>">

    <?php if ($seg): ?>
    <div class="form-info-actual">
        <small>
            Última modificación:
            <strong><?= htmlspecialchars($seg['nombre_usuario'] ?? 'Desconocido') ?></strong>
            — <?= date('d/m/Y H:i', strtotime($seg['updated_at'])) ?>
        </small>
    </div>
    <?php endif; ?>

    <!-- Estado -->
    <div class="form-group">
        <label>Estado:</label>
        <div class="estado-opciones">
            <?php foreach ($ESTADOS as $val => $e): ?>
            <label class="estado-opcion <?= $e['class'] ?> <?= $estado_actual === $val ? 'activo' : '' ?>">
                <input type="radio" name="estado" value="<?= $val ?>"
                       <?= $estado_actual === $val ? 'checked' : '' ?>>
                <span class="estado-icon"><?= $e['icon'] ?></span>
                <span><?= $e['label'] ?></span>
            </label>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Observación -->
    <div class="form-group">
        <label>Observación:</label>
        <textarea name="observacion" rows="3"
                  placeholder="Opcional..."><?= htmlspecialchars($seg['observacion'] ?? '') ?></textarea>
    </div>

    <!-- Archivo adjunto -->
    <div class="form-group">
        <label>Adjuntar archivo <small>(PDF o imagen, máx. 2 MB)</small>:</label>
        <?php if ($seg && $seg['archivo']): ?>
        <div class="archivo-actual">
            <a href="uploads/<?= htmlspecialchars($seg['archivo']) ?>" target="_blank">
                &#128206; Ver archivo actual
            </a>
        </div>
        <?php endif; ?>
        <input type="file" name="archivo" accept=".pdf,.jpg,.jpeg,.png,.gif,.webp">
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
