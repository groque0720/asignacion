<?php
/**
 * items_admin.php — Gestión de ítems (columnas).
 * Solo perfiles 1 y 2. Devuelve HTML para el modal de admin.
 */
require_once '_init.php';

if (!$es_admin) {
    echo '<p class="modal-error">Sin permisos para acceder a esta sección.</p>';
    exit;
}

$items_all = [];
$r = mysqli_query($con, "SELECT * FROM usados_docs_items ORDER BY posicion, id_item");
while ($row = mysqli_fetch_assoc($r)) $items_all[] = $row;
?>

<div class="admin-section">

    <!-- Lista de ítems -->
    <h3>Ítems configurados</h3>
    <?php if (empty($items_all)): ?>
        <p class="hist-empty">No hay ítems configurados aún.</p>
    <?php else: ?>
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Posición</th>
                <th>Activo</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items_all as $item): ?>
            <tr id="admin-row-<?= $item['id_item'] ?>">
                <td><?= $item['id_item'] ?></td>
                <td><strong><?= htmlspecialchars($item['nombre']) ?></strong></td>
                <td><?= htmlspecialchars($item['descripcion'] ?? '—') ?></td>
                <td><?= $item['posicion'] ?></td>
                <td><?= $item['activo'] ? '<span style="color:#2e7d32">&#10003; Sí</span>' : '<span style="color:#c62828">&#10007; No</span>' ?></td>
                <td>
                    <button class="btn-edit-item" onclick="editarItem(
                        <?= $item['id_item'] ?>,
                        <?= json_encode($item['nombre']) ?>,
                        <?= json_encode($item['descripcion'] ?? '') ?>,
                        <?= $item['posicion'] ?>,
                        <?= $item['activo'] ?>
                    )">&#9998; Editar</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <!-- Formulario nuevo ítem -->
    <h3>Agregar nuevo ítem</h3>
    <form id="form-nuevo-item" class="admin-form">
        <input type="hidden" name="id_item" value="0">
        <div class="form-row">
            <div class="form-group">
                <label>Nombre <span class="req">*</span></label>
                <input type="text" name="nombre" required maxlength="100" placeholder="Ej: Fotos exteriores">
            </div>
            <div class="form-group">
                <label>Descripción</label>
                <input type="text" name="descripcion" maxlength="255" placeholder="Opcional">
            </div>
            <div class="form-group form-group-sm">
                <label>Posición</label>
                <input type="number" name="posicion" value="<?= count($items_all) + 1 ?>" min="1" style="width:70px">
            </div>
        </div>
        <button type="submit" class="btn-guardar">+ Agregar ítem</button>
    </form>

    <!-- Formulario editar ítem (oculto) -->
    <div id="form-editar-contenedor" style="display:none">
        <h3>Editar ítem</h3>
        <form id="form-edit-item" class="admin-form">
            <input type="hidden" name="id_item" id="edit-id-item">
            <div class="form-row">
                <div class="form-group">
                    <label>Nombre <span class="req">*</span></label>
                    <input type="text" name="nombre" id="edit-nombre" required maxlength="100">
                </div>
                <div class="form-group">
                    <label>Descripción</label>
                    <input type="text" name="descripcion" id="edit-descripcion" maxlength="255">
                </div>
                <div class="form-group form-group-sm">
                    <label>Posición</label>
                    <input type="number" name="posicion" id="edit-posicion" min="1" style="width:70px">
                </div>
            </div>
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="activo" id="edit-activo" value="1">
                    Activo (visible en la tabla)
                </label>
            </div>
            <div style="display:flex; gap:10px">
                <button type="submit" class="btn-guardar">Guardar cambios</button>
                <button type="button" class="btn-link" onclick="cancelarEdicion()">Cancelar</button>
            </div>
        </form>
    </div>

    <p class="admin-nota">
        &#9432; Al desactivar un ítem deja de aparecer en la tabla, pero sus datos se conservan.
        Al reactivarlo vuelve a mostrarse.
    </p>
</div>
