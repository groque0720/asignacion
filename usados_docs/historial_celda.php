<?php
/**
 * historial_celda.php — Devuelve HTML con el historial de cambios de una celda.
 * Llamado vía AJAX GET.
 */
require_once '_init.php';

$id_unidad = isset($_GET['id_unidad']) ? (int)$_GET['id_unidad'] : 0;
$id_item   = isset($_GET['id_item'])   ? (int)$_GET['id_item']   : 0;

if (!$id_unidad || !$id_item) {
    echo '<p class="modal-error">Parámetros inválidos.</p>';
    exit;
}

$r = mysqli_query($con, "SELECT h.*, u.nombre
    FROM usados_docs_historial h
    LEFT JOIN usuarios u ON h.id_usuario = u.idusuario
    WHERE h.id_unidad = $id_unidad AND h.id_item = $id_item
    ORDER BY h.fecha DESC
    LIMIT 50");

$rows = [];
while ($row = mysqli_fetch_assoc($r)) $rows[] = $row;

if (empty($rows)):
?>
    <p class="hist-empty">Sin historial de cambios registrado.</p>
<?php else: ?>
    <?php foreach ($rows as $h):
        $ea = ($h['estado_anterior'] !== null) ? $ESTADOS[(int)$h['estado_anterior']] : null;
        $en = $ESTADOS[(int)$h['estado_nuevo']];
    ?>
    <div class="hist-item">
        <div class="hist-meta">
            <strong><?= htmlspecialchars($h['nombre'] ?? 'Desconocido') ?></strong>
            <span class="hist-fecha"><?= date('d/m/Y H:i', strtotime($h['fecha'])) ?></span>
        </div>
        <div class="hist-cambio">
            <?php if ($ea): ?>
                <span class="badge-mini <?= $ea['class'] ?>"><?= $ea['icon'] ?> <?= $ea['label'] ?></span>
                <span class="hist-arrow">&#8594;</span>
            <?php endif; ?>
            <span class="badge-mini <?= $en['class'] ?>"><?= $en['icon'] ?> <?= $en['label'] ?></span>
        </div>
        <?php if ($h['observacion']): ?>
            <div class="hist-obs">"<?= htmlspecialchars($h['observacion']) ?>"</div>
        <?php endif; ?>
        <?php if ($h['archivo']): ?>
            <div>
                <a href="uploads/<?= htmlspecialchars($h['archivo']) ?>" target="_blank" class="hist-link">
                    &#128206; Ver archivo adjunto
                </a>
            </div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
<?php endif; ?>
