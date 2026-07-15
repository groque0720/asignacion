<?php

	// Guard de clave para los scripts de mantenimiento que hacen cambios masivos
	// o irreversibles (habilitar planilla, asignar, transpaso, borrar notificaciones).
	//
	// Uso: primera línea del script, ANTES de cualquier salida HTML:
	//
	//     require_once(__DIR__ . '/funciones/guard_clave.php');
	//     guard_clave();
	//
	// Pide sesión iniciada + la clave de config_guard.php. La clave queda validada
	// GUARD_MINUTOS dentro de la misma sesión, así encadenar scripts no la re-pide.

	require_once(__DIR__ . '/config_guard.php');

	function guard_clave() {

		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}

		// 1) Estar logueado sigue siendo requisito previo.
		if (!isset($_SESSION['autentificado']) || $_SESSION['autentificado'] !== 'SI') {
			header('Location: ../login');
			exit;
		}

		// 2) Sesión bloqueada por intentos fallidos.
		if (isset($_SESSION['guard_bloqueo']) && time() < $_SESSION['guard_bloqueo']) {
			guard_form('Demasiados intentos fallidos. Cerrá sesión y volvé a entrar.', true);
			exit;
		}

		// 3) Clave ya validada hace poco.
		if (isset($_SESSION['guard_ok']) && (time() - $_SESSION['guard_ok']) < (GUARD_MINUTOS * 60)) {
			return;
		}

		// 4) Llegó el formulario.
		$error = '';
		if (isset($_POST['guard_clave'])) {

			if (password_verify($_POST['guard_clave'], GUARD_CLAVE_HASH)) {
				$_SESSION['guard_ok'] = time();
				unset($_SESSION['guard_intentos']);
				// Redirigir a GET limpio: un refresh no vuelve a correr el script.
				header('Location: ' . guard_url_limpia());
				exit;
			}

			sleep(1);
			$_SESSION['guard_intentos'] = isset($_SESSION['guard_intentos']) ? $_SESSION['guard_intentos'] + 1 : 1;

			if ($_SESSION['guard_intentos'] >= GUARD_MAX_INTENTOS) {
				$_SESSION['guard_bloqueo'] = time() + 900; // 15 minutos
				guard_form('Demasiados intentos fallidos. Cerrá sesión y volvé a entrar.', true);
				exit;
			}

			$restantes = GUARD_MAX_INTENTOS - $_SESSION['guard_intentos'];
			$error = 'Clave incorrecta. Te quedan ' . $restantes . ' intento(s).';
		}

		guard_form($error, false);
		exit;
	}

	// URL del propio script conservando el query string, armada por nosotros
	// (no se toma de REQUEST_URI para no reenviar lo que mande el cliente).
	function guard_url_limpia() {
		$url = basename($_SERVER['SCRIPT_NAME']);
		if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] !== '') {
			$url .= '?' . $_SERVER['QUERY_STRING'];
		}
		return $url;
	}

	function guard_form($error, $bloqueado) {

		$script = htmlspecialchars(basename($_SERVER['SCRIPT_NAME']), ENT_QUOTES, 'UTF-8');
		$action = htmlspecialchars(guard_url_limpia(), ENT_QUOTES, 'UTF-8');
		$error  = htmlspecialchars($error, ENT_QUOTES, 'UTF-8');

		header('Content-Type: text/html; charset=utf-8');
		?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Clave requerida</title>
	<style>
		body { margin:0; min-height:100vh; display:flex; align-items:center; justify-content:center;
		       background:#f1f5f9; font-family:'Segoe UI',Arial,sans-serif; color:#1e293b; }
		.caja { background:#fff; padding:32px; border-radius:10px; box-shadow:0 4px 20px rgba(0,0,0,.12);
		        width:100%; max-width:380px; }
		h1 { margin:0 0 6px; font-size:18px; }
		.script { margin:0 0 20px; font-size:13px; color:#64748b; font-family:Consolas,monospace; }
		.aviso { background:#fef3c7; border-left:3px solid #f59e0b; padding:10px 12px; margin:0 0 20px;
		         font-size:13px; line-height:1.5; }
		label { display:block; font-size:13px; font-weight:600; margin-bottom:6px; }
		input { width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:6px; font-size:14px;
		        box-sizing:border-box; }
		input:focus { outline:none; border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,.15); }
		button { width:100%; margin-top:16px; padding:10px; background:#2563eb; color:#fff; border:0;
		         border-radius:6px; font-size:14px; font-weight:600; cursor:pointer; }
		button:hover { background:#1d4ed8; }
		.error { background:#fee2e2; color:#991b1b; padding:10px 12px; border-radius:6px;
		         font-size:13px; margin:0 0 16px; }
		a { display:block; margin-top:16px; text-align:center; font-size:13px; color:#64748b; }
	</style>
</head>
<body>
	<div class="caja">
		<h1>Clave requerida</h1>
		<p class="script"><?php echo $script; ?></p>

		<?php if ($error != '') { ?>
			<p class="error"><?php echo $error; ?></p>
		<?php } ?>

		<?php if (!$bloqueado) { ?>
			<p class="aviso">Este script hace cambios masivos en la base. Ingresá la clave de mantenimiento para continuar.</p>
			<form method="post" action="<?php echo $action; ?>" autocomplete="off">
				<label for="guard_clave">Clave de mantenimiento</label>
				<input type="password" name="guard_clave" id="guard_clave" autofocus required>
				<button type="submit">Continuar</button>
			</form>
		<?php } ?>

		<a href="../asignacion/index_.php">Volver</a>
	</div>
</body>
</html>
		<?php
	}

?>
