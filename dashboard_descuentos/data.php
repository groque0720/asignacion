<?php
include 'config/config_app.php';
include 'actions/reservas_descuento.php';

header('Content-Type: application/json');
echo json_encode($dyv, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>

