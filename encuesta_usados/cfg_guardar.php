<?php
/*
 * Endpoint JSON de ESCRITURA del configurador. POST: res + accion + campos.
 *   res=encuesta : crear | editar | activar | desactivar | baja
 *   res=pregunta : guardar (crea si id=0) | baja | orden
 *   res=area     : guardar | eliminar
 *   res=nivel    : guardar | eliminar
 */
$AUTH_FAIL = 'json';
require __DIR__ . '/config/config_app.php';

header('Content-Type: application/json; charset=utf-8');
if (!$puedeConfigurar) { http_response_code(403); echo json_encode(['ok' => false, 'error' => 'Sin permiso']); exit(); }

function P($k, $def = '') { return isset($_POST[$k]) ? $_POST[$k] : $def; }
function esc($con, $v) { return mysqli_real_escape_string($con, (string)$v); }
function ok($extra = []) { echo json_encode(array_merge(['ok' => true], $extra), JSON_UNESCAPED_UNICODE); exit(); }
function fail($m) { echo json_encode(['ok' => false, 'error' => $m]); exit(); }

$res    = P('res');
$accion = P('accion');

// ── ENCUESTAS ───────────────────────────────────────────────────────────────
if ($res === 'encuesta') {
    if ($accion === 'crear' || $accion === 'editar') {
        $nombre = trim(P('nombre'));
        if ($nombre === '') fail('El nombre es obligatorio');
        $n  = esc($con, $nombre);
        $d  = esc($con, trim(P('descripcion')));
        $mb = esc($con, trim(P('mensaje_bienvenida')));
        if ($accion === 'crear') {
            if (!mysqli_query($con, "INSERT INTO encu_encuestas (nombre, descripcion, mensaje_bienvenida, activa)
                                     VALUES ('$n', '$d', '$mb', 0)")) fail(mysqli_error($con));
            ok(['id_encuesta' => mysqli_insert_id($con)]);
        } else {
            $id = (int)P('id_encuesta');
            if (!mysqli_query($con, "UPDATE encu_encuestas SET nombre='$n', descripcion='$d', mensaje_bienvenida='$mb'
                                     WHERE id_encuesta=$id")) fail(mysqli_error($con));
            ok();
        }
    }
    if ($accion === 'activar') {
        $id = (int)P('id_encuesta');
        mysqli_query($con, "UPDATE encu_encuestas SET activa = 0");          // sólo una activa
        mysqli_query($con, "UPDATE encu_encuestas SET activa = 1 WHERE id_encuesta = $id");
        ok();
    }
    if ($accion === 'desactivar') {
        $id = (int)P('id_encuesta');
        mysqli_query($con, "UPDATE encu_encuestas SET activa = 0 WHERE id_encuesta = $id");
        ok();
    }
    if ($accion === 'baja') {
        $id = (int)P('id_encuesta');
        $a = mysqli_fetch_assoc(mysqli_query($con, "SELECT activa FROM encu_encuestas WHERE id_encuesta=$id"));
        if ($a && (int)$a['activa'] === 1) fail('No se puede eliminar una encuesta activa. Desactivala primero.');
        mysqli_query($con, "UPDATE encu_encuestas SET baja = 1 WHERE id_encuesta = $id");
        ok();
    }
    fail('Acción inválida');
}

// ── PREGUNTAS ─────────────────────────────────────────────────────────────────
if ($res === 'pregunta') {
    if ($accion === 'guardar') {
        $id    = (int)P('id_pregunta');
        $idE   = (int)P('id_encuesta');
        $texto = trim(P('texto'));
        $tipo  = (int)P('tipo');
        if ($idE <= 0) fail('Encuesta inválida');
        if ($texto === '') fail('El texto de la pregunta es obligatorio');
        if ($tipo < 1 || $tipo > 5) fail('Tipo inválido');

        $pondera = (in_array($tipo, [4, 5], true)) ? 0 : ((int)P('pondera') === 1 ? 1 : 0);
        $esObs   = ((int)P('es_observacion') === 1) ? 1 : 0;
        $idArea  = (int)P('id_area'); $areaSql = $idArea > 0 ? $idArea : 'NULL';

        // Condición opcional
        $condRef = (int)P('cond_ref');
        if ($condRef > 0) {
            $op  = P('cond_op'); $val = trim(P('cond_val'));
            $ops = ['<','<=','=','>=','>','!='];
            if (!in_array($op, $ops, true) || $val === '') fail('Condición incompleta');
            $condRefSql = $condRef; $condOpSql = "'" . esc($con, $op) . "'"; $condValSql = "'" . esc($con, $val) . "'";
        } else {
            $condRefSql = 'NULL'; $condOpSql = 'NULL'; $condValSql = 'NULL';
        }

        $t = esc($con, $texto);
        if ($id > 0) {
            if (!mysqli_query($con,
                "UPDATE encu_preguntas SET texto_pregunta='$t', tipo_pregunta=$tipo, pondera=$pondera,
                        es_observacion=$esObs, id_area=$areaSql,
                        cond_id_preg_ref=$condRefSql, cond_operador=$condOpSql, cond_valor=$condValSql
                 WHERE id_pregunta=$id")) fail(mysqli_error($con));
        } else {
            $ord = (int)mysqli_fetch_assoc(mysqli_query($con,
                "SELECT COALESCE(MAX(nro_orden),0)+1 n FROM encu_preguntas WHERE id_encuesta=$idE"))['n'];
            if (!mysqli_query($con,
                "INSERT INTO encu_preguntas (id_encuesta, nro_orden, texto_pregunta, tipo_pregunta, pondera, es_observacion, id_area, cond_id_preg_ref, cond_operador, cond_valor)
                 VALUES ($idE, $ord, '$t', $tipo, $pondera, $esObs, $areaSql, $condRefSql, $condOpSql, $condValSql)")) fail(mysqli_error($con));
            $id = mysqli_insert_id($con);
        }

        // Opciones (tipos 3 y 4): reconciliar contra el set enviado
        if (in_array($tipo, [3, 4], true)) {
            $opciones = json_decode(P('opciones_json', '[]'), true);
            if (!is_array($opciones)) $opciones = [];
            $idsConservar = [];
            $n = 0;
            foreach ($opciones as $o) {
                $txt = isset($o['texto']) ? trim($o['texto']) : '';
                if ($txt === '') continue;
                $n++;
                $te = esc($con, $txt);
                $oid = isset($o['id']) ? (int)$o['id'] : 0;
                if ($oid > 0) {
                    mysqli_query($con, "UPDATE encu_opciones SET texto_opcion='$te', nro_orden=$n, baja=0 WHERE id_opcion=$oid AND id_pregunta=$id");
                    $idsConservar[] = $oid;
                } else {
                    mysqli_query($con, "INSERT INTO encu_opciones (id_pregunta, texto_opcion, nro_orden) VALUES ($id, '$te', $n)");
                    $idsConservar[] = mysqli_insert_id($con);
                }
            }
            // Baja lógica de las que ya no están
            $keep = count($idsConservar) ? implode(',', array_map('intval', $idsConservar)) : '0';
            mysqli_query($con, "UPDATE encu_opciones SET baja=1 WHERE id_pregunta=$id AND id_opcion NOT IN ($keep)");
        }
        ok(['id_pregunta' => $id]);
    }

    if ($accion === 'baja') {
        $id = (int)P('id_pregunta');
        // No permitir bajar una pregunta que es disparador de una condición vigente
        $dep = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) n FROM encu_preguntas WHERE cond_id_preg_ref=$id AND baja=0"));
        if ($dep && (int)$dep['n'] > 0) fail('Otra pregunta depende de esta como condición. Quitá esa condición primero.');
        mysqli_query($con, "UPDATE encu_preguntas SET baja=1 WHERE id_pregunta=$id");
        ok();
    }

    if ($accion === 'orden') {
        $id  = (int)P('id_pregunta');
        $dir = P('dir'); // 'up' | 'down'
        $cur = mysqli_fetch_assoc(mysqli_query($con, "SELECT id_encuesta, nro_orden FROM encu_preguntas WHERE id_pregunta=$id"));
        if (!$cur) fail('Pregunta inexistente');
        $idE = (int)$cur['id_encuesta']; $ord = (int)$cur['nro_orden'];
        $cmp = $dir === 'up' ? "nro_orden < $ord ORDER BY nro_orden DESC" : "nro_orden > $ord ORDER BY nro_orden ASC";
        $vec = mysqli_fetch_assoc(mysqli_query($con, "SELECT id_pregunta, nro_orden FROM encu_preguntas WHERE id_encuesta=$idE AND baja=0 AND $cmp LIMIT 1"));
        if ($vec) {
            $vid = (int)$vec['id_pregunta']; $vord = (int)$vec['nro_orden'];
            mysqli_query($con, "UPDATE encu_preguntas SET nro_orden=$vord WHERE id_pregunta=$id");
            mysqli_query($con, "UPDATE encu_preguntas SET nro_orden=$ord WHERE id_pregunta=$vid");
        }
        ok();
    }
    fail('Acción inválida');
}

// ── ÁREAS ────────────────────────────────────────────────────────────────────
if ($res === 'area') {
    if ($accion === 'guardar') {
        $id     = (int)P('id_area');
        $nombre = trim(P('nombre'));
        if ($nombre === '') fail('El nombre es obligatorio');
        $n   = esc($con, $nombre);
        $col = esc($con, trim(P('color')) ?: '#607d8b');
        $ord = (int)P('nro_orden');
        if ($id > 0) {
            if (!mysqli_query($con, "UPDATE encu_areas SET nombre='$n', color='$col', nro_orden=$ord WHERE id_area=$id")) fail(mysqli_error($con));
        } else {
            if (!mysqli_query($con, "INSERT INTO encu_areas (nombre, color, nro_orden) VALUES ('$n', '$col', $ord)")) fail(mysqli_error($con));
            $id = mysqli_insert_id($con);
        }
        ok(['id_area' => $id]);
    }
    if ($accion === 'eliminar') {
        $id = (int)P('id_area');
        mysqli_query($con, "UPDATE encu_preguntas SET id_area=NULL WHERE id_area=$id");  // liberar FK
        mysqli_query($con, "DELETE FROM encu_areas WHERE id_area=$id");
        ok();
    }
    fail('Acción inválida');
}

// ── NIVELES ──────────────────────────────────────────────────────────────────
if ($res === 'nivel') {
    if ($accion === 'guardar') {
        $id     = (int)P('id_nivel');
        $nombre = trim(P('nombre'));
        if ($nombre === '') fail('El nombre es obligatorio');
        $n   = esc($con, $nombre);
        $col = esc($con, trim(P('color')) ?: '#607d8b');
        $de  = (float)P('desde'); $ha = (float)P('hasta');
        if ($id > 0) {
            if (!mysqli_query($con, "UPDATE encu_niveles SET nombre='$n', valor_desde=$de, valor_hasta=$ha, color='$col' WHERE id_nivel=$id")) fail(mysqli_error($con));
        } else {
            if (!mysqli_query($con, "INSERT INTO encu_niveles (nombre, valor_desde, valor_hasta, color) VALUES ('$n', $de, $ha, '$col')")) fail(mysqli_error($con));
            $id = mysqli_insert_id($con);
        }
        ok(['id_nivel' => $id]);
    }
    if ($accion === 'eliminar') {
        $id = (int)P('id_nivel');
        mysqli_query($con, "DELETE FROM encu_niveles WHERE id_nivel=$id");
        ok();
    }
    fail('Acción inválida');
}

fail('Recurso inválido');
