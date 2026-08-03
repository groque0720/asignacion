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

	define('GUARD_CLAVE_HASH', '$2y$10$KM4J9ofStw40CCEBeXGwCO0joDcmsRDP5Bvu5NN58w9ZxlATQR0B6');

	// Minutos que dura la clave validada dentro de la misma sesión.
	define('GUARD_MINUTOS', 15);

	// Intentos fallidos permitidos antes de bloquear la sesión.
	define('GUARD_MAX_INTENTOS', 5);

	// Minutos que dura el bloqueo tras agotar los intentos.
	define('GUARD_BLOQUEO_MINUTOS', 15);

?>
