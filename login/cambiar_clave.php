<?php
include("funciones/func_mysql.php");
include("funciones/destino_login.php");
conectar();
mysqli_query($con, "SET NAMES 'utf8'");
@session_start();

// A esta pantalla sólo se llega con una sesión PARCIAL: clave correcta pero
// con cambio pendiente (la setea validarusuario.php). Sin eso, afuera.
if (empty($_SESSION['cambio_pendiente_id'])) {
	header('Location: index.php');
	exit;
}
$uid = (int) $_SESSION['cambio_pendiente_id'];

// Política de complejidad (validada en el servidor, nunca sólo en JS).
function clave_valida($c, &$err) {
	if (strlen($c) < 8)                    { $err = 'Debe tener al menos 8 caracteres.';        return false; }
	if (!preg_match('/[A-Z]/', $c))        { $err = 'Debe incluir al menos una MAYÚSCULA.';    return false; }
	if (!preg_match('/[a-z]/', $c))        { $err = 'Debe incluir al menos una minúscula.';    return false; }
	if (!preg_match('/[^A-Za-z0-9]/', $c)) { $err = 'Debe incluir al menos un símbolo (! @ # $ % ...).'; return false; }
	return true;
}

$error = '';

if (isset($_POST['clave1'])) {

	$c1 = $_POST['clave1'];
	$c2 = isset($_POST['clave2']) ? $_POST['clave2'] : '';

	// Traigo usuario para bloquear que la clave sea igual al nombre de usuario.
	$r = mysqli_query($con, "SELECT usuario FROM usuarios WHERE idusuario = ".$uid." LIMIT 1");
	$u = mysqli_fetch_assoc($r);

	if ($c1 !== $c2) {
		$error = 'Las dos claves no coinciden.';
	} elseif ($u && strcasecmp($c1, $u['usuario']) === 0) {
		$error = 'La clave no puede ser igual a tu usuario.';
	} elseif (!clave_valida($c1, $error)) {
		// $error ya quedó cargado por clave_valida()
	} else {
		// OK: guardo la clave hasheada y bajo la marca.
		$hashEsc = mysqli_real_escape_string($con, password_hash($c1, PASSWORD_DEFAULT));
		mysqli_query($con, "UPDATE usuarios SET clave = '".$hashEsc."', debe_cambiar_clave = 0 WHERE idusuario = ".$uid);

		// Recién ahora completo la sesión y lo dejo entrar.
		$campo = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM usuarios WHERE idusuario = ".$uid." LIMIT 1"));
		unset($_SESSION['cambio_pendiente_id']);

		$_SESSION["autentificado"] = "SI";
		$_SESSION["id"]            = $campo['idusuario'];
		$_SESSION["usuario"]       = $campo['nombre'];
		$_SESSION["idperfil"]      = $campo['idperfil'];
		$_SESSION["idsuc"]         = $campo['idsucursal'];
		$_SESSION["es_gerente"]    = $campo['es_gerente'];
		$_SESSION["id_negocio"]    = $campo['id_negocio'];

		mysqli_close($con);
		header('Location: ' . destino_login($campo));
		exit;
	}
}

$error = htmlspecialchars($error, ENT_QUOTES, 'UTF-8');
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Cambiar contraseña</title>
	<style>
		body { margin:0; min-height:100vh; display:flex; align-items:center; justify-content:center;
		       background:#f1f5f9; font-family:'Segoe UI',Arial,sans-serif; color:#1e293b; }
		.caja { background:#fff; padding:32px; border-radius:10px; box-shadow:0 4px 20px rgba(0,0,0,.12);
		        width:100%; max-width:400px; }
		h1 { margin:0 0 6px; font-size:19px; }
		.sub { margin:0 0 20px; font-size:13px; color:#64748b; }
		.reglas { background:#eff6ff; border-left:3px solid #2563eb; padding:10px 12px; margin:0 0 20px;
		          font-size:12.5px; line-height:1.6; color:#1e3a5f; }
		label { display:block; font-size:13px; font-weight:600; margin:14px 0 6px; }
		input { width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:6px; font-size:14px;
		        box-sizing:border-box; }
		input:focus { outline:none; border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,.15); }
		button { width:100%; margin-top:20px; padding:11px; background:#2563eb; color:#fff; border:0;
		         border-radius:6px; font-size:14px; font-weight:600; cursor:pointer; }
		button:hover { background:#1d4ed8; }
		.error { background:#fee2e2; color:#991b1b; padding:10px 12px; border-radius:6px;
		         font-size:13px; margin:0 0 8px; }
	</style>
</head>
<body>
	<div class="caja">
		<h1>Cambiá tu contraseña</h1>
		<p class="sub">Por seguridad, tenés que elegir una contraseña nueva para continuar.</p>

		<div class="reglas">
			La contraseña debe tener:
			<br>• al menos <strong>8 caracteres</strong>
			<br>• una <strong>mayúscula</strong> y una <strong>minúscula</strong>
			<br>• al menos un <strong>símbolo</strong> (! @ # $ % ...)
		</div>

		<?php if ($error != '') { ?>
			<p class="error"><?php echo $error; ?></p>
		<?php } ?>

		<form method="post" action="cambiar_clave.php" autocomplete="off">
			<label for="clave1">Nueva contraseña</label>
			<input type="password" name="clave1" id="clave1" autofocus required>

			<label for="clave2">Repetir contraseña</label>
			<input type="password" name="clave2" id="clave2" required>

			<button type="submit">Guardar y entrar</button>
		</form>
	</div>
</body>
</html>
