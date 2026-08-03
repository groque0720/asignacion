<?php

	// Clave de acceso a los scripts sensibles de mantenimiento (ver guard_clave.php).
	// Se guarda como hash bcrypt, nunca en texto plano.
	//
	// Para cambiar la clave: poner la clave nueva DENTRO de un archivo temporal
	// fuera del repo y correrlo, en vez de pasarla como argumento en la consola.
	// Si va en la línea de comandos queda registrada en el historial de la shell
	// (así se filtró la clave anterior). En ese .php descartable alcanza con:
	//   echo password_hash('LA-CLAVE-NUEVA', PASSWORD_DEFAULT);
	// Pegar el resultado acá abajo y borrar el archivo temporal.

	// El prefijo puede ser $2y$ (lo que genera password_hash) o $2a$ (lo que
	// generan los generadores online). password_verify() acepta los dos; sólo
	// password_get_info() no reconoce el $2a$, y acá no se usa.

	define('GUARD_CLAVE_HASH', '$2a$12$3jrAAgqo5IpTfxpSMe2EpuT2UKC96TbYImaAn/pdoVkZj51pjoclq');

	// Minutos que dura la clave validada dentro de la misma sesión.
	define('GUARD_MINUTOS', 15);

	// Intentos fallidos permitidos antes de bloquear la sesión.
	define('GUARD_MAX_INTENTOS', 5);

	// Minutos que dura el bloqueo tras agotar los intentos.
	define('GUARD_BLOQUEO_MINUTOS', 15);

?>
