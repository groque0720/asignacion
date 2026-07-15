<?php

@session_start();

//COMPRUEBA QUE EL USUARIO ESTA AUTENTIFICADO
if ($_SESSION["autentificado"] != "SI") {
	// Al login real. Antes iba a ventas/index.php, que tenía su propio login roto
	// desde PHP 7 (validar.php llamaba mysql_real_escape_string, ya removida).
	// Ruta desde la raíz: este archivo lo incluyen páginas a distintas
	// profundidades y una relativa resolvería distinto en cada una.
	header("Location: /login/");
	//ademas salgo de este script
	exit();
}	
?>