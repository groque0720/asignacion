/* ============================================================
   usados_docs.js — Módulo Seguimiento Documentación Usados
   Requiere jQuery
   ============================================================ */

$(document).ready(function () {

    // ── Click en celda ────────────────────────────────────────────────
    $(document).on('click', '.celda-item', function () {
        var $cell       = $(this);
        var idUnidad    = $cell.data('id-unidad');
        var idItem      = $cell.data('id-item');
        var nombreItem  = $cell.data('nombre-item');
        var vehiculo    = $cell.data('nombre-vehiculo');
        var interno     = $cell.data('interno');

        $('#modal-titulo').text('Interno ' + interno + '  —  ' + vehiculo);
        $('#modal-subtitulo').text(nombreItem);
        $('#ud-modal-body').html('<div class="ud-loading">Cargando...</div>');
        $('#ud-modal-overlay').fadeIn(150);

        // Guardar referencia para actualizar la celda tras guardar
        $('#ud-modal-overlay').data('cell', $cell);

        $.get('celda_modal.php', { id_unidad: idUnidad, id_item: idItem }, function (html) {
            $('#ud-modal-body').html(html);
            initEstadoOpciones();
        }).fail(function () {
            $('#ud-modal-body').html('<p class="modal-error">Error al cargar el formulario.</p>');
        });
    });

    // ── Cierre de modales con overlay ────────────────────────────────
    $('#ud-modal-overlay').on('click', function (e) {
        if ($(e.target).is('#ud-modal-overlay')) cerrarModal();
    });
    $('#ud-admin-overlay').on('click', function (e) {
        if ($(e.target).is('#ud-admin-overlay')) cerrarAdmin();
    });

    // ── Botón cerrar modal celda ──────────────────────────────────────
    $(document).on('click', '#btn-cerrar-modal', function () { cerrarModal(); });

    // ── Botones abrir/cerrar admin ────────────────────────────────────
    $(document).on('click', '#btn-abrir-admin, #btn-abrir-admin-2', function () { abrirAdmin(); });
    $(document).on('click', '#btn-cerrar-admin', function () { cerrarAdmin(); });

    // ── ESC cierra el modal activo ────────────────────────────────────
    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') {
            if ($('#ud-modal-overlay').is(':visible')) cerrarModal();
            else if ($('#ud-admin-overlay').is(':visible')) cerrarAdmin();
        }
    });

    // ── Guardar celda (AJAX con FormData para soportar archivo) ──────
    $(document).on('submit', '#form-celda', function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        var $btn = $(this).find('.btn-guardar');
        $btn.prop('disabled', true).text('Guardando...');

        $.ajax({
            url: 'guardar_celda.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (res) {
                if (res.ok) {
                    // Actualizar la celda en la tabla
                    var $cell = $('#ud-modal-overlay').data('cell');
                    if ($cell) {
                        $cell.removeClass('est-pendiente est-hecho est-no-corresponde est-en-proceso');
                        $cell.addClass(res.class);
                        $cell.find('.celda-icon').text(res.icon);
                        // Icono de clip si tiene archivo
                        if (res.archivo) {
                            if ($cell.find('.celda-clip').length === 0) {
                                $cell.append('<span class="celda-clip" title="Tiene archivo adjunto">&#128206;</span>');
                            }
                        }
                        // Actualizar estado general de la fila
                        actualizarEstadoGeneralFila($cell.data('id-unidad'));
                    }
                    cerrarModal();
                    toast('Guardado correctamente', 'success');
                } else {
                    toast('Error: ' + res.error, 'error');
                    $btn.prop('disabled', false).text('Guardar');
                }
            },
            error: function () {
                toast('Error de conexión al guardar', 'error');
                $btn.prop('disabled', false).text('Guardar');
            }
        });
    });

    // ── Nuevo ítem ────────────────────────────────────────────────────
    $(document).on('submit', '#form-nuevo-item', function (e) {
        e.preventDefault();
        var $btn = $(this).find('.btn-guardar');
        $btn.prop('disabled', true).text('Guardando...');

        $.post('guardar_item.php', $(this).serialize(), function (res) {
            if (res.ok) {
                toast('Ítem "' + $('[name="nombre"]', '#form-nuevo-item').val() + '" agregado', 'success');
                cargarAdmin();
            } else {
                toast('Error: ' + res.error, 'error');
                $btn.prop('disabled', false).text('+ Agregar ítem');
            }
        }, 'json').fail(function () {
            toast('Error de conexión', 'error');
            $btn.prop('disabled', false).text('+ Agregar ítem');
        });
    });

    // ── Editar ítem ───────────────────────────────────────────────────
    $(document).on('submit', '#form-edit-item', function (e) {
        e.preventDefault();
        var $btn   = $(this).find('.btn-guardar');
        $btn.prop('disabled', true).text('Guardando...');

        // El checkbox no aparece en serialize() cuando está desmarcado;
        // en PHP usamos isset($_POST['activo']) para resolver esto.
        $.post('guardar_item.php', $(this).serialize(), function (res) {
            if (res.ok) {
                toast('Ítem actualizado', 'success');
                cargarAdmin();
            } else {
                toast('Error: ' + res.error, 'error');
                $btn.prop('disabled', false).text('Guardar cambios');
            }
        }, 'json').fail(function () {
            toast('Error de conexión', 'error');
            $btn.prop('disabled', false).text('Guardar cambios');
        });
    });

});

