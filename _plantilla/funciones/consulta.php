<?php
/*
 * Lógica de datos del módulo (la usan las actions). Requiere $con (config_app.php).
 * Prefijo pl_ = "plantilla"; renombralo al prefijo del módulo nuevo.
 */

// Listado paginado. Devuelve ['total'=>int, 'rows'=>array].
function pl_lista($con, $q, $per, $offset) {
    $qe = mysqli_real_escape_string($con, $q);

    // AJUSTAR: tabla, columnas y filtros reales del módulo.
    $FROM = "FROM tu_tabla t";
    $W = "1 = 1";
    if ($q !== '') $W .= " AND t.nombre LIKE '%$qe%'";

    $total = (int)mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) n $FROM WHERE $W"))['n'];

    $res = mysqli_query($con,
        "SELECT t.id, t.nombre
         $FROM
         WHERE $W
         ORDER BY t.nombre ASC
         LIMIT ".(int)$per." OFFSET ".(int)$offset);
    if (!$res) return ['error' => mysqli_error($con)];

    $rows = [];
    while ($r = mysqli_fetch_assoc($res)) {
        $rows[] = ['id' => (int)$r['id'], 'nombre' => $r['nombre']];
    }
    return ['total' => $total, 'rows' => $rows];
}
