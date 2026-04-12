<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Encuesta no disponible</title>
	<link rel="stylesheet" href="../css/encuesta_publica.css">
</head>
<body>
<?php
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';
if ($tipo == 'completada') {
	$icono   = '✅';
	$titulo  = 'Encuesta ya completada';
	$mensaje = 'Esta encuesta ya fue respondida. ¡Gracias por tu colaboración!';
} elseif ($tipo == 'sin_preguntas') {
	$icono   = '⚙️';
	$titulo  = 'Encuesta en configuración';
	$mensaje = 'Esta encuesta aún no tiene preguntas configuradas. Intentá más tarde.';
} else {
	$icono   = '🔗';
	$titulo  = 'Link inválido';
	$mensaje = 'El link de esta encuesta no es válido o venció. Si creés que es un error, consultá con el concesionario.';
}
?>
<div class="enc-pantalla-final" style="background:#7f8c8d;">
	<div class="enc-final-card">
		<div class="enc-final-icono"><?php echo $icono; ?></div>
		<div class="enc-final-titulo" style="color:#555;"><?php echo $titulo; ?></div>
		<p class="enc-final-mensaje"><?php echo $mensaje; ?></p>
		<p style="margin-top:18px;font-size:12px;color:#aaa;">Podés cerrar esta ventana.</p>
	</div>
</div>
</body>
</html>
