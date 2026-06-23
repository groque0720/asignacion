<?php
@session_start();
if ($_SESSION["autentificado"] != "SI") {
	header("Location: ../login");
	exit();
}
include_once("config.php");
if (!in_array($_SESSION["idperfil"], ENCUESTA_PERFILES)) {
	header("Location: ../login");
	exit();
}
include_once("funciones/func_mysql.php");
conectar();

$sec = isset($_GET['sec']) ? $_GET['sec'] : 'entregas';
if (!in_array($sec, ['entregas', 'config', 'resultados'])) $sec = 'entregas';
// Solo perfil autorizado puede acceder a la configuración
if ($sec === 'config' && !in_array($_SESSION["id"], ENCUESTA_USUARIOS_CONFIG)) $sec = 'entregas';

// Filtro sucursal: default = sucursal del usuario; 0 = todas
$filtro_sucursal = isset($_GET['suc']) ? (int)$_GET['suc'] : (int)$_SESSION["idsuc"];

// Lista de sucursales para el dropdown
$sucursales_list = [];
$res_suc = mysqli_query($con, "SELECT idsucursal, sucursal FROM sucursales ORDER BY sucursal ASC");
while ($s = mysqli_fetch_array($res_suc)) $sucursales_list[] = $s;
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Encuesta Satisfacción 0km</title>
	<link rel="shortcut icon" type="image/x-icon" href="../asignacion/imagenes/favicon.ico" />
	<script src="js/jquery-2.1.3.min.js"></script>
	<script src="alertas_query/sweetalert-dev.js"></script>
	<link rel="stylesheet" href="alertas_query/sweetalert.css">
	<link rel="stylesheet" href="en_proceso/en_proceso.css">
	<link rel="stylesheet" href="css/encuesta_admin.css">
	<link href="../asignacion/css/iconos.css" rel="stylesheet">
</head>
<body>

<?php include('en_proceso/en_proceso.php'); ?>

<!-- CABECERA -->
<div class="enc-cabecera">
	<div class="enc-cab-izq">
		<img class="enc-logo" src="../asignacion/imagenes/logodyv_c.png" alt="Logo">
		<span class="enc-titulo">Encuesta Satisfacción 0km</span>
	</div>
	<div class="enc-cab-der">
		<span class="enc-usuario"><span class="icon-user"></span> <?php echo htmlspecialchars($_SESSION["usuario"]); ?></span>
		<input type="hidden" id="id_usuario" value="<?php echo $_SESSION["id"]; ?>">
	</div>
</div>

<!-- NAV SECUNDARIO -->
<div class="enc-nav">
	<a href="index.php?sec=entregas"  class="enc-nav-item <?php if($sec=='entregas')  echo 'activo'; ?>">
		<span class="icon-auto"></span> Entregas
	</a>
	<?php if (in_array($_SESSION["id"], ENCUESTA_USUARIOS_CONFIG)): ?>
	<a href="index.php?sec=config"    class="enc-nav-item <?php if($sec=='config')    echo 'activo'; ?>">
		<span class="icon-cogs"></span> Configurar Encuesta
	</a>
	<?php endif; ?>
	<a href="resultados/dashboard.php" class="enc-nav-item">
		<span class="icon-line-chart"></span> Resultados
	</a>
</div>

<!-- CONTENIDO -->
<div class="enc-contenido">
	<?php
	switch ($sec) {
		case 'entregas':   include('templates/tab_entregas.php');   break;
		case 'config':     include('templates/tab_config.php');     break;
		case 'resultados': include('templates/tab_resultados.php'); break;
	}
	?>
</div>

<div id="msg_respuesta"></div>

<script>
function mostrarCargando()  { $("#enc_cargando").show(); }
function ocultarCargando()  { $("#enc_cargando").hide(); }
</script>

</body>
</html>
