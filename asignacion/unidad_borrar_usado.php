<?php 

include("funciones/func_mysql.php");
conectar();
mysqli_query($con,"SET NAMES 'utf8'");
extract($_POST);

//Si el vehículo se borra cuando el asesor hace click el icono del basurero, se envian los datos del vehiculo a eliminar a la API de usados de la página web
// y luego se elimina el registro de la base de datos local.

// Primero buscamos el id_unidad en la base de datos local
$SQL= "SELECT * FROM asignaciones_usados WHERE id_unidad = ".$id_unidad;
$resultado = mysqli_query($con, $SQL);
if ($resultado) {
    $unidad = mysqli_fetch_array($resultado);
    $id_unidad = $unidad['id_unidad'];
} else {
    echo "Error: " . mysqli_error($con);
    exit;
}

// Crear el array de datos para enviar a la API
$datosAPI = [
    'dominio' => $unidad['dominio'],
    'interno' => $unidad['interno'],
    'borrar' => "1"
];

// URL de la API
$url = 'https://panelweb.derkayvargas.com/api/usados/webhook/update-usado';

// Encodificar datos a JSON
$jsonData = json_encode($datosAPI);

// Configurar el contexto para la solicitud POST
$opciones = [
    'http' => [
        'method' => 'POST',
        'header' => [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData),
            'Access-Control-Allow-Origin: *',
            'Access-Control-Allow-Methods: POST, GET, OPTIONS',
            'Access-Control-Allow-Headers: Content-Type, Authorization'
        ],
        'content' => $jsonData,
        'ignore_errors' => true // Permitir obtener respuesta incluso con errores HTTP
    ]
];

// Crear el contexto de la solicitud
$contexto = stream_context_create($opciones);

// Realizar la solicitud y obtener la respuesta (silenciamos errores con @)
@file_get_contents($url, false, $contexto); // Realiza la solicitud a la API: url, false, contexto. Url: url de la API, false: no se usa el modo de solo lectura, contexto: opciones de la solicitud.

// Verificar si hubo error
if ($response === false) {
    // Manejar el error de la solicitud a la API
    echo "Error: No se pudo conectar a la API.";
    exit;
}

// Independientemente de la respuesta de la API, eliminar el registro local
$SQL="DELETE FROM asignaciones_usados WHERE id_unidad = ".$id_unidad;
mysqli_query($con, $SQL);

?>
