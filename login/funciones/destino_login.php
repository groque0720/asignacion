<?php

// Devuelve la URL de destino post-login según el usuario (dashboard / idperfil).
//
// IMPORTANTE: mantener sincronizado con el bloque de redirección de
// login/validarusuario.php — es la misma tabla idperfil -> URL. Si se agrega
// un perfil nuevo allá, agregarlo también acá (lo usa cambiar_clave.php al
// terminar el cambio de clave forzado).

function destino_login($campo) {

	// Usuarios que van al dashboard contable, sin importar el perfil.
	$usuariosDashboard = [
		11, 13, 14, 15, 16, 28, 31, 36, 37, 41,
		45, 47, 51, 56, 57, 59, 65, 66, 68, 71,
		72, 79, 82, 83, 87, 89, 91, 93, 94, 96, 101, 102,
		104, 106, 111, 116, 117, 119, 120, 121, 124, 125,
		132, 135, 136, 138, 139, 144, 146, 147, 163,
	];

	if (in_array((int)$campo['idusuario'], $usuariosDashboard, true) || $campo['id_negocio'] == 2) {
		return "../dashboard/index.php";
	}

	$map = [
		1  => "../ventas/_admin/admin.php",
		2  => "../ventas/web/administracion.php",
		3  => "../ventas/web/notificaciones_panel.php",
		7  => "../gestoria/index.php",
		8  => "../ventas/web/pagos_clientes.php",
		9  => "../ventas/web/control_pagos_clientes.php",
		10 => "../ventas/web/control_pagos_clientes.php",
		11 => "../ventas/web/creditos.php",
		12 => "../ventas/web/remesas.php",
		13 => "../ventas/_admin/precios_admin.php",
		14 => "../ventas/web/control_reservas.php",
		15 => "../ventas/web/reportes.php",
		17 => "../ventas/web/control_pagos_clientes.php",
		18 => "../ventas/web/noticias.php",
		19 => "../ventas/web/recursos_dyv_toyota.php",
		20 => "../ventas/web/recepcion.php",
		22 => "../asignacion",
		24 => "../asignacion",
		5  => "../asignacion",
	];

	$p = (int)$campo['idperfil'];
	return isset($map[$p]) ? $map[$p] : "../asignacion";
}