// ── Estado opciones: manejo visual de radio buttons ───────────────────
function initEstadoOpciones() {
    $(document).off('click.estado').on('click.estado', '.estado-opcion', function () {
        $('.estado-opcion').removeClass('activo');
        $(this).addClass('activo');
        $(this).find('input[type="radio"]').prop('checked', true);
    });
}

// ── Cerrar modal celda ────────────────────────────────────────────────
function cerrarModal() {
    $('#ud-modal-overlay').fadeOut(150);
    setTimeout(function () {
        $('#ud-modal-body').html('<div class="ud-loading">Cargando...</div>');
    }, 160);
}

// ── Admin de ítems ────────────────────────────────────────────────────
function abrirAdmin() {
    $('#ud-admin-overlay').fadeIn(150);
    cargarAdmin();
}

function cerrarAdmin() {
    $('#ud-admin-overlay').fadeOut(150, function () {
        // Recargar para reflejar cambios en columnas
        location.reload();
    });
}

function cargarAdmin() {
    $('#ud-admin-body').html('<div class="ud-loading">Cargando...</div>');
    $.get('items_admin.php', function (html) {
        $('#ud-admin-body').html(html);
    }).fail(function () {
        $('#ud-admin-body').html('<p class="modal-error">Error al cargar la gestión de ítems.</p>');
    });
}

// ── Editar ítem: rellenar formulario ─────────────────────────────────
function editarItem(id, nombre, descripcion, posicion, activo) {
    $('#edit-id-item').val(id);
    $('#edit-nombre').val(nombre);
    $('#edit-descripcion').val(descripcion);
    $('#edit-posicion').val(posicion);
    $('#edit-activo').prop('checked', activo == 1);
    $('#form-editar-contenedor').slideDown(150);
    $('#edit-nombre').focus();
    // Scroll al formulario
    $('#ud-admin-body').animate({ scrollTop: $('#form-editar-contenedor').offset().top }, 200);
}

function cancelarEdicion() {
    $('#form-editar-contenedor').slideUp(150);
}

// ── Historial ─────────────────────────────────────────────────────────
function verHistorial(idUnidad, idItem) {
    $('#form-celda').hide();
    $('#historial-container').show();
    $('#historial-lista').html('<div class="ud-loading">Cargando...</div>');

    $.get('historial_celda.php', { id_unidad: idUnidad, id_item: idItem }, function (html) {
        $('#historial-lista').html(html);
    }).fail(function () {
        $('#historial-lista').html('<p class="modal-error">Error al cargar el historial.</p>');
    });
}

function ocultarHistorial() {
    $('#historial-container').hide();
    $('#form-celda').show();
}

// ── Recalcular estado general de una fila ─────────────────────────────
function actualizarEstadoGeneralFila(idUnidad) {
    var $row        = $('tr[data-id-unidad="' + idUnidad + '"]');
    var hayPendiente  = false;
    var hayEnProceso  = false;

    $row.find('.celda-item').each(function () {
        if ($(this).hasClass('est-pendiente'))  hayPendiente  = true;
        if ($(this).hasClass('est-en-proceso')) hayEnProceso  = true;
    });

    var icon, label, cls;
    if (hayPendiente) {
        icon = '○'; label = 'Pendiente'; cls = 'est-pendiente';
    } else if (hayEnProceso) {
        icon = '◑'; label = 'En proceso'; cls = 'est-en-proceso';
    } else {
        icon = '✓'; label = 'Completo'; cls = 'est-hecho';
    }

    var $badge = $row.find('.badge-estado');
    $badge.removeClass('est-pendiente est-hecho est-no-corresponde est-en-proceso');
    $badge.addClass(cls);
    $badge.text(icon + ' ' + label);
}

// ── Eliminar archivo adjunto ──────────────────────────────────────────
function eliminarArchivo(tipo, idUnidad, idItem, idHist, btn) {
    if (!confirm('¿Eliminar este archivo? Esta acción no se puede deshacer.')) return;

    var $btn = $(btn);
    $btn.prop('disabled', true).text('…');

    $.post('eliminar_archivo.php', {
        tipo:      tipo,
        id_unidad: idUnidad,
        id_item:   idItem,
        id_hist:   idHist
    }, function (res) {
        if (res.ok) {
            if (tipo === 'actual') {
                $('#archivo-actual-row').remove();
                // Quitar clip de la celda si ya no hay archivo actual
                var $cell = $('#ud-modal-overlay').data('cell');
                if ($cell) $cell.find('.celda-clip').remove();
            } else {
                $('#archivo-hist-' + idHist).remove();
            }
            toast('Archivo eliminado', 'success');
        } else {
            toast('Error: ' + res.error, 'error');
            $btn.prop('disabled', false).text('✕');
        }
    }, 'json').fail(function () {
        toast('Error de conexión', 'error');
        $btn.prop('disabled', false).text('✕');
    });
}

// ── Toast ─────────────────────────────────────────────────────────────
function toast(msg, tipo) {
    var $t = $('#ud-toast');
    $t.removeClass('toast-success toast-error toast-info visible');
    $t.addClass('toast-' + (tipo || 'info'));
    $t.text(msg);

    // Forzar reflow
    $t[0].offsetHeight;

    $t.addClass('visible');
    clearTimeout(window._toastTimer);
    window._toastTimer = setTimeout(function () {
        $t.removeClass('visible');
    }, 2400);
}
