<?php
// Script simple para manejar la comunicación con la API externa usando file_get_contents() en lugar de cURL

// Habilitar registro de errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Recoger los datos enviados por el formulario
$dominio = isset($_POST['dominio']) ? trim($_POST['dominio']) : '';
$interno = isset($_POST['interno']) ? trim($_POST['interno']) : '';
$precio = isset($_POST['precio_venta']) ? floatval($_POST['precio_venta']) : 0;
$vehiculo = isset($_POST['vehiculo']) ? trim($_POST['vehiculo']) : '';
$año = isset($_POST['año']) ? intval($_POST['año']) : 0;
$km = isset($_POST['km']) ? intval($_POST['km']) : 0;
$color = isset($_POST['color']) ? intval($_POST['color']) : 0;
$id_estado_certificado = isset($_POST['id_estado_certificado']) ? intval($_POST['id_estado_certificado']) : 0;
$estado_reserva = isset($_POST['estado_reserva']) ? intval($_POST['estado_reserva']) : 0;
$entregado = isset($_POST['entregado']) ? intval($_POST['entregado']) : 0;
$id_estado = isset($_POST['id_estado']) ? intval($_POST['id_estado']) : 0;

// Verificar que tenemos datos válidos
if (empty($dominio) || empty($interno) || $precio < 0 || empty($vehiculo) || $año <= 0 || $km < 0 || $color <= 0 || $id_estado_certificado < 0 || $estado_reserva < 0 || $id_estado_certificado > 3 || $estado_reserva > 1 || $entregado < 0 || $entregado > 1 || $id_estado < 0 || $id_estado > 5) {
    echo "Error: Datos incompletos o inválidos";
    exit;
}

// Crear el array de datos para enviar a la API
$datosAPI = [
    'dominio' => $dominio,
    'interno' => $interno,
    'precio_venta' => $precio,
    'vehiculo' => $vehiculo,
    'año' => $año,
    'km' => $km,
    'color' => $color,
    'id_estado_certificado' => $id_estado_certificado,
    'estado_reserva' => $estado_reserva,
    'entregado' => $entregado,
    'id_estado' => $id_estado
];

// URL de la API
//$url = 'http://webdyvsa.oo/api/usados/webhook/update-usado'; // URL de la API de desarrollo
$url = 'https://panelweb.derkayvargas.com/api/usados/webhook/update-usado'; // URL de la API de producción


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

try {
    // Realizar la solicitud y obtener la respuesta
    $response = @file_get_contents($url, false, $contexto);
    
    // Verificar si hubo error
    if ($response === false) {
        $error = error_get_last();
        echo "Error al enviar datos a la API: " . (isset($error['message']) ? $error['message'] : 'Error desconocido');
    } else {
        // Si llegamos aquí es que la solicitud se completó (aunque puede haber un error HTTP)
        // Obtener código de respuesta HTTP
        $status_line = $http_response_header[0];
        preg_match('{HTTP\/\S*\s(\d{3})}', $status_line, $match);
        $status = $match[1];
        
        if ($status >= 200 && $status < 300) {
            // Éxito
            echo "<script>
                console.log('Precio actualizado en API: $precio para dominio $dominio');
                window.history.back();
            </script>";
        } else {
            // Error HTTP
            echo "Error en la respuesta de la API: Código $status - $response";
        }
    }
} catch (Exception $e) {
    echo "Excepción: " . $e->getMessage();
}
?>
