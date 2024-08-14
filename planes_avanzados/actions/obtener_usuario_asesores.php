<?php 
    $SQL="SELECT idusuario, nombre FROM usuarios WHERE activo = 1 AND idperfil = 3 and idusuario > 1 ORDER BY nombre";
	$usuarios=mysqli_query($con, $SQL);
?>