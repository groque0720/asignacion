<?php
/**
 * Política y manejo de `usuarios.clave` — única fuente de verdad.
 *
 * Lo incluyen el login, la pantalla de cambio de clave y el alta/edición de
 * usuarios del admin. Sólo define funciones (no abre conexión ni sesión), así
 * puede incluirse desde cualquier módulo, moderno o legacy:
 *
 *     require_once(__DIR__ . "/../comun/clave.php");      // desde login/
 *     require_once(__DIR__ . "/../../comun/clave.php");   // desde ventas/_admin/
 */

/** ¿La clave cumple la política? Deja el motivo del rechazo en $err. */
function clave_valida($c, &$err) {
	if (strlen($c) < 8)                    { $err = 'Debe tener al menos 8 caracteres.';                     return false; }
	if (!preg_match('/[A-Z]/', $c))        { $err = 'Debe incluir al menos una MAYÚSCULA.';                  return false; }
	if (!preg_match('/[a-z]/', $c))        { $err = 'Debe incluir al menos una minúscula.';                  return false; }
	if (!preg_match('/[^A-Za-z0-9]/', $c)) { $err = 'Debe incluir al menos un símbolo (! @ # $ % ...).';     return false; }
	return true;
}

/** Hash con el que se guarda toda clave nueva. */
function clave_hash($c) {
	return password_hash($c, PASSWORD_DEFAULT);
}

/** ¿Lo guardado ya es un hash bcrypt, o todavía es texto plano? */
function clave_es_hash($guardada) {
	return substr($guardada, 0, 4) === '$2y$';
}

/**
 * Verifica la clave tecleada contra lo guardado en la base.
 *
 * Acepta las dos formas mientras dure la transición: si la guardada todavía
 * está en texto plano compara como venía haciéndolo MySQL (sin distinguir
 * mayúsculas, ignorando espacios finales), para no dejar afuera a nadie que
 * hoy sí puede entrar. Quien llama debe re-hashear cuando `clave_es_hash()`
 * da false.
 */
function clave_verificar($tecleada, $guardada) {
	if (clave_es_hash($guardada)) {
		return password_verify($tecleada, $guardada);
	}
	return strcasecmp(rtrim($guardada), rtrim($tecleada)) === 0;
}
