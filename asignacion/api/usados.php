<?php
// Configurar CORS primero
header('Access-Control-Allow-Origin: *');   // feed publico de usados (consumido por la web externa). Ver nota de CORS mas abajo.
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Conexion directa a la base de datos sin incluir func_mysql.php
// TODO (Fase 3): reemplazar 'root' por un usuario MySQL de SOLO-SELECT sobre esta vista.
$con = mysqli_connect('localhost', 'root', '', 'asignacion');
if (!$con) {
    http_response_code(500);
    echo json_encode(['error' => 'No se pudo conectar a la base']);   // no filtrar el detalle del error
    exit;
}
mysqli_query($con, "SET NAMES 'utf8'");

// --- Whitelist de columnas filtrables --------------------------------------
// El nombre de columna ($campo) lo elige el cliente y se interpola como
// IDENTIFICADOR SQL. mysqli_real_escape_string NO protege identificadores
// (no van entre comillas), asi que antes se podia inyectar un UNION por la
// clave del GET y volcar cualquier tabla (ej. usuarios). Solucion: solo se
// acepta como filtro una columna REAL de la vista; cualquier otra clave se
// ignora. El VALOR si va escapado y entre comillas (eso ya estaba bien).
$columnasValidas = [];
$colRes = mysqli_query($con, "SHOW COLUMNS FROM view_asignaciones_usados");
if ($colRes) {
    while ($c = mysqli_fetch_assoc($colRes)) {
        $columnasValidas[$c['Field']] = true;
    }
}

$SQL = "SELECT au.*, e.estado_usado, e.posicion
        FROM view_asignaciones_usados au
        INNER JOIN asignaciones_usados_estados e ON au.id_estado = e.id_estado_usado";

// Si hay parametros GET, construimos el WHERE (solo con columnas whitelisteadas)
if (!empty($_GET)) {
    $where = [];

    foreach ($_GET as $campo => $valor) { // Recorre los campos y valores del GET
        // Solo se filtra por columnas reales de la vista. El resto se descarta
        // (esto es lo que cierra la inyeccion por identificador).
        if (!isset($columnasValidas[$campo])) {
            continue;
        }

        $valores = explode(',', $valor); // Permite valores separados por coma

        $condiciones = []; // Condiciones para un campo
        foreach ($valores as $v) {
            $v = mysqli_real_escape_string($con, $v); // escapa el valor
            $condiciones[] = "au.`$campo` = '$v'"; // columna whitelisteada + valor escapado y entre comillas
        }

        $where[] = '(' . implode(' OR ', $condiciones) . ')'; // une los valores del campo con OR
    }

    if (!empty($where)) { // Si hay condiciones validas, las agregamos al SQL
        $SQL .= " WHERE " . implode(' AND ', $where); // une las condiciones con AND
    }
}

$SQL .= " ORDER BY e.posicion, au.vehiculo, au.interno DESC"; // Ordena por posicion y luego por vehiculo e interno

$usados = mysqli_query($con, $SQL);

$resultados = [];
if ($usados) {
    while ($fila = mysqli_fetch_assoc($usados)) {
        $resultados[] = $fila;
    }
}

mysqli_close($con);

// Enviar los datos como JSON
echo json_encode($resultados);
?>
