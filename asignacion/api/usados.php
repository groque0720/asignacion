<?php
include("../funciones/func_mysql.php");
conectar();
mysqli_query($con, "SET NAMES 'utf8'");

@session_start();
//COMPRUEBA QUE EL USUARIO ESTA AUTENTIFICADO
if ($_SESSION["autentificado"] != "SI") {
    //si no existe, envio a la página de autentificacion
    header("Location: /login");
    //ademas salgo de este script
    exit();
}

// $allowedOrigins = [
//     'https://derkayvargas.com',
//     // 'https://otraaplicacion.com'
//   ];
  
//   // Obtener el origen de la solicitud
//   $origin = $_SERVER['HTTP_ORIGIN'] ?? null;
  
//   if ($origin && in_array($origin, $allowedOrigins)) {
//     header('Access-Control-Allow-Origin: ' . $origin);
//     header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
//     header('Access-Control-Allow-Headers: Content-Type, Authorization');
//   }

$SQL = "SELECT au.*, e.estado_usado, e.posicion 
        FROM view_asignaciones_usados au 
        INNER JOIN asignaciones_usados_estados e ON au.id_estado = e.id_estado_usado";

// Si hay parámetros GET, construimos el WHERE
if (!empty($_GET)) {
    $where = [];
    
    foreach ($_GET as $campo => $valor) { // Recorre los campos y valores del GET 
        $campo = mysqli_real_escape_string($con, $campo); //escapa caracteres especiales
        $valores = explode(',', $valor); // Permite valores separados por coma
        
        $condiciones = []; // Condiciones para un campo
        foreach ($valores as $v) {
            $v = mysqli_real_escape_string($con, $v);//escapa caracteres especiales
            $condiciones[] = "au.$campo = '$v'"; // Crea una condición por cada valor del campo 
        }
        
        $where[] = '(' . implode(' OR ', $condiciones) . ')';//une los valores con OR y los mete en un array de condiciones 
    }
    
    if (!empty($where)) { // Si hay condiciones, las agregamos al SQL
        $SQL .= " WHERE " . implode(' AND ', $where); //une las condiciones con AND 
    }
}

$SQL .= " ORDER BY e.posicion, au.vehiculo";

$usados = mysqli_query($con, $SQL);

$resultados = [];
if ($usados) {
    while ($fila = mysqli_fetch_assoc($usados)) {
        $resultados[] = $fila;
    }
}

mysqli_close($con);

// Enviar los datos como JSON
header('Content-Type: application/json');
echo json_encode($resultados);
?>
