<?php
// Script simple para manejar la comunicación con la API externa

// Habilitar registro de errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Recoger los datos enviados por el formulario
$dominio = isset($_POST['dominio']) ? trim($_POST['dominio']) : '';
$interno = isset($_POST['interno']) ? trim($_POST['interno']) : '';
$precio = isset($_POST['precio']) ? floatval($_POST['precio']) : 0;
$vehiculo = isset($_POST['vehiculo']) ? trim($_POST['vehiculo']) : '';
$año = isset($_POST['año']) ? intval($_POST['año']) : 0;
$km = isset($_POST['km']) ? intval($_POST['km']) : 0;
$color = isset($_POST['color']) ? intval($_POST['color']) : 0;
$id_estado_certificado = isset($_POST['id_estado_certificado']) ? intval($_POST['id_estado_certificado']) : 0;
$estado_reserva = isset($_POST['estado_reserva']) ? intval($_POST['estado_reserva']) : 0;
$entregado = isset($_POST['entregado']) ? intval($_POST['entregado']) : 0;
$id_estado = isset($_POST['id_estado']) ? intval($_POST['id_estado']) : 0;

// Verificar que tenemos datos válidos
if (empty($dominio) || empty($interno) || $precio <= 0 || empty($vehiculo) || $año <= 0 || $km < 0 || $color <= 0 || $id_estado_certificado < 0 || $estado_reserva < 0 || $id_estado_certificado > 3 || $estado_reserva > 1 || $entregado < 0 || $entregado > 1 || $id_estado < 0 || $id_estado > 5) {
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

// Ejecutar la solicitud
$response = curl_exec($ch);
$error = curl_error($ch);
$info = curl_getinfo($ch);
curl_close($ch);

// Si todo fue exitoso, mostrar un mensaje de éxito o redirigir
if (!$error) {
    echo "<script>
            console.log('Precio actualizado en API: $precio para dominio $dominio');
            window.history.back();
          </script>";
} else {
    echo "Error al enviar datos a la API: $error";
}
?>
