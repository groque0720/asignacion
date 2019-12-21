
<?php

	include_once("funciones/func_mysql.php");
	conectar();
	mysqli_query($con,"SET NAMES 'utf8'");
	extract($_POST);

	$SQL="SELECT * FROM usuarios WHERE idperfil=3 AND activo = 1 AND id_negocio=1 AND idsucursal =".$id_sucursal;
	$asesores = mysqli_query($con, $SQL); ?>

	<option value="0">Todos</option>
 	<?php while ($asesor = mysqli_fetch_array($asesores)) { ?>
 		<option value="<?php echo $asesor['idusuario']; ?>"><?php echo $asesor['nombre']; ?></option>
 	<?php } ?>
?>


