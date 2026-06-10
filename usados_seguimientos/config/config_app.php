<?php
/*
 * Bootstrap del módulo Seguimiento Documentación Usados (versión moderna).
 * Auth + conexión los provee comun/bootstrap.php (expone $con, $userId, $userName, $perfil).
 * Acá se calculan los permisos del módulo y las rutas de adjuntos.
 *
 * Endpoints JSON: setear  $AUTH_FAIL = 'json';  antes del require.
 *
 * Convive con el módulo viejo usados_docs/: comparten las MISMAS tablas
 * (usados_docs_*) y el MISMO almacén físico de archivos (usados_docs/uploads/).
 */

require __DIR__ . '/../../comun/bootstrap.php';   // auth + conexión: expone $con, $userId, $userName, $perfil
require __DIR__ . '/../funciones/schema.php';     // crea las tablas usados_docs_* si no existen

// ─────────────────────────────────────────────────────────────────────────────
// ACCESO AL MÓDULO ── usuarios habilitados a INGRESAR (ordenados por id).
// ⚠️ Sólo estos ids pueden entrar. Asegurate de incluir tu propio usuario.
// ─────────────────────────────────────────────────────────────────────────────
$USUARIOS_MODULO = [11, 14, 56, 66, 71, 79, 94, 96, 106, 135, 138, 139, 144];

if (!in_array($userId, $USUARIOS_MODULO, true)) {
    if (isset($AUTH_FAIL) && $AUTH_FAIL === 'json') {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(403);
        echo json_encode(['error' => 'Sin acceso a este módulo']);
    } else {
        header('Location: ../login');
    }
    exit();
}

// ─────────────────────────────────────────────────────────────────────────────
// PERMISOS DENTRO DEL MÓDULO
// ─────────────────────────────────────────────────────────────────────────────
// Editar celdas (estado, observación, subir/eliminar adjuntos): todos los que ingresan.
$puedeEditar = in_array($userId, $USUARIOS_MODULO, true);

// Administrar ítems/columnas (alta/edición): subconjunto reducido.  ⚠️ ajustá si hace falta.
$ADMIN_USUARIOS = [11, 14];
$esAdmin = in_array($userId, $ADMIN_USUARIOS, true);

// ─────────────────────────────────────────────────────────────────────────────
// Almacén de adjuntos (compartido con usados_docs/ durante la convivencia).
//   _DIR = ruta de filesystem para mover/borrar archivos.
//   _URL = ruta relativa a la URL del módulo (asignacion/usados_seguimientos/) para el navegador.
// Al deprecar usados_docs, mover la carpeta uploads/ acá y cambiar estas 2 líneas.
// ─────────────────────────────────────────────────────────────────────────────
$UPLOADS_DIR = __DIR__ . '/../../usados_docs/uploads/';
$UPLOADS_URL = '../usados_docs/uploads/';
