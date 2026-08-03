<?php

	// Clave de acceso a los scripts sensibles de mantenimiento (ver guard_clave.php).
	// Se guarda como hash bcrypt, nunca en texto plano.
	//
	// Para cambiar la clave, generar el hash nuevo y reemplazar la constante:
	//   php -r "echo password_hash('LA-CLAVE-NUEVA', PASSWORD_DEFAULT);"

	define('GUARD_CLAVE_HASH', '$2a$12$ygRqZpgeClYTp24K2DQU2eOOHeMxJwaLwv5lpsLpT8ChvlCFtD1AO');

	// Minutos que dura la clave validada dentro de la misma sesión.
	define('GUARD_MINUTOS', 15);

	// Intentos fallidos permitidos antes de bloquear la sesión.
	define('GUARD_MAX_INTENTOS', 5);

?>
