<?php
/**
 * Guard de las pantallas de administración de USUARIOS (sólo perfil admin = 1).
 *
 * `ventas/includes/security.php` sólo comprueba que haya sesión iniciada, sin
 * mirar el perfil. Con eso solo, cualquier usuario logueado podía POSTear a
 * usuario_edit.php y cambiarle la clave o el perfil a cualquier otro, incluido
 * el admin — lo que dejaba sin sentido tener las claves hasheadas.
 *
 * Va en las 5 pantallas de usuarios (usuarios/usuario/usuario_agregar y los dos
 * endpoints de escritura), NO en el resto de _admin: el perfil 13 entra acá para
 * administrar precios y tiene que seguir pudiendo.
 */

@session_start();

if ($_SESSION["autentificado"] != "SI") {
	header("Location: ../../login/");
	exit();
}

// Logueado pero sin perfil admin: no lo mando al login (ya tiene sesión), le
// aviso y corto.
if ((int) $_SESSION["idperfil"] !== 1) {
	header("HTTP/1.1 403 Forbidden");
	header("Content-Type: text/html; charset=utf-8");
	echo '<p style="font-family:Arial,sans-serif;padding:24px;">'
	   . 'No tenés permiso para administrar usuarios. Si necesitás un cambio, pedíselo a un administrador.'
	   . '</p>';
	exit();
}
?>
