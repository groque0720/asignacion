<?php

	// Clave de acceso a los scripts sensibles de mantenimiento (ver guard_clave.php).
	// Se guarda como hash bcrypt, nunca en texto plano.
	//
	// Para cambiar la clave, generar el hash nuevo y reemplazar la constante:
	//   php -r "echo password_hash('LA-CLAVE-NUEVA', PASSWORD_DEFAULT);"

	define('GUARD_CLAVE_HASH', '$2y$10$CsCWDAzShHRbcANmLcww1.gDUAK3Q.3oXdO1f8C1iuHGCbOzJiRd2');

	// Minutos que dura la clave validada dentro de la misma sesión.
	define('GUARD_MINUTOS', 15);

	// Intentos fallidos permitidos antes de bloquear la sesión.
	define('GUARD_MAX_INTENTOS', 5);

?>
