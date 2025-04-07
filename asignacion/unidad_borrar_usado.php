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


// Registrar los datos recibidos para depuración
$log_message = "Datos recibidos: Dominio=".$unidad['dominio'].", Interno=".$unidad['interno'].", ID Unidad=".$unidad['id_unidad']."\n";
file_put_contents('api_log.txt', $log_message, FILE_APPEND);

$datosAPI = [
    'dominio' => $unidad['dominio'],
    'interno' => $unidad['interno'],
    'borrar' => "1"
];

// URL de la API
$url = 'https://panelweb.derkayvargas.com/api/usados/webhook/update-usado';

// Inicializar cURL
$ch = curl_init($url);

// Configurar opciones de cURL para una solicitud POST
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datosAPI));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen(json_encode($datosAPI)),
    'Access-Control-Allow-Origin: *',
    'Access-Control-Allow-Methods: POST, GET, OPTIONS',
    'Access-Control-Allow-Headers: Content-Type, Authorization'
]);

// Añadir opciones para depuración
curl_setopt($ch, CURLOPT_VERBOSE, true);
$verbose = fopen('curl_log.txt', 'w+');
curl_setopt($ch, CURLOPT_STDERR, $verbose);

// Ejecutar la solicitud
$response = curl_exec($ch);
$error = curl_error($ch);
$info = curl_getinfo($ch);
curl_close($ch);

// Registrar la respuesta para depuración
$log_response = "Respuesta API: " . ($error ? "ERROR: $error" : $response) . "\n";
file_put_contents('api_log.txt', $log_response, FILE_APPEND);

$SQL="DELETE FROM asignaciones_usados WHERE id_unidad = ".$id_unidad;
mysqli_query($con, $SQL);

?>
