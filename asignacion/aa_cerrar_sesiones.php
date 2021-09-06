<?php
	if ($_SESSION["idperfil"] != 14 or $_SESSION["id"] == 14) {
		echo '<script>	window.location.href = "../asignacion/index_.php";</script>';
	}
?>